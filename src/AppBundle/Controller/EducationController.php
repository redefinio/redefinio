<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Education;
use AppBundle\Form\EducationType;

/**
 * Education controller.
 *
 * @Route("/education")
 */
class EducationController extends Controller
{
    /**
     * Creates a new Education entity.
     *
     * @Route("/{id}/new", name="education_new")
     * @Method({"GET", "POST"})
     */
    public function newAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $cv = $em->getRepository('AppBundle:cv')->find($id);
        if (!$cv) {
            throw $this->createNotFoundException('Unable to find cv entity.');
        }        
        $education = new Education();
        $form = $this->createForm('AppBundle\Form\EducationType', $education);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $education->setCv($cv);
            $em->persist($education);
            $em->flush();

            return $this->redirectToRoute('cv_show', array('id' => $id));
        }

        return $this->render('education/new.html.twig', array(
            'education' => $education,
            'form' => $form->createView(),
            'cv_id' => $id,
        ));
    }

    /**
     * Finds and displays a Education entity.
     *
     * @Route("/{id}/show", name="education_show")
     * @Method("GET")
     */
    public function showAction(Education $education)
    {
        $deleteForm = $this->createDeleteForm($education);

        return $this->render('education/show.html.twig', array(
            'education' => $education,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Education entity.
     *
     * @Route("/{id}/edit", name="education_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Education $education)
    {
        $deleteForm = $this->createDeleteForm($education);
        $editForm = $this->createForm('AppBundle\Form\EducationType', $education);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($education);
            $em->flush();

            return $this->redirectToRoute('education_show', array('id' => $education->getId()));
        }

        return $this->render('education/edit.html.twig', array(
            'education' => $education,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Education entity.
     *
     * @Route("/{id}", name="education_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Education $education)
    {
        $form = $this->createDeleteForm($education);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($education);
            $em->flush();
        }

        return $this->redirectToRoute('education_index');
    }

    /**
     * Creates a form to delete a Education entity.
     *
     * @param Education $education The Education entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Education $education)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('education_delete', array('id' => $education->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
