<?php

namespace AppBundle\Controller;

use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Block;
use AppBundle\Entity\BlockData;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller
{


    /**
     * @Route("/block/{block_id}", name="api_block_delete")
     * @Method("DELETE")
     */
    public function deleteBlock($block_id) {
        $em = $this->getDoctrine()->getManager();

        $blockData = $em->getRepository('AppBundle:BlockData')->find($block_id);

        $em->remove($blockData);
        $em->flush();

        return new Response();

    }

    /**
     * @Route("/block/{template_id}/{block_type}", name="api_block_html")
     * @Method({"GET"})
     */
    public function blockHtmlAction($template_id, $block_type) {
        $em = $this->getDoctrine()->getManager();

        $block = $em->getRepository('AppBundle:Block')->createQueryBuilder('b')
            ->where('b.template = :template')
            ->andWhere('b.type = :type')
            ->setParameter('type', $block_type)
            ->setParameter('template', $template_id)
            ->getQuery()->getOneOrNullResult();

        if (!$block) {
            return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);
        }
        //$twig = $this->container->get('twig');
        $twig = new \Twig_Environment(new \Twig_Loader_Array(), array(
            'cache' => false,
        ));

        $template = $twig->createTemplate($block->getHtmlSource());

        // set default parameters for the template
        $parameters = json_decode($block->getAvailableFields(), true);
        // pass BlockData object itself to the template in order to print out its id or other needed attributes
        $parameters['block_data'] = new BlockData();
        
        $child = $em->getRepository('AppBundle:Block')->createQueryBuilder('b')
            ->where('b.template = :template')
            ->andWhere('b.parent = :block')
            ->setParameter('block', $block)
            ->setParameter('template', $template_id)
            ->getQuery()->getOneOrNullResult();

        // if data has embedded child data, generate template for each of them and include in parent template
        if ($child) {
            $childTemplate = $twig->createTemplate($child->getHtmlSource());
            $childrenString = $childTemplate->render(json_decode($child->getAvailableFields(), true));
            // if template is parent it must define 'blocks' variable where all children template will be inserted.
            $parameters['blocks'] = $childrenString;
        }

        $html = $template->render($parameters);

        return new Response(json_encode(array('data' => urlencode($html))));
    }

    /**
     * @Route("/block/{cv_id}/{template_slot_id}", name="api_block_new", requirements={"cv_id": "\d+"})
     * @Method({"POST"})
     */
    public function blockNewAction($cv_id, $template_slot_id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:CV')->find($cv_id); 
        if (!$cv) return new Response(json_encode(array('error' => 'CV not found')), Response::HTTP_NOT_FOUND);
        $slot = $em->getRepository('AppBundle:TemplateSlot')->createQueryBuilder('ts')
            ->where('ts.wildcard = :wildcard')
            ->andWhere('ts.template = :template')
            ->setParameter('wildcard', $template_slot_id)
            ->setParameter('template', $cv->getTemplate())
            ->getQuery()->getOneOrNullResult(); 
        if (!$slot) return new Response(json_encode(array('error' => 'TemplateSlot not found')), Response::HTTP_NOT_FOUND);
        if ($cv->getTemplate() != $slot->getTemplate()) return new Response(json_encode(array('error' => 'CV and TemplateSlot do not match')), Response::HTTP_NOT_FOUND);

        $data = new BlockData();
        $data->setCV($cv);
        $data->setTemplateSlot($slot);
        // get the block from available in template by block type
        $block_type = $request->get('blockType', null);
        $block = $em->getRepository('AppBundle:Block')->createQueryBuilder('b')
            ->where('b.type = :type')
            ->andWhere('b.template = :template')
            ->setParameter('type', $block_type)
            ->setParameter('template', $cv->getTemplate())
            ->getQuery()->getOneOrNullResult();

        if (!$block) return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);
        $data->setBlock($block);
        
        // validuoti gautus duomenis
        $formData = $request->get('fields', array());
        if (isset($formData['blocks']) && (
            $block_type == Block::TYPE_SKILLS || 
            $block_type == Block::TYPE_EXPERIENCE || 
            $block_type == Block::TYPE_EDUCATION || 
            $block_type == Block::TYPE_CERTIFICATES
        )) {
            $block_child = $em->getRepository('AppBundle:Block')->createQueryBuilder('b')
                ->where('b.parent = :parent')
                ->andWhere('b.template = :template')
                ->setParameter('parent', $block)
                ->setParameter('template', $cv->getTemplate())
                ->getQuery()->getOneOrNullResult();
            if (!$block_child) return new Response(json_encode(array('error' => 'Block child not found for parent - '.$block->getId())), Response::HTTP_NOT_FOUND);
            foreach($formData['blocks'] as $inner_data) {
                $data_child = new BlockData();
                $data_child->setCv($cv);
                $data_child->setParent($data);
                $data_child->setBlock($block_child);
                $data_child->setData(json_encode($inner_data));
                $em->persist($data_child);
            }
        }
        $data->setData(json_encode($formData));
        $em->persist($data);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/block/{cv_id}/{template_slot_id}/{data_id}", name="api_block_update", requirements={"cv_id": "\d+", "data_id": "\d+"})
     * @Method({"PUT"})
     */
    public function blockUpdateAction($cv_id, $template_slot_id, $data_id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $cv = $em->getRepository('AppBundle:CV')->find($cv_id); 
        if (!$cv) return new Response(json_encode(array('error' => 'CV not found')), Response::HTTP_NOT_FOUND);

        $data = $em->getRepository('AppBundle:BlockData')->find($data_id); 
        if (!$data) return new Response(json_encode(array('error' => 'BlockData not found')), Response::HTTP_NOT_FOUND);

        $slot = $em->getRepository('AppBundle:TemplateSlot')->createQueryBuilder('ts')
            ->where('ts.wildcard = :wildcard')
            ->andWhere('ts.template = :template')
            ->setParameter('wildcard', $template_slot_id)
            ->setParameter('template', $cv->getTemplate())
            ->getQuery()->getOneOrNullResult(); 
        // Fixed blocks do not need template slot provided because they are always in the same position and can not be moved anywhere
        $block_type = $data->getBlock()->getType();
        if ($block_type != Block::TYPE_FIXED && !$slot) return new Response(json_encode(array('error' => 'TemplateSlot not found')), Response::HTTP_NOT_FOUND);
        if ($slot && $cv->getTemplate() != $slot->getTemplate()) return new Response(json_encode(array('error' => 'CV and TemplateSlot do not match')), Response::HTTP_NOT_FOUND);
        if ($slot) {
            $data->setTemplateSlot($slot);
        }
        $block = $data->getBlock();
        if (!$block) return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);
        
        // validuoti gautus duomenis
        $formData = $request->get('fields', array());
        if (isset($formData['blocks']) && (
            $block_type == Block::TYPE_SKILLS || 
            $block_type == Block::TYPE_EXPERIENCE || 
            $block_type == Block::TYPE_EDUCATION || 
            $block_type == Block::TYPE_CERTIFICATES
        )) {
            $block_child = $em->getRepository('AppBundle:Block')->createQueryBuilder('b')
                ->where('b.parent = :parent')
                ->andWhere('b.template = :template')
                ->setParameter('parent', $block)
                ->setParameter('template', $cv->getTemplate())
                ->getQuery()->getOneOrNullResult();
            if (!$block_child) return new Response(json_encode(array('error' => 'Block child not found for parent - '.$block->getId())), Response::HTTP_NOT_FOUND);
            // we are doing hard reset of all child records, so delete old ones
            $em->createQueryBuilder()
                ->delete('AppBundle:BlockData', 'b')
                ->where('b.parent = :parent')
                ->setParameter(':parent', $data)
                ->getQuery()->execute();
            foreach($formData['blocks'] as $inner_data) {
                $data_child = new BlockData();
                $data_child->setCv($cv);
                $data_child->setParent($data);
                $data_child->setBlock($block_child);
                $data_child->setData(json_encode($inner_data));
                $em->persist($data_child);
            }
        }
        $data->setData(json_encode($formData));
        $em->persist($data);
        $em->flush();

        return new Response();
    }

}
