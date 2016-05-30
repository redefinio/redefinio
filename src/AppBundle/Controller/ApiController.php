<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/block/{template_id}/{block_type}", name="api_block_html")
     */
    public function blockHtmlAction($template_id, $block_type) {
        $em = $this->getDoctrine()->getManager();
        try {
            $html = $em->getRepository('AppBundle:Block')->getHTML($template_id, $block_type);
        } catch(\Exception $e) {
            return new Response(json_encode(array('error' => 'Not found')), Response::HTTP_NOT_FOUND);    
        }
        return new Response(json_encode(array('data' => urlencode($html))));
    }

}
