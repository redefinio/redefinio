<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlockTemplate;
use AppBundle\Entity\CvData;
use AppBundle\Service\CvService;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Block;
use AppBundle\Entity\BlockData;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{

    /**
     * @Route("/block/{block_id}", name="api_block_delete")
     * @Method("DELETE")
     */
    public function deleteBlock($block_id) {
        $em = $this->getDoctrine()->getManager();

        $blockData = $em->getRepository('AppBundle:BlockData')->find($block_id);

        if ($this->isUserOwnBlock($blockData)) {
            $em->remove($blockData);
            $em->flush();
        }

        return new Response();

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

        $cv = $service->getUserCv($this->getUser());
        $templateId = $request->get('templateId', null);
        $blockType = $request->get('blockType', null);
        $formData = $request->get('fields', array());

        $service->createNewBlock($cv, $wildcard, $templateId, $blockType, $formData);

        return new Response();
    }

    /**
     * @Route("/block/{wildcard}", name="api_block_update")
     * @Method({"PUT"})
     */
    public function blockUpdateAction($wildcard, Request $request) {
        $service = $this->get(CvService::class);
        $em = $this->getDoctrine()->getManager();

        $cv = $service->getUserCv($this->getUser());
        if (!$cv) return new Response(json_encode(array('error' => 'CV not found')), Response::HTTP_NOT_FOUND);

        $id = $request->get('blockId', null);
        $formData = $request->get('fields', array());

        $service->updateBlock($id, $formData, $wildcard);
        
        return new Response();
    }

    private function updateText($persistedData, $formData) {
        foreach($persistedData as $data) {
            $data->setData($formData);
        }

        return $persistedData;
    }

    private function updateMixed($persistedData, $formData) {
        foreach($persistedData as $data) {
            $value = array($data->getField() => $formData[$data->getField()]);
            $data->setData($value);
        }

        return $persistedData;
    }

    private function isUserOwnBlock(BlockData $blockData) {
        $cv = $this->getDoctrine()->getManager()->getRepository('AppBundle:CV')->findOneById($blockData->getCv()->getId());

        if (is_null($cv)) {
            return false;
        }

        return $cv->getUser()->getId() === $this->getUser()->getId();
    }

}
