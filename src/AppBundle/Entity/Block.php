<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Block
 *
 * @ORM\Table(name="block")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlockRepository")
 */
class Block
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
     * @ORM\OneToMany(targetEntity="Block", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="BlockData", mappedBy="block")
     */
    private $block_datas;

    /**
     * @ORM\ManyToMany(targetEntity="TemplateSlot", inversedBy="blocks")
     * @ORM\JoinTable(name="templateslots_blocks")
     */
    private $template_slots;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var html_source HTML source of the block with variables to be replaced with real values.
     *
     * @ORM\Column(name="html_source", type="text")
     */
    private $html_source;

    /**
     * @var available_fields JSON array of used variable names in the html source of the Block.
     *
     * @ORM\Column(name="available_fields", type="text")
     */
    private $available_fields;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->block_datas = new ArrayCollection();
        $this->template_slots = new ArrayCollection();
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
     * @return Block
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
     * Set type
     *
     * @param integer $type
     *
     * @return Block
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add blockData
     *
     * @param \AppBundle\Entity\BlockData $blockData
     *
     * @return Block
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
     * Add templateSlot
     *
     * @param \AppBundle\Entity\TemplateSlot $templateSlot
     *
     * @return Block
     */
    public function addTemplateSlot(\AppBundle\Entity\TemplateSlot $templateSlot)
    {
        $this->template_slots[] = $templateSlot;

        return $this;
    }

    /**
     * Remove templateSlot
     *
     * @param \AppBundle\Entity\TemplateSlot $templateSlot
     */
    public function removeTemplateSlot(\AppBundle\Entity\TemplateSlot $templateSlot)
    {
        $this->template_slots->removeElement($templateSlot);
    }

    /**
     * Get templateSlots
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemplateSlots()
    {
        return $this->template_slots;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Block $child
     *
     * @return Block
     */
    public function addChild(\AppBundle\Entity\Block $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Block $child
     */
    public function removeChild(\AppBundle\Entity\Block $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Block $parent
     *
     * @return Block
     */
    public function setParent(\AppBundle\Entity\Block $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Block
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set htmlSource
     *
     * @param string $htmlSource
     *
     * @return Block
     */
    public function setHtmlSource($htmlSource)
    {
        $this->html_source = $htmlSource;

        return $this;
    }

    /**
     * Get htmlSource
     *
     * @return string
     */
    public function getHtmlSource()
    {
        return $this->html_source;
    }

    /**
     * Set availableFields
     *
     * @param string $availableFields
     *
     * @return Block
     */
    public function setAvailableFields($availableFields)
    {
        $this->available_fields = $availableFields;

        return $this;
    }

    /**
     * Get availableFields
     *
     * @return string
     */
    public function getAvailableFields()
    {
        return $this->available_fields;
    }
}
