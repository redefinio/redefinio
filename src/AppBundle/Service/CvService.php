<?php

namespace AppBundle\Service;

use AppBundle\Entity\BlockData;
use AppBundle\Entity\BlockTemplate;
use AppBundle\Entity\TemplatType;
use AppBundle\Entity\User;
use AppBundle\Entity\CV;
use AppBundle\Event\CreateDataEvent;
use AppBundle\Event\SortBlockEvent;
use AppBundle\Event\UpdateDataEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CVService
 *
 * @Service for manipulating cv's.
 */
class CvService {

    private $em;
    private $container;
    private $eventHandler;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->eventHandler = $this->container->get(EventHandlerService::class);
    }

    public function getUserCv(User $user) {
        $userCV = $this->em->getRepository('AppBundle:CV')->findOneByUser($user);

        return $userCV;
    }

    public function initializeCv(User $user, int $templateId) {
        $template = $this->em->getRepository('AppBundle:Template')->findOneById($templateId);
        $theme = $this->em->getRepository('AppBundle:Theme')->findOneById(1);

        $cv = new CV();
        $cv->setUser($user);
        $cv->setTemplate($template);
        $cv->setPublicTemplate($template);
        $cv->setTheme($theme);
        $cv->setUrl($this->generateUserHash($user));

        $this->em->persist($cv);
        $this->em->flush();

        $this->initializeData($cv, $template);
    }

    public function createNewBlock($cv, $wildcard, $templateId, $blockType, $formData) {
        $template = $this->em->getRepository('AppBundle:BlockTemplate')->findOneBy(array('template' => $templateId, "type" => $blockType));

        $event = $this->createDataEvent($cv, $template, $wildcard);
        $event->setData($formData);

        $this->eventHandler->applyEvent($event);
    }

    public function updateBlock($blockId, $formData, $wildcard) {
        $block = $this->em->getRepository('AppBundle:BlockData')->findOneById($blockId);

        $event = $this->updateDataEvent($block, $formData, $wildcard);

        $this->apply($event);
    }

    public function distributeBlocks($wildcard, $templateId, $cv, $positions) {
        $template = $this->em->getRepository('AppBundle:Template')->findOneById($templateId);

        foreach($positions as $key=>$position) {
            $event = new SortBlockEvent();
            $event->setWildcard($wildcard);
            $event->setPosition($key);
            $event->setBlockId($position);
            $event->setCvId($cv->getId());
            $event->setParentTemplate($template);

            $this->eventHandler->applyEvent($event);
        }

    }

    public function publishCv($templateId, $user) {
        $renderService = $this->container->get(CVRenderService::class);

        $template = $this->em->getRepository('AppBundle:Template')->findOneById($templateId);
        $cv = $this->getUserCv($user);

        $html = $renderService->getTemplateHtml($template, $cv);

        $cv->setPublicTemplate($template);
        $cv->setPublicHtml($html);

        $this->em->persist($cv);
        $this->em->flush();
    }

    public function getPublicLinkHtml($user, $identifier) {
        $cv = $this->em->getRepository('AppBundle:CV')->findOneBy(array('url' => $identifier, 'user' => $user));

        return $cv->getPublicHtml();
    }

    public function getPublicHtml($user) {
       $cv = $this->getUserCv($user);

       return $cv->getPublicHtml();
    }

    private function initializeData($cv, $template) {
        $templates = $this->em->getRepository('AppBundle:BlockTemplate')->findByTemplate($template);

        foreach($templates as $block) {
            if (!is_null($block->getSlot())) {

                $wildcard = $block->getSlot()->getWildcard();
            } else {
                $wildcard = null;
            }

            $event = $this->createDataEvent($cv, $block, $wildcard);

            $this->apply($event);
        }
    }

    private function mapFixed($event, $data) {
        $events = array();

        foreach($data as $key=>$field) {
            $separateEvent = clone($event);
            $separateEvent->setData(array($key => $field));
            $separateEvent->setField($key);

            $events[] = $separateEvent;
        }

        return $events;
    }

    private function initializeBlock($event, $block)
    {
        $fields = json_decode($block->getAvailableFields());

        $data = array();
        foreach($fields as $key=>$field) {
            $data[$key] = $field;
        }

        if (count($block->getChildren()) > 0) {
            foreach($block->getChildren() as $child) {
                $childFields = json_decode($child->getAvailableFields());
                $childData = array();

                foreach ($childFields as $key=>$childField) {
                    $childData[$key] = $childField;
                }

                $data['blocks'][] = $childData;
            }
        }

        $event->setData($data);

        return $event;
    }

    private function updateBlockData($event, $data) {
        $event->setData($data);

        return $event;
    }

    private function createDataEvent($cv, $template, $wildcard) {
        $event = new CreateDataEvent();
        $event->setCvId($cv);
        $event->setType($template->getType());
        $event->setTemplateId($template->getId());
        $event->setParentTemplate($template->getTemplate());
        $event->setSlotWildcard($wildcard);

        //TODO: Refactor with immutable
        switch ($template->getType()) {
            case TemplatType::TYPE_SKILLS:
            case TemplatType::TYPE_EXPERIENCE:
            case TemplatType::TYPE_CERTIFICATES:
            case TemplatType::TYPE_EDUCATION:
            case TemplatType::TYPE_TEXT:
                $event = $this->initializeBlock($event, $template);
                break;
            case TemplatType::TYPE_FIXED:
                $event = $this->mapFixed($event, json_decode($template->getAvailableFields()));
                break;
        }


        return $event;
    }

    private function updateDataEvent($block, $formData, $wildcard) {
        $event = new UpdateDataEvent();
        $event->setBlockId($block->getId());
        $event->setCvId($block->getCv());
        $event->setType($block->getBlockTemplate()->getType());
        $event->setTemplateId($block->getBlockTemplate()->getId());
        $event->setParentTemplate($block->getBlockTemplate()->getTemplate());
        $event->setSlotWildcard($wildcard);
        $event->setData($formData);

        //TODO: Refactor with immutable
        switch ($block->getBlockTemplate()->getType()) {
            case TemplatType::TYPE_SKILLS:
            case TemplatType::TYPE_EXPERIENCE:
            case TemplatType::TYPE_CERTIFICATES:
            case TemplatType::TYPE_EDUCATION:
            case TemplatType::TYPE_TEXT:
                $event = $this->updateBlockData($event, $formData);
                break;
            case TemplatType::TYPE_FIXED:
                $event = $this->mapFixed($event, $formData);
                break;
        }

        return $event;
    }

    //TODO Refactor with event sourcing
    public function mapDataToSlotTemplates($slot, $cv) {
        $data = $this->em->getRepository('AppBundle:CvData')->findByCv($cv);
        $templateRepository = $this->em->getRepository('AppBundle:BlockTemplate');

        foreach($data as $entity) {
            $template = $templateRepository->findOneBy(array('type' => $entity->getType(), 'template' => $slot->getTemplate()));
            switch ($entity->getType()) {
                case BlockTemplate::TYPE_SKILLS:
                case BlockTemplate::TYPE_EXPERIENCE:
                case BlockTemplate::TYPE_CERTIFICATES:
                case BlockTemplate::TYPE_EDUCATION:
                    $block = $this->mapText($template, $entity);
                    break;
                case BlockTemplate::TYPE_TEXT:
                    $block = $this->mapText($template, $entity);
                    break;
                case BlockTemplate::TYPE_FIXED:
                    $block = $this->mapFixedData($template, $entity);
                    break;
            }
            if (!is_null($block)) {
                $this->em->persist($block);
                $this->em->flush();
            }
        }

        $this->em->refresh($slot);
    }

    private function mapFixedData($template, $entity) {
        $fields = json_decode($template->getAvailableFields(), true);
        if (array_key_exists($entity->getField(), $fields)) {

            $blocks = $this->em->getRepository('AppBundle:BlockData')->findBy(array('blockTemplate' => $template, 'cv' => $entity->getCv()));
            $block = $this->filterTemplateBlock($blocks, $template, $entity->getCv());
            $block->addCvData($entity);

            return $block;
        }

    }

    private function filterTemplateBlock($blocks, $template, $cv) {
        foreach ($blocks as $block) {
            if ($block->getBlockTemplate() == $template) {
                return $block;
            }
        }

        $block = new BlockData();
        $block->setCv($cv);
        $block->setBlockTemplate($template);
        $block->setTemplateSlot($template->getSlot());

        return $block;

    }

    private function mapText($template, $entity) {
        $block = new BlockData();
        $block->setCv($entity->getCv());
        $block->setBlockTemplate($template);
        $block->addCvData($entity);
        $block->setTemplateSlot($template->getSlot());

        return $block;
    }

    /**
     * @param $event
     */
    private function apply($event) {
        if (is_array($event)) {
            $this->eventHandler->applyEvents($event);
        } else {
            $this->eventHandler->applyEvent($event);
        }
    }

    /**
     * @param User $user
     * @return bool|string
     */
    private function generateUserHash(User $user)
    {
        $seed = 'JvKnrQWPsThuJteNQAuH' . $user->getId() . $user->getUsername();
        $hash = sha1(uniqid($seed . mt_rand(), true));

        # To get a shorter version of the hash, just use substr
        $hash = substr($hash, 0, 10);
        return $hash;
    }

}