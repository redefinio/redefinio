<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\CV;
use AppBundle\Form\CVType;

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
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cVs = $em->getRepository('AppBundle:CV')->findAll();

        return $this->render('cv/index.html.twig', array(
            'cVs' => $cVs,
        ));
    }

    /**
     * Creates a new CV entity.
     *
     * @Route("/new", name="cv_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $cV = new CV();        
        $form = $this->createForm('AppBundle\Form\CVType', $cV);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cV);
            $em->flush();

            return $this->redirectToRoute('cv_show', array('id' => $cV->getId()));
        }

        return $this->render('cv/new.html.twig', array(
            'cV' => $cV,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a CV entity.
     *
     * @Route("/{id}", name="cv_show")
     * @Method("GET")
     */
    public function showAction(CV $cV)
    {
        $deleteForm = $this->createDeleteForm($cV);

        return $this->render('cv/show.html.twig', array(
            'cV' => $cV,
            'delete_form' => $deleteForm->createView(),
        ));
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

        return $this->render('cv/edit.html.twig', array(
            'cV' => $cV,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
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
