<?php

namespace AppBundle\Controller;

use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\BlockData;

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

    /**
     * @Route("/block/{cv_id}/{template_slot_id}/{data_id}", name="api_block_post", requirements={"cv_id": "\d+", "template_slot_id": "\d+", "data_id": "\d+"})
     * @Method({"POST"})
     */
    public function blockAction($cv_id, $template_slot_id, $data_id) {
        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:CV')->find($cv_id); 
        if (!$cv) return new Response(json_encode(array('error' => 'CV not found')), Response::HTTP_NOT_FOUND);
        $slot = $em->getRepository('AppBundle:TemplateSlot')->find($template_slot_id); 
        if (!$slot) return new Response(json_encode(array('error' => 'TemplateSlot not found')), Response::HTTP_NOT_FOUND);
        if ($cv != $slot->getCV()) return new Response(json_encode(array('error' => 'CV and TemplateSlot do not match')), Response::HTTP_NOT_FOUND);

        if ($data_id === 0) {
            $data = new BlockData();
            $data->setCV($cv);
            $data->setTemplateSlot($slot);
        } else {
            $data = $em->getRepository('AppBundle:BlockData')->find($data_id); 
            $data->setTemplateSlot($slot);
        }
        if (!$data) return new Response(json_encode(array('error' => 'BlockData not found')), Response::HTTP_NOT_FOUND);
        
        // koki bloka gauname
        // validuoti gautus duomenis
        // issaugoti pakeitimus
        $em->persist($block);
        $em->flush();

        return new Response();
    }

}
