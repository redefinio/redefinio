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
     * @ORM\ManyToOne(targetEntity="BlockTemplate", inversedBy="block_datas")
     * @ORM\JoinColumn(name="block_template_id", referencedColumnName="id")
     */
    private $blockTemplate;

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
     * @ORM\ManyToMany(targetEntity="CvData", inversedBy="block_datas", fetch="EAGER")
     */
    private $cv_datas;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->cv_datas = new ArrayCollection();
        $this->position = 1;
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
     * @param BlockTemplate|null $blockTemplate
     * @return BlockData
     * @internal param BlockTemplate $block
     *
     */
    public function setBlockTemplate(\AppBundle\Entity\BlockTemplate $blockTemplate = null)
    {
        $this->blockTemplate = $blockTemplate;

        return $this;
    }

    /**
     * Get block
     *
     * @return \AppBundle\Entity\BlockTemplate
     */
    public function getBlockTemplate()
    {
        return $this->blockTemplate;
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

        return $this;
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
     * @param \AppBundle\Entity\CvData $cvData
     * @return BlockData
     */
    public function addCvData(\AppBundle\Entity\CvData $cvData) {
        $this->cv_datas[] = $cvData;

        return $this;
    }

    /**
     * @param \AppBundle\Entity\CvData $cvData
     *
     */
    public function removeCvData(\AppBundle\Entity\CvData $cvData) {
        $this->cv_datas->removeElement($cvData);
    }

    /**
     * Get cv data
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCvDatas() {
        return $this->cv_datas;
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

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BlockData
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
