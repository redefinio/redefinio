<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Block
 *
 * @ORM\Table(name="block_template")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlockTemplateRepository")
 */
class BlockTemplate
{
    const TYPE_FIXED = 0;
    const TYPE_TEXT = 1;
    const TYPE_SKILLS = 2;
    const TYPE_SKILLS_INNER = 3;
    const TYPE_EXPERIENCE = 4;
    const TYPE_EXPERIENCE_INNER = 5;
    const TYPE_EDUCATION = 6;
    const TYPE_EDUCATION_INNER = 7;
    const TYPE_CERTIFICATES = 8;
    const TYPE_CERTIFICATES_INNER = 9;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="BlockTemplate", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="BlockTemplate", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="blockTemplates")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="BlockData", mappedBy="blockTemplate")
     */
    private $block_datas;

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

    /**
     * @ORM\ManyToOne(targetEntity="TemplateSlot")
     * @ORM\JoinColumn(name="template_slot_id", referencedColumnName="id")
     */
    private $slot;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->block_datas = new ArrayCollection();
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
     * @return BlockTemplate
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
     * @return BlockTemplate
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
     * @return BlockTemplate
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
     * Add child
     *
     * @param \AppBundle\Entity\BlockTemplate $child
     *
     * @return BlockTemplate
     */
    public function addChild(\AppBundle\Entity\BlockTemplate $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\BlockTemplate $child
     */
    public function removeChild(\AppBundle\Entity\BlockTemplate $child)
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
     * @param \AppBundle\Entity\BlockTemplate $parent
     *
     * @return Block
     */
    public function setParent(\AppBundle\Entity\BlockTemplate $parent = null)
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

    /**
     * Set template
     *
     * @param \AppBundle\Entity\Template $template
     *
     * @return Block
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
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }

    /**
     * @param mixed $slot
     */
    public function setSlot($slot)
    {
        $this->slot = $slot;
    }
}
