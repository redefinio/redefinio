<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TemplateSlot
 *
 * @ORM\Table(name="template_slot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TemplateSlotRepository")
 */
class TemplateSlot
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="template_slots")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

    /**
     * @ORM\ManyToMany(targetEntity="Block", mappedBy="template_slots")
     */
    private $blocks;

    /**
     * @ORM\OneToMany(targetEntity="BlockData", mappedBy="template_slot")
     */
    private $block_datas;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string Wildcard to be replaced in the Template.
     *
     * @ORM\Column(name="wildcard", type="string", length=255)
     */
    private $wildcard;

    public function __construct() {
        $this->block_datas = new ArrayCollection();
        $this->blocks = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return TemplateSlot
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set template
     *
     * @param \AppBundle\Entity\Template $template
     *
     * @return TemplateSlot
     */
    public function setTemplate(\AppBundle\Entity\Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \AppBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Add block
     *
     * @param \AppBundle\Entity\Block $block
     *
     * @return TemplateSlot
     */
    public function addBlock(\AppBundle\Entity\Block $block)
    {
        $this->blocks[] = $block;

        return $this;
    }

    /**
     * Remove block
     *
     * @param \AppBundle\Entity\Block $block
     */
    public function removeBlock(\AppBundle\Entity\Block $block)
    {
        $this->blocks->removeElement($block);
    }

    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Add blockData
     *
     * @param \AppBundle\Entity\BlockData $blockData
     *
     * @return TemplateSlot
     */
    public function addBlockData(\AppBundle\Entity\BlockData $blockData)
    {
        $this->block_datas[] = $blockData;

        return $this;
    }

    /**
     * Remove blockData
     *
     * @param \AppBundle\Entity\BlockData $blockData
     */
    public function removeBlockData(\AppBundle\Entity\BlockData $blockData)
    {
        $this->block_datas->removeElement($blockData);
    }

    /**
     * Get blockDatas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlockDatas()
    {
        return $this->block_datas;
    }

    /**
     * Set wildcard
     *
     * @param string $wildcard
     *
     * @return TemplateSlot
     */
    public function setWildcard($wildcard)
    {
        $this->wildcard = $wildcard;

        return $this;
    }

    /**
     * Get wildcard
     *
     * @return string
     */
    public function getWildcard()
    {
        return $this->wildcard;
    }
}
