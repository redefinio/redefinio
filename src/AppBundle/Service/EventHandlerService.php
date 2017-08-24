<?php

namespace AppBundle\Service;

use AppBundle\Entity\BlockData;
use AppBundle\Entity\CvData;
use AppBundle\Entity\EventSource;
use AppBundle\Event\Event;
use AppBundle\Entity\Data;
use Doctrine\ORM\EntityManager;

/**
 * EventHandlerService
 *
 * @Service for manipulating event sources.
 */
class EventHandlerService
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function applyEvents($events) {
        foreach($events as $event) {
            $this->applyEvent($event);
        }
    }

    public function applyEvent(Event $event) {
        $eventSource = $this->storeEvent($event);

        if (is_null($event->getData())) {
            return;
        }

        switch (get_class($event)) {
            case 'AppBundle\Event\CreateDataEvent':
                $this->applyCreateEvent($eventSource);
                break;
        }
    }

    private function storeEvent(Event $event) {
        $cv = $this->em->getRepository('AppBundle:CV')->findOneById($event->getCvId());

        $eventSource = new EventSource();

        $eventSource->setCv($cv);
        $eventSource->setType($event->getType());
        $eventSource->setObject($event);

        $this->em->persist($eventSource);
        $this->em->flush();

        return $eventSource;
    }

    private function applyCreateEvent(EventSource $eventSource) {
        $blockTemplate = $this->em->getRepository("AppBundle:BlockTemplate")->findOneById($eventSource->getObject()->getTemplateId());
        $block = $this->em->getRepository("AppBundle:BlockData")->findOneBy(
            array(
                'cv' => $eventSource->getCv(),
                'blockTemplate' => $blockTemplate
            ));

        if (is_null($block)) {
            $block = new BlockData();

            $block->setTemplateSlot($blockTemplate->getSlot());
            $block->setCv($eventSource->getCv());
            $block->setBlockTemplate($blockTemplate);
        }

        $data = new CvData();

        $data->setType($eventSource->getObject()->getType());
        $data->setCv($eventSource->getCv());
        $data->setField($eventSource->getObject()->getField());
        $data->setData($eventSource->getObject()->getData());
        $data->setType($eventSource->getObject()->getType());

        $block->addCvData($data);

        $this->em->persist($block);
        $this->em->flush();
    }
}