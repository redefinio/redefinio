<?php

namespace AppBundle\Controller;

use Doctrine\ORM\ORMException;
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
            $result = $em->getRepository('AppBundle:Block')->getHTML($template_id, $block_type);
            // only 1 block should be returned
            $result = $result[0];
            $id = $result['id'];
            $html = $result['html_source'];
        } catch(ORMException $e) {
            return new Response(json_encode(array('error' => $e->getMessage())), Response::HTTP_NOT_FOUND);
        }
        try {
            $result = $em->getRepository('AppBundle:Block')->getChildHTML($template_id, $id);
            
            if(count($result) != 0) {
                $result = $result[0];
                $cHtml = $result['html_source'];
                $html = str_replace('{{ blocks|raw }}', $cHtml, $html);
            }
        } catch(ORMException $e) {
        }

        return new Response(json_encode(array('data' => urlencode($html))));
    }

}
