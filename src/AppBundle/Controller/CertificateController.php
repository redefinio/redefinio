<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Certificate;
use AppBundle\Form\CertificateType;

/**
 * Certificate controller.
 *
 * @Route("/certificate")
 */
class CertificateController extends Controller
{
    
    /**
     * Creates a new Certificate entity.
     *
     * @Route("/{id}/new", name="certificate_new")
     * @Method({"GET", "POST"})
     */
    public function newAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $cv = $em->getRepository('AppBundle:cv')->find($id);
        if (!$cv) {
            throw $this->createNotFoundException('Unable to find cv entity.');
        }
        $certificate = new Certificate();
        $form = $this->createForm('AppBundle\Form\CertificateType', $certificate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $certificate->setCv($cv);
            $em->persist($certificate);
            $em->flush();

            return $this->redirectToRoute('cv_show', array('id' => $id));
        }

        return $this->render('certificate/new.html.twig', array(
            'certificate' => $certificate,
            'form' => $form->createView(),
            'cv_id' => $id,
        ));
    }

    /**
     * Finds and displays a Certificate entity.
     *
     * @Route("/{id}/show", name="certificate_show")
     * @Method("GET")
     */
    public function showAction(Certificate $certificate)
    {
        $deleteForm = $this->createDeleteForm($certificate);

        return $this->render('certificate/show.html.twig', array(
            'certificate' => $certificate,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Certificate entity.
     *
     * @Route("/{id}/edit", name="certificate_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Certificate $certificate)
    {
        $deleteForm = $this->createDeleteForm($certificate);
        $editForm = $this->createForm('AppBundle\Form\CertificateType', $certificate);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($certificate);
            $em->flush();

            return $this->redirectToRoute('certificate_show', array('id' => $certificate->getId()));
        }

        return $this->render('certificate/edit.html.twig', array(
            'certificate' => $certificate,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Certificate entity.
     *
     * @Route("/{id}", name="certificate_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Certificate $certificate)
    {
        $form = $this->createDeleteForm($certificate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($certificate);
            $em->flush();
        }

        return $this->redirectToRoute('certificate_index');
    }

    /**
     * Creates a form to delete a Certificate entity.
     *
     * @param Certificate $certificate The Certificate entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Certificate $certificate)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('certificate_delete', array('id' => $certificate->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
