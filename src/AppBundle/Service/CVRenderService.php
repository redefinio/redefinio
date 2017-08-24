<?php

namespace AppBundle\Service;

use AppBundle\Entity\BlockData;
use AppBundle\Entity\TemplatType;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CVRenderService
 *
 * @Service for rendering the CVs.
 */
class CVRenderService {

	protected $twig;
	protected $twigLocal;


    private $em;
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->twig = $this->container->get('twig');
    }

	public function getTemplateHtml($template) {
		// all slots just replace the twig blocks in base template from Template.templatePath
		$templateString = '{% extends \'templates/'.$template->getTemplatePath().'.html.twig\' %}';
		// each TemaplteSlot acts as a block in parent template
		foreach($template->getTemplateSlots() as $slot) {
			$templateString .= '{% block '.$slot->getWildcard().' %}';
			// traverse through each slots blocks and fill it with data
			foreach ($slot->getBlockDatas() as $data) {
                $template = $this->twig->createTemplate($data->getBlockTemplate()->getHtmlSource());

                $parameters = $this->getParameters($data);
				// pass BlockData object itself to the template in order to print out its id or other needed attributes
				$parameters['block_data'] = $data;
				// if data has embedded child data, generate template for each of them and include in parent template
				if (count($data->getBlockTemplate()->getChildren()) > 0) {
					$childrenString = '';
					foreach ($parameters['blocks'] as $child) {
						$childTemplate = $this->twig->createTemplate($data->getBlockTemplate()->getChildren()->first()->getHtmlSource());
						$childrenString .= $childTemplate->render($child);
					}
					// if template is parent it must define 'blocks' variable where all children template will be inserted.
					$parameters['blocks'] = $childrenString;
				}
				$templateString .= ($template->render($parameters));
			}
			$templateString .= '{% endblock %}';
		}

		$template = $this->twig->createTemplate($templateString);
		return $template->render(array());
	}

	public function generateTemplate($data, $template) {
        $templateString = '{% extends \'templates/'.$template->getTemplatePath().'.html.twig\' %}';

        $fixed = array_filter($data, function ($item) {
            return  $item->getType() == TemplatType::TYPE_FIXED;
        });

        $blocks = array_filter($data, function ($item) {
            return $item->getType() != TemplatType::TYPE_FIXED;
        });

        foreach ($template->getTemplateSlots() as $slot) {
            $templates = $this->em->getRepository("AppBundle:BlockTemplate")->findByTemplate($template);
        }
    }


	private function getParameters(BlockData $data) {

        return $this->decodeCollection($data->getCvDatas());
    }

    private function decodeCollection(PersistentCollection $data) {
        $parameters = [];
        foreach($data as $parameter) {
            $parameters = array_merge($parameters, $parameter->getData());
        }

        return $parameters;
    }

    private function decodeString($json) {
        return json_decode($json, true);
    }
}
