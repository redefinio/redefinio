<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig', array(
        ));
    }

    /**
     * @Route("/cv", name="cv")
     */
    public function cvAction()
    {
    	$repository = $this->getDoctrine()->getRepository('AppBundle:CV');
    	$cv = $repository->createQueryBuilder('p')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    	$cvRenderService = $this->get('cv_render');

        return new Response($cvRenderService->getTemplateHtml($cv));
    }
}
