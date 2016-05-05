<?php

namespace AppBundle\Service;

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
				$template = $this->twig->createTemplate($data->getBlock()->getHtmlSource());
				$parameters = json_decode($data->getData(), true);
				// if data has embedded child data, generate template for each of them and include in parent template
				if (count($data->getChildren()) > 0) {
					$childrenString = '';
					foreach ($data->getChildren() as $child) {
						$childTemplate = $this->twig->createTemplate($child->getBlock()->getHtmlSource());
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
}
