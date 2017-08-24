<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\BlockTemplate;
use AppBundle\Entity\CvData;
use Doctrine\Common\Collections\ArrayCollection;
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

        if ($this->isUserOwnBlock($blockData)) {
            $em->remove($blockData);
            $em->flush();
        }

        return new Response();

    }

    /**
     * @Route("/block/{template_id}/{block_type}", name="api_block_html")
     * @Method({"GET"})
     * @param $template_id
     * @param $block_type
     * @return Response
     */
    public function blockHtmlAction($template_id, $block_type) {
            $em = $this->getDoctrine()->getManager();

        $block = $em->getRepository('AppBundle:BlockTemplate')->createQueryBuilder('b')
            ->where('b.template = :template')
            ->andWhere('b.type = :type')
            ->setParameter('type', $block_type)
            ->setParameter('template', $template_id)
            ->getQuery()->getOneOrNullResult();

        if (!$block) {
            return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);
        }

        $twig = new \Twig_Environment(new \Twig_Loader_Array(), array(
            'cache' => false,
        ));

        $template = $twig->createTemplate($block->getHtmlSource());

        // set default parameters for the template
        $parameters = json_decode($block->getAvailableFields(), true);
        // pass BlockData object itself to the template in order to print out its id or other needed attributes
        $parameters['block_data'] = new BlockData();
        
        $child = $em->getRepository('AppBundle:BlockTemplate')->createQueryBuilder('b')
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
        $block = $em->getRepository('AppBundle:BlockTemplate')->createQueryBuilder('b')
            ->where('b.type = :type')
            ->andWhere('b.template = :template')
            ->setParameter('type', $block_type)
            ->setParameter('template', $cv->getTemplate())
            ->getQuery()->getOneOrNullResult();

        if (!$block) return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);
        $data->setBlockTemplate($block);
        $data->addCvData($this->initData($data, $cv));

        // validuoti gautus duomenis
        $formData = $request->get('fields', array());
        switch($block_type) {
            case BlockTemplate::TYPE_FIXED:
                $this->updateMixed($data->getCvDatas(), $formData);
                break;
            case BlockTemplate::TYPE_TEXT:
                $this->updateText($data->getCvDatas(), $formData);
                break;
            case BlockTemplate::TYPE_SKILLS:
            case BlockTemplate::TYPE_EXPERIENCE:
            case BlockTemplate::TYPE_CERTIFICATES:
            case BlockTemplate::TYPE_EDUCATION:
                $this->updateCollection($data, $cv, $formData);
                break;
        }

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
        $block_type = $data->getBlockTemplate()->getType();
        if ($block_type != BlockTemplate::TYPE_FIXED && !$slot) return new Response(json_encode(array('error' => 'TemplateSlot not found')), Response::HTTP_NOT_FOUND);
        if ($slot && $cv->getTemplate() != $slot->getTemplate()) return new Response(json_encode(array('error' => 'CV and TemplateSlot do not match')), Response::HTTP_NOT_FOUND);
        if ($slot) {
            $data->setTemplateSlot($slot);
        }
        $block = $data->getBlockTemplate();
        if (!$block) return new Response(json_encode(array('error' => 'Block not found')), Response::HTTP_NOT_FOUND);

        // validuoti gautus duomenis
        $formData = $request->get('fields', array());
        switch($block_type) {
            case BlockTemplate::TYPE_FIXED:
                $this->updateMixed($data->getCvDatas(), $formData);
                break;
            case BlockTemplate::TYPE_TEXT:
                $this->updateText($data->getCvDatas(), $formData);
                break;
            case BlockTemplate::TYPE_SKILLS:
            case BlockTemplate::TYPE_EXPERIENCE:
            case BlockTemplate::TYPE_CERTIFICATES:
            case BlockTemplate::TYPE_EDUCATION:
                $this->updateText($data->getCvDatas(), $formData);
                break;
        }

        $em->persist($data);
        $em->flush();

        return new Response();
    }

    private function updateCollection($data, $cv, $formData) {
        if (!isset($formData['blocks'])) {
            return;
        }
        $em = $this->getDoctrine()->getManager();
        $blockTemplate = $data->getBlockTemplate();
        $childTemplate = $em->getRepository('AppBundle:BlockTemplate')->createQueryBuilder('b')
            ->where('b.parent = :parent')
            ->andWhere('b.template = :template')
            ->setParameter('parent', $blockTemplate)
            ->setParameter('template', $cv->getTemplate())
            ->getQuery()->getOneOrNullResult();
        if (!$childTemplate) return new Response(json_encode(array('error' => 'Block child not found for parent - '.$blockTemplate->getId())), Response::HTTP_NOT_FOUND);


        if (is_null($data->getId())) {
            foreach($this->initCollection($data, $cv, $childTemplate, $formData) as $child) {
                $data->addChild($child);
            }
            $children = $data->getChildren();
        } else {
            $children = $em->getRepository('AppBundle:BlockData')->createQueryBuilder('b')
                ->where('b.parent = :parent')
                ->setParameter('parent', $data)
                ->getQuery()->execute();
        }

        foreach($children as $key=>$child) {
            $this->updateText($child->getCvDatas(), $formData['blocks'][$key]);
        }
    }

    private function updateText($persistedData, $formData) {
        foreach($persistedData as $data) {
            $data->setData($formData);
        }

        return $persistedData;
    }

    private function updateMixed($persistedData, $formData) {
        foreach($persistedData as $data) {
            $value = array($data->getField() => $formData[$data->getField()]);
            $data->setData($value);
        }

        return $persistedData;
    }

    private function initCollection($data, $cv, $template, $formData) {
        //TODO:: Refactor with immutable
        foreach ($data->getCvDatas() as $cvData) {
            $cvData->setData(json_encode(array("blocks" => "")));
        }
        $collection = new ArrayCollection();

        foreach($formData['blocks'] as $inner_data) {
            $data_child = new BlockData();
            $data_child->setCv($cv);
            $data_child->setParent($data);
            $data_child->setBlockTemplate($template);
            $data_child->addCvData($this->initData($data_child, $cv));
            $data_child->setData(json_encode($inner_data));

            $collection->add($data_child);
        }

        return $collection;
    }


    private function initData($block, $cv) {

        $data = new CvData();
        $data->setCv($cv);
        $data->addBlock($block);

        return $data;
    }

    private function isUserOwnBlock(BlockData $blockData) {
        $cv = $this->getDoctrine()->getManager()->getRepository('AppBundle:CV')->findOneById($blockData->getCv()->getId());

        if (is_null($cv)) {
            return false;
        }

        return $cv->getUser()->getId() === $this->getUser()->getId();
    }

}
