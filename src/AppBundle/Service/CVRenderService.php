<?php

namespace AppBundle\Service;

use AppBundle\Entity\BlockData;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

/**
 * CVRenderService
 *
 * @Service for rendering the CVs.
 */
class CVRenderService {

	protected $twig;
	protected $twigLocal;


    public function __construct(\Twig_Environment $twig) {
        $this->twig = $twig;
    }

	public function getTemplateHtml($cv, $template) {
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
				if (count($data->getChildren()) > 0) {
					$childrenString = '';
					foreach ($data->getChildren() as $child) {
						$childTemplate = $this->twig->createTemplate($child->getBlockTemplate()->getHtmlSource());
						$childrenString .= $childTemplate->render($this->getParameters($child));
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


	private function getParameters(BlockData $data) {
        return $this->decodeCollection($data->getCvDatas());
    }

    private function decodeCollection(PersistentCollection $data) {
        $parameters = [];
        foreach($data as $parameter) {
            $parameters = array_merge($parameters, $this->decodeString($parameter->getData()));
        }

        return $parameters;
    }

    private function decodeString($json) {
        return json_decode($json, true);
    }
}
