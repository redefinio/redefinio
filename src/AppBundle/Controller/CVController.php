<?php

namespace AppBundle\Controller;


use AppBundle\Service\CVRenderService;
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

        $parameters = array('cV' => $userCv);

        if (is_null($userCv)) {
            $template = 'cv/create.html.twig';
            $em = $this->getDoctrine()->getManager();
            $parameters['templates'] = $em->getRepository('AppBundle:Template')->findAll();
        }

        return $this->render($template, $parameters);
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
            $cv = $service->initializeCv($this->getUser(), $templateId);
        }

        return $this->redirect($this->generateUrl("cv_edit"));
    }

    /**
     * Displays a form to edit an existing CV entity.
     *
     * @Route("/edit", name="cv_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request)
    {
        $cV = $this->get(CvService::class)->getUserCv($this->getUser());
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
            'current_template_id' => $cV->getTemplate()->getId(),
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
        $cvRenderService = $this->get(CVRenderService::class);
        $repository = $this->getDoctrine()->getRepository('AppBundle:Template');

        $template = $repository->findOneById($id);
        $cv = $this->get(CvService::class)->getUserCv($this->getUser());
        $cv->setTemplate($template);

        $em->persist($cv);
        $em->flush();

        foreach($template->getTemplateSlots() as $slot) {
            $dataBlocks = $em->getRepository('AppBundle:BlockData')->findBy(array('template_slot' => $slot, 'cv' => $cv));
            if (count($dataBlocks) == 0) {
                $this->mapDataToSlotTemplates($slot, $cv);
                $em->refresh($template);
            }
        }


        if (!$cv) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);

            return $response;
        }

        return new Response($cvRenderService->getTemplateHtml($template, $cv));
    }

    /**
     * Generte pdf
     *
     * @Route("/pdf", name="cv_pdf")
     * @param Request $request
     * @return Response
     */
    public function renderPdfAction(Request $request) {
        $cvRenderService = $this->get(CVRenderService::class);
        $cvService = $this->get(CvService::class);

        $cv = $cvService->getUserCv($this->getUser());
        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($cvRenderService->getTemplateHtml($cv->getPublicTemplate(), $cv)),
                '200',
                array(
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'cv.pdf'
                )
        );
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
