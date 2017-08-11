<?php

namespace AppBundle\Service;

use Doctrine\ORM\PersistentCollection;
use \Twig_Loader_Array;
use \Twig_Loader_Chain;

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

	public function getTemplateHtml($cv) {
		// all slots just replace the twig blocks in base template from Template.templatePath
		$templateString = '{% extends \'templates/'.$cv->getTemplate()->getTemplatePath().'.html.twig\' %}';
		// each TemaplteSlot acts as a block in parent template
		foreach($cv->getTemplate()->getTemplateSlots() as $slot) {
			$templateString .= '{% block '.$slot->getWildcard().' %}';
			// traverse through each slots blocks and fill it with data
			foreach ($slot->getBlockDatas() as $data) {
                $template = $this->twig->createTemplate($data->getBlockTemplate()->getHtmlSource());
                if (count($data->getCvDatas()) > 0) {
                    $parameters = $this->decodeData($data->getCvDatas());
                } else {
                    $parameters = json_decode($data->getData(), true);
                }
				// pass BlockData object itself to the template in order to print out its id or other needed attributes
				$parameters['block_data'] = $data;
				// if data has embedded child data, generate template for each of them and include in parent template
				if (count($data->getChildren()) > 0) {
					$childrenString = '';
					foreach ($data->getChildren() as $child) {
						$childTemplate = $this->twig->createTemplate($child->getBlockTemplate()->getHtmlSource());
						$childrenString .= $childTemplate->render(json_decode($child->getData(), true));
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


	private function decodeData(PersistentCollection $data) {
        $parameters = [];
        foreach($data as $parameter) {
            $parameters = array_merge($parameters, json_decode($parameter->getData(), true));
        }

        return $parameters;
    }
}
