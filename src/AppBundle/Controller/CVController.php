<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BlockData;
use AppBundle\Entity\BlockTemplate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\CV;
use AppBundle\Service\CvService;
use Symfony\Component\HttpFoundation\Response;

/**
 * CV controller.
 *
 * @Route("/cv")
 */
class CVController extends Controller
{

    /**
     * Lists all CV entities.
     *
     * @Route("/", name="cv_index")
     * @Method("GET")
     * @return Response
     */
    public function indexAction()
    {
        $template = 'cv/show.html.twig';

        $userCv = $this->get(CvService::class)->getUserCv($this->getUser());

        $parametes = array('cV' => $userCv);

        if (is_null($userCv)) {
            $template = 'cv/create.html.twig';
            $em = $this->getDoctrine()->getManager();
            $parametes['templates'] = $em->getRepository('AppBundle:Template')->findAll();
        }

        return $this->render($template, $parametes);
    }

    /**
     * Creates a new CV entity.
     *
     * @Route("/new/{templateId}", name="cv_new")
     * @Method({"GET",})
     * @param Request $request
     * @param int $templateId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function newAction(Request $request, int $templateId)
    {
        $service = $this->get(CvService::class);
        if (is_null($service->getUserCv($this->getUser()))) {
            $service->initializeCv($this->getUser(), $templateId);
        }

        return new Response($templateId);
    }

    /**
     * Displays a form to edit an existing CV entity.
     *
     * @Route("/{id}/edit", name="cv_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, CV $cV)
    {
        $deleteForm = $this->createDeleteForm($cV);
        $editForm = $this->createForm('AppBundle\Form\CVType', $cV);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cV);
            $em->flush();

            return $this->redirectToRoute('cv_edit', array('id' => $cV->getId()));
        }
        $em = $this->getDoctrine()->getManager();
        $templates = $em->getRepository('AppBundle:Template')->findAll();

        return $this->render('cv/edit.html.twig', array(
            'cv' => $cV,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'templates' => $templates
        ));
    }

    /**
     * Deletes a CV entity.
     *
     * @Route("/{id}", name="cv_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, CV $cV)
    {
        $form = $this->createDeleteForm($cV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($cV);
            $em->flush();
        }

        return $this->redirectToRoute('cv_index');
    }

    /**
     * @Route("/{id}/template", name="cv_render_template")
     * @Method("GET")
     */
    public function renderTemplateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('AppBundle:Template');
        $template = $repository->findOneById($id);
        $cv = $this->get(CvService::class)->getUserCv($this->getUser());

        foreach($template->getTemplateSlots() as $slot) {
            if (count($slot->getBlockDatas()) == 0) {
                $this->mapDataToSlotTemplates($slot, $cv);
                $em->refresh($template);
            }
        }


        if (!$cv) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            return $response;
        }

        $cvRenderService = $this->get('cv_render');
        return new Response($cvRenderService->getTemplateHtml($repository->findOneById($id)));
    }

    private function mapDataToSlotTemplates($slot, $cv) {
        $em = $this->getDoctrine()->getManager();

        $data = $this->getDoctrine()->getRepository('AppBundle:CvData')->findByCv($cv);
        $templateRepository = $this->getDoctrine()->getRepository('AppBundle:BlockTemplate');

        foreach($data as $entity) {
            $template = $templateRepository->findOneBy(array('type' => $entity->getType(), 'template' => $slot->getTemplate()));
            switch ($entity->getType()) {
                case BlockTemplate::TYPE_SKILLS:
                case BlockTemplate::TYPE_EXPERIENCE:
                case BlockTemplate::TYPE_CERTIFICATES:
                case BlockTemplate::TYPE_EDUCATION:
                    $block = $this->mapBlock($template, $entity, $slot);
                    break;
                case BlockTemplate::TYPE_TEXT:
                    $block = $this->mapText($template, $entity);
                    break;
                case BlockTemplate::TYPE_FIXED:
                    $block = $this->mapFixedData($template, $entity);
                    break;
            }
            if (!is_null($block)) {
                $em->persist($block);
                $em->flush();
            }
        }

        $em->refresh($slot);


    }

    private function mapFixedData($template, $entity) {
        $em = $this->getDoctrine()->getManager();

        $fields = json_decode($template->getAvailableFields(), true);
        if (in_array($entity->getField(), $fields)) {

            $blocks = $this->getDoctrine()->getRepository('AppBundle:BlockData')->findBy(array('blockTemplate' => $template, 'cv' => $entity->getCv()));
            $block = $this->filterTemplateBlock($blocks, $template, $entity->getCv());
            $block->addCvData($entity);

            return $block;
        }

    }

    private function filterTemplateBlock($blocks, $template, $cv) {
        foreach ($blocks as $block) {
            if ($block->getBlockTemplate() == $template) {
                return $block;
            }
        }

        $block = new BlockData();
        $block->setCv($cv);
        $block->setBlockTemplate($template);
        $block->setTemplateSlot($template->getSlot());

        return $block;

    }

    private function mapText($template, $entity) {
        $block = new BlockData();
        $block->setCv($entity->getCv());
        $block->setBlockTemplate($template);
        $block->addCvData($entity);
        $block->setTemplateSlot($template->getSlot());

        return $block;
    }

    private function mapBlock($template, $entity, $slot) {
        $parent = new BlockData();
        $parent->setCv($entity->getCv());
        $parent->setBlockTemplate($template);
        $parent->addCvData($entity);
        $parent->setTemplateSlot($template->getSlot());

        $templateRepository = $this->getDoctrine()->getRepository('AppBundle:BlockTemplate');
        $children = $entity->getChildren();
        $childTemplate = $templateRepository->findOneBy(array('type' => $children->first()->getType(), 'template' => $template->getTemplate()));
        foreach ($children as $child) {
            $childBlock = new BlockData();
            $childBlock->setParent($parent);
            $childBlock->setCv($entity->getCv());
            $childBlock->setBlockTemplate($childTemplate);
            $childBlock->addCvData($child);

            $parent->addChild($childBlock);
        }

        return $parent;
    }

    /**
     * Creates a form to delete a CV entity.
     *
     * @param CV $cV The CV entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(CV $cV)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cv_delete', array('id' => $cV->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
