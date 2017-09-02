<?php

namespace AppBundle\Repository;

/**
 * BlockTemplateRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BlockTemplateRepository extends \Doctrine\ORM\EntityRepository
{
	public function getHtml($template_id, $block_type) {
		return $this->getEntityManager()->createQueryBuilder('b')
			->select('b.id, b.html_source')
			->from('AppBundle\Entity\Block', 'b')
    		->where('b.type = :type')
    		->andWhere('b.template = :template')
		    ->setParameter('type', $block_type)
		    ->setParameter('template', $template_id)
		    ->setMaxResults(1)
		    ->getQuery()->getScalarResult();
	}

	public function getChildHtml($template_id, $parent_id) {
		return $this->getEntityManager()->createQueryBuilder('b')
			->select('b.id, b.html_source')
			->from('AppBundle\Entity\Block', 'b')
    		->where('b.parent = :parent')
    		->andWhere('b.template = :template')
		    ->setParameter('parent', $parent_id)
		    ->setParameter('template', $template_id)
		    ->setMaxResults(1)
		    ->getQuery()->getScalarResult();
	}


	public function getUsedTemplates($type) {
	    $templates = $this->findByType($type);

	    return array_filter($templates, function ($template) {
	        return count($template->getBlockDatas()) > 0;
        });
    }
}
