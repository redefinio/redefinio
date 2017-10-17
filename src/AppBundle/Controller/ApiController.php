<?php

namespace AppBundle\Controller;

use AppBundle\Service\CvService;
use AppBundle\Service\CVRenderService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Block;
use AppBundle\Entity\BlockData;
use Symfony\Bundle\MonologBundle\SwiftMailer;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{


    /**
     * Render block
     * @Route("/block/{id}", name="api_render_block")
     * @Method("GET")
     */
    public function renderBlock(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();

        $block = $em->getRepository('AppBundle:BlockData')->findOneById($id);
        if (is_null($block)) {
            $response->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
        }

        $response->setData(array(
            'html' => $this->get(CVRenderService::class)->renderBlock($block)
        ));

        return $response;
    }
    /**
     * @Route("/{id}/template", name="api_render_template")
     * @Method("GET")
     */
    public function renderTemplateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $cvRenderService = $this->get(CVRenderService::class);
        $service = $this->get(CvService::class);

        $cv = $this->get(CvService::class)->getUserCv($this->getUser());

        $relations = $this->get(CvService::class)->updateRelations($id, $cv);

        $template = $relations->getTemplate();

        foreach($template->getTemplateSlots() as $slot) {
            $dataBlocks = $em->getRepository('AppBundle:BlockData')->findBy(array('template_slot' => $slot, 'cv' => $cv));
            if (count($dataBlocks) == 0) {
                $service->mapDataToSlotTemplates($slot, $cv);
                $em->refresh($template);
            }
        }

        if (!$cv) {
            $response = new JsonResponse();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            return $response;
        }

        return new JsonResponse(array(
            'html' => $cvRenderService->getTemplateHtml($relations),
            'themes' => $this->renderView('cv/themes.html.twig', array(
                'currentTheme' => $relations->getTheme(),
                'cv' => $cv
            ))
        ));
    }

    /**
     * @Route("/photo", name="api_upload_photo")
     * @Method("POST")
     */
    public function uploadPhoto(Request $request) {

        $test = "ddd";
        $photo = $request->files->all()[0];
        $name = $this->get(CvService::class)->generateUserHash($this->getUser()).".".$photo->guessExtension();
        $target = $photo->move('upload/photos', $name)->getPathname();

        return new JsonResponse(array('photo' => "/".$target));
    }

    /**
     * @Route("/template", name="api_public_template")
     * @Method("GET")
     */
    public function getPublicHtml(Request $request) {


        $relations = $this->get(CvService::class)->getRelations($this->getUser());
        return new JsonResponse(array(
            'html' => $this->get(CVRenderService::class)->getTemplateHtml($relations)
        ));
    }

    /**
     * @Route("/block/{block_id}", name="api_block_delete")
     * @Method("DELETE")
     */
    public function deleteBlock($block_id) {
        $em = $this->getDoctrine()->getManager();

        $blocks = $em
            ->getRepository('AppBundle:BlockData')
            ->find($block_id)
            ->getCvDatas()
            ->first()
            ->getBlockDatas();


        foreach($blocks as $block) {
            if ($this->isUserOwnBlock($block)) {
                $em->remove($block);
            }
        }
        $em->flush();


        return new Response();
    }

    /**
     * @Route("/zone", name="api_zone_sort")
     * @Method({"PUT"})
     */
    public function blockSort(Request $request) {
        $service = $this->get(CvService::class);

        $wildcard = $request->get('wildcard');
        $positions = $request->get('positions');
        $templateId = $request->get('templateId');

        $service->distributeBlocks($wildcard, $templateId, $service->getUserCv($this->getUser()), $positions);


        return new Response();
    }


    /**
     * @Route("/publish", name="api_cv_publish")
     * @Method("PUT")
     */
    public function publishCv(Request $request) {
        $service = $this->get(CvService::class);

        $templateId = $request->get('templateId');

        $service->publishCv($templateId, $this->getUser());

        return new Response();
    }

    /**
     * @Route("/theme", name="api_set_theme")
     * @Method("PUT")
     */
    public function setTheme(Request $request) {
        $service = $this->get(CvService::class);

        $temapleId = $request->get("templateId");
        $themeId = $request->get("themeId");
        $cv = $service->getUserCv($this->getUser());

        $this->get(CvService::class)->updateRelations($temapleId, $cv, $themeId);

        return new JsonResponse();
    }

    /**
     * @Route("/block/{template_id}/{block_type}", name="api_block_html")
     * @Method({"GET"})
     * @param $template_id
     * @param $block_type
     * @return Response
     */
    public function blockHtmlAction($template_id, $block_type) {
            $em = $this->getDoctrine()->getManager();

        $block = $em->getRepository('AppBundle:BlockTemplate')->createQueryBuilder('b')
            ->where('b.template = :template')
            ->andWhere('b.type = :type')
            ->setParameter('type', $block_type)
            ->setParameter('template', $template_id)
            ->getQuery()->getOneOrNullResult();

        if (!$block) {
            return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);
        }

        $twig = new \Twig_Environment(new \Twig_Loader_Array(), array(
            'cache' => false,
        ));

        $template = $twig->createTemplate($block->getHtmlSource());

        // set default parameters for the template
        $parameters = json_decode($block->getAvailableFields(), true);
        // pass BlockData object itself to the template in order to print out its id or other needed attributes
        $parameters['block_data'] = new BlockData();
        
        $child = $em->getRepository('AppBundle:BlockTemplate')->createQueryBuilder('b')
            ->where('b.template = :template')
            ->andWhere('b.parent = :block')
            ->setParameter('block', $block)
            ->setParameter('template', $template_id)
            ->getQuery()->getOneOrNullResult();

        // if data has embedded child data, generate template for each of them and include in parent template
        if ($child) {
            $childTemplate = $twig->createTemplate($child->getHtmlSource());
            $childrenString = $childTemplate->render(json_decode($child->getAvailableFields(), true));
            // if template is parent it must define 'blocks' variable where all children template will be inserted.
            $parameters['blocks'] = $childrenString;
        }

        $html = $template->render($parameters);

        return new Response(json_encode(array('data' => urlencode($html))));
    }

    /**
     * @Route("/block/{wildcard}", name="api_block_new")
     * @Method({"POST"})
     */
    public function blockNewAction($wildcard, Request $request) {
        $service = $this->get(CvService::class);
        $response = new JsonResponse();

        $cv = $service->getUserCv($this->getUser());
        $templateId = $request->get('templateId', null);
        $blockType = $request->get('blockType', null);
        $formData = $request->get('fields', array());

        $block = $service->createNewBlock($cv, $wildcard, $templateId, $blockType, $formData);

        $response->setData(array(
            'html' => $this->get(CVRenderService::class)->renderBlock($block)
        ));

        return $response;
    }

    /**
     * @Route("/block/{wildcard}", name="api_block_update")
     * @Method({"PUT"})
     */
    public function blockUpdateAction($wildcard, Request $request) {
        $service = $this->get(CvService::class);
        $response = new JsonResponse();
        $cv = $service->getUserCv($this->getUser());
        if (!$cv) {
            $response->setStatusCode(JsonResponse::HTTP_NOT_FOUND);
            $response->setData(array('error' => 'CV not found'));

            return $response;
        }

        $id = $request->get('blockId', null);
        $formData = $request->get('fields', array());

        $block = $service->updateBlock($id, $formData, $wildcard);

        $response->setData(array(
            'html' => $this->get(CVRenderService::class)->renderBlock($block)
        ));
        
        return $response;
    }

    /**
     * @Route("/report", name="api_bug_report")
     * @Method({"POST"})
     */
    public function reportBug(Request $request) {
        $message = $request->get("message", null);

        $message = (new \Swift_Message('Bug report'))
            ->setFrom('info@redefinio.io')
            ->setTo(['erikas@redefin.io', 'oleg@dmarksai.com'])
            ->setBody(
                $message,
                'text'
            )
        ;

        $this->get('mailer')->send($message);

        return new JsonResponse();

    }


    private function isUserOwnBlock(BlockData $blockData) {
        $cv = $this->getDoctrine()->getManager()->getRepository('AppBundle:CV')->findOneById($blockData->getCv()->getId());

        if (is_null($cv)) {
            return false;
        }

        return $cv->getUser()->getId() === $this->getUser()->getId();
    }

}
