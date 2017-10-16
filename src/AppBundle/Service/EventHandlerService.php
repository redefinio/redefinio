<?php

namespace AppBundle\Service;

use AppBundle\Entity\BlockData;
use AppBundle\Entity\CvData;
use AppBundle\Entity\EventSource;
use AppBundle\Entity\TemplatType;
use AppBundle\Event\Event;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * EventHandlerService
 *
 * @Service for manipulating event sources.
 */
class EventHandlerService
{
    protected $em;

    protected $container;

    protected $user;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
    }

    public function applyEvents($events) {
        foreach($events as $event) {
            $this->applyEvent($event);
        }
    }

    public function applyEvent(Event $event) {
        $eventSource = $this->storeEvent($event);
        $block = null;

        switch (get_class($event)) {
            case 'AppBundle\Event\CreateDataEvent':
                $block = $this->applyCreateEvent($eventSource);
                break;
            case 'AppBundle\Event\UpdateDataEvent':
                $block = $this->applyUpdateEvent($eventSource);
                break;
            case 'AppBundle\Event\SortBlockEvent':
                $block = $this->applySortEvent($eventSource);
                break;
            case 'AppBundle\Event\CreateBlockEvent':
                $block = $this->applyCreateBlockEvent($eventSource);
                break;
        }

        return $block;
    }

    private function storeEvent(Event $event) {
        $cv = $this->container->get(CvService::class)->getUserCv($this->user);
        $eventSource = new EventSource();

        $eventSource->setCv($cv);
        if (method_exists($event, 'getType')) {
            $eventSource->setType($event->getType());
        }
        $eventSource->setTemplate($event->getParentTemplate());
        $eventSource->setObject($event);

        $this->em->persist($eventSource);
        $this->em->flush();

        return $eventSource;
    }

    private function applyCreateEvent(EventSource $eventSource) {
        if (is_null($eventSource->getObject()->getData())) {
            return;
        }

        $blockTemplate = $this->em->getRepository("AppBundle:BlockTemplate")->findOneById($eventSource->getObject()->getTemplateId());
        $block = $this->em->getRepository("AppBundle:BlockData")->findOneBy(
            array(
                'cv' => $eventSource->getCv(),
                'blockTemplate' => $blockTemplate
            ));

        if (is_null($block) || $eventSource->getType() != TemplatType::TYPE_FIXED) {
            $block = new BlockData();

            $wildcard = ($eventSource->getObject()->getSlotWildcard()) ? $eventSource->getObject()->getSlotWildcard(): $blockTemplate->getSlot();
            $slot = $this->em->getRepository('AppBundle:TemplateSlot')->findOneByWildcard($wildcard);

            $block->setTemplateSlot($slot);
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

    private function applyUpdateEvent($eventSource)
    {
        $event = $eventSource->getObject();
        $block = $this->em->getRepository('AppBundle:BlockData')->findOneById($event->getBlockId());

        $notExist = true;

        foreach($block->getCvDatas() as $data) {
            if ($data->getField() == $event->getField()) {
                $data->setData($event->getData());
                $notExist = false;
            }
        }

        if ($notExist) {
            $newData = new CvData();

            $newData->setCv($block->getCv());
            $newData->setField($event->getField());
            $newData->setData($event->getData());
            $newData->setType($eventSource->getType());

            $block->addCvData($newData);
        }


        $this->em->persist($block);
        $this->em->flush();
    }

    private function applySortEvent($eventSource)
    {
        $event = $eventSource->getObject();

        $slot = $this->em->getRepository('AppBundle:TemplateSlot')->findOneByWildcard($event->getWildcard());
        $block = $this->em->getRepository('AppBundle:BlockData')->findOneById($event->getBlockId());

        $block->setTemplateSlot($slot);
        $block->setPosition($event->getPosition());

        $this->em->persist($block);
        $this->em->flush();

    }

    private function applyCreateBlockEvent($eventSource) {
        $event = $eventSource->getObject();

        $data = new CvData();

        $data->setCv($eventSource->getCv());
        $data->setType($event->getBlockType());
        $data->setData($event->getFormData());

        $this->em->persist($data);
        $this->em->flush();

        $templates = $this->em->getRepository('AppBundle:BlockTemplate')->getUsedTemplates($event->getBlockType());

        foreach ($templates as $template) {
            if ($template->getTemplate()->getId() != $event->getParentTemplate()->getId()) {
                $wildcard = $template->getSlot()->getWildcard();
            } else {
                $wildcard = $event->getWildcard();
            }

            $slot = $this->em->getRepository('AppBundle:TemplateSlot')->findOneByWildcard($wildcard);

            $block = new BlockData();

            $block->setTemplateSlot($slot);
            $block->setCv($eventSource->getCv());
            $block->setBlockTemplate($template);

            $block->addCvData($data);

            $this->em->persist($block);
            $this->em->flush();

            if ($template->getTemplate()->getId() == $event->getParentTemplate()->getId()) {
                $response = $block;
            }
        }


        return $response;

    }
}