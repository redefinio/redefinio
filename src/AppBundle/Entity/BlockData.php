<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BlockData
 *
 * @ORM\Table(name="block_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BlockDataRepository")
 */
class BlockData
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
     * @ORM\ManyToOne(targetEntity="CV", inversedBy="block_datas")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id")
     */
    private $cv;

    /**
     * @ORM\ManyToOne(targetEntity="TemplateSlot", inversedBy="block_datas")
     * @ORM\JoinColumn(name="template_slot_id", referencedColumnName="id")
     */
    private $template_slot;

    /**
     * @ORM\ManyToOne(targetEntity="Block", inversedBy="block_datas")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     */
    private $block;

    /**
     * @ORM\OneToMany(targetEntity="BlockData", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="BlockData", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    private $parent;

    /**
     * @var data JSON array of used variable names and their values.
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    public function __construct() {
        $this->children = new ArrayCollection();
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
     * Set created_at
     *
     * @param \DateTime $created_at
     *
     * @return BlockData
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set cv
     *
     * @param \AppBundle\Entity\CV $cv
     *
     * @return BlockData
     */
    public function setCv(\AppBundle\Entity\CV $cv = null)
    {
        $this->cv = $cv;

        return $this;
    }

    /**
     * Get cv
     *
     * @return \AppBundle\Entity\CV
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * Set templateSlot
     *
     * @param \AppBundle\Entity\TemplateSlot $templateSlot
     *
     * @return BlockData
     */
    public function setTemplateSlot(\AppBundle\Entity\TemplateSlot $templateSlot = null)
    {
        $this->template_slot = $templateSlot;

        return $this;
    }

    /**
     * Get templateSlot
     *
     * @return \AppBundle\Entity\TemplateSlot
     */
    public function getTemplateSlot()
    {
        return $this->template_slot;
    }

    /**
     * Set block
     *
     * @param \AppBundle\Entity\Block $block
     *
     * @return BlockData
     */
    public function setBlock(\AppBundle\Entity\Block $block = null)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return \AppBundle\Entity\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return BlockData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\BlockData $child
     *
     * @return BlockData
     */
    public function addChild(\AppBundle\Entity\BlockData $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\BlockData $child
     */
    public function removeChild(\AppBundle\Entity\BlockData $child)
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
     * @param \AppBundle\Entity\BlockData $parent
     *
     * @return BlockData
     */
    public function setParent(\AppBundle\Entity\BlockData $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\BlockData
     */
    public function getParent()
    {
        return $this->parent;
    }
}
