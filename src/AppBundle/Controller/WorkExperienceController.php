<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\WorkExperience;
use AppBundle\Form\WorkExperienceType;

/**
 * WorkExperience controller.
 *
 * @Route("/workexperience")
 */
class WorkExperienceController extends Controller
{
    

    /**
     * Creates a new WorkExperience entity.
     *
     * @Route("/{id}/new", name="workexperience_new")
     * @Method({"GET", "POST"})
     */
    public function newAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $cv = $em->getRepository('AppBundle:cv')->find($id);
        if (!$cv) {
            throw $this->createNotFoundException('Unable to find cv entity.');
        }
        $workExperience = new WorkExperience();
        $form = $this->createForm('AppBundle\Form\WorkExperienceType', $workExperience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            
            $workExperience->setCv($cv);
            $em->persist($workExperience);
            $em->flush();

            return $this->redirectToRoute('cv_show', array('id' => $id));
        }

        return $this->render('workexperience/new.html.twig', array(
            'workExperience' => $workExperience,
            'form' => $form->createView(),
            'cv_id' => $id,
        ));
    }

    /**
     * Finds and displays a WorkExperience entity.
     *
     * @Route("/{id}/show", name="workexperience_show")
     * @Method("GET")
     */
    public function showAction(WorkExperience $workExperience)
    {
        $deleteForm = $this->createDeleteForm($workExperience);

        return $this->render('workexperience/show.html.twig', array(
            'workExperience' => $workExperience,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing WorkExperience entity.
     *
     * @Route("/{id}/edit", name="workexperience_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, WorkExperience $workExperience)
    {
        $deleteForm = $this->createDeleteForm($workExperience);
        $editForm = $this->createForm('AppBundle\Form\WorkExperienceType', $workExperience);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($workExperience);
            $em->flush();

            return $this->redirectToRoute('workexperience_show', array('id' => $workExperience->getId()));
        }

        return $this->render('workexperience/edit.html.twig', array(
            'workExperience' => $workExperience,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a WorkExperience entity.
     *
     * @Route("/{id}", name="workexperience_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, WorkExperience $workExperience)
    {
        $form = $this->createDeleteForm($workExperience);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($workExperience);
            $em->flush();
        }

        return $this->redirectToRoute('workexperience_index');
    }

    /**
     * Creates a form to delete a WorkExperience entity.
     *
     * @param WorkExperience $workExperience The WorkExperience entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(WorkExperience $workExperience)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('workexperience_delete', array('id' => $workExperience->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
