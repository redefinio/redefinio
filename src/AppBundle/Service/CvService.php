<?php

namespace AppBundle\Service;

use AppBundle\Entity\TemplatType;
use AppBundle\Entity\User;
use AppBundle\Entity\CV;
use AppBundle\Event\CreateDataEvent;
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
        $cv->setTheme($theme);
        $cv->setUrl("some_url");

        $this->em->persist($cv);
        $this->em->flush();

        $this->initializeData($cv, $template);
    }

    private function initializeData($cv, $template) {
        $templates = $this->em->getRepository('AppBundle:BlockTemplate')->findByTemplate($template);

        foreach($templates as $block) {
            $event = $this->createDataEvent($cv, $block);

            if (is_array($event)) {
                $this->eventHandler->applyEvents($event);
            } else {

                $this->eventHandler->applyEvent($event);
            }
        }
    }


    private function initializeFixed($event, $template) {
        $fields = json_decode($template->getAvailableFields());

        $events = array();

        foreach($fields as $key=>$field) {
            $separateEvent = clone($event);
            $separateEvent->setData(array($key => $field));
            $separateEvent->setField($field);

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

    private function createDataEvent($cv, $template) {
        $event = new CreateDataEvent();
        $event->setCvId($cv);
        $event->setType($template->getType());
        $event->setTemplateId($template->getId());

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
                $event = $this->initializeFixed($event, $template);
                break;
        }


        return $event;
    }
}