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

    const RENDER_TYPE_PUBLIC = 0;
    const RENDER_TYPE_EDIT = 1;
    const RENDER_TYPE_PDF = 2;

	protected $twig;
	protected $twigLocal;
    private $em;
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->twig = $this->container->get('twig');
    }

	public function getTemplateHtml($relations, $type = CVRenderService::RENDER_TYPE_EDIT) {
        $template = $relations->getTemplate();
        $theme = $relations->getTheme();
        $cv = $relations->getCv();

		$templatePath = $this->getTemplatePath($template, $type);
		// all slots just replace the twig blocks in base template from Template.templatePath
		$templateString = '{% extends \'templates/'.$templatePath.'.html.twig\' %}';
		// each TemaplteSlot acts as a block in parent template
		foreach($template->getTemplateSlots() as $slot) {
			$templateString .= '{% block '.$slot->getWildcard().' %}';
			// traverse through each slots blocks and fill it with data
            $dataBlocks = $this->em->getRepository('AppBundle:BlockData')->findBy(array('template_slot' => $slot, 'cv' => $cv), array('position' => 'asc'));
			foreach ($dataBlocks as $data) {

				$templateString .= $this->renderBlock($data);
			}
			$templateString .= '{% endblock %}';
		}


		$template = $this->twig->createTemplate($templateString);
		return $template->render(array('theme' => $theme));
	}


	public function renderBlock($block) {
        $template = $this->twig->createTemplate($block->getBlockTemplate()->getHtmlSource());

        $parameters = $this->getParameters($block);
        // pass BlockData object itself to the template in order to print out its id or other needed attributes
        $parameters['block_data'] = $block;
        // if data has embedded child data, generate template for each of them and include in parent template
        if (count($block->getBlockTemplate()->getChildren()) > 0) {
            $childrenString = '';
            foreach ($parameters['blocks'] as $child) {
                $childTemplate = $this->twig->createTemplate($block->getBlockTemplate()->getChildren()->first()->getHtmlSource());
                $childrenString .= $childTemplate->render($child);
            }
            // if template is parent it must define 'blocks' variable where all children template will be inserted.
            $parameters['blocks'] = $childrenString;
        }

        return $template->render($parameters);
    }

    private function getTemplatePath($template, $type) {
        $templatePath = $template->getTemplatePath();
        switch ($type) {
            case CVRenderService::RENDER_TYPE_PUBLIC:
                $templatePath .= "_public";
                break;
            case CVRenderService::RENDER_TYPE_PDF:
                $templatePath .= "_pdf";
                break;
        }

        return $templatePath;
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
