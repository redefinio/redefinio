<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BlockData
 *
 * @ORM\Table(name="cv_data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CvDataRepository")
 */
class CvData {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="field", type="text", nullable=true)
     */
    private $field;

    /**
     * @ORM\ManyToOne(targetEntity="CV", inversedBy="cv_datas")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id")
     */
    private $cv;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="BlockData",  cascade={"persist"}, mappedBy="cv_datas")
     */
    private $block_datas;

    /**
     * @ORM\OneToMany(targetEntity="CvData", mappedBy="parent", cascade={"persist"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="CvData", inversedBy="children")
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
        $this->block_datas = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param mixed $cv
     */
    public function setCv($cv)
    {
        $this->cv = $cv;
    }

    /**
     * @return mixed
     */
    public function getBlockDatas()
    {
        return $this->block_datas;
    }

    /**
     * @param mixed $block_datas
     * @return CvData
     */
    public function setBlockDatas($block_datas)
    {
        $this->block_datas = $block_datas;

        return $this;
    }

    /**
     * @param mixed $block
     * @return ArrayCollection
     */
    public function addBlock($block)
    {
        $this->block_datas[] = $block;

        return $this;
    }

    /**
     * @return data
     */
    public function getData()
    {
        return json_decode($this->data, true);
    }

    /**
     * @param data $data
     * @return CvData
     */
    public function setData($data)
    {
        $this->data = json_encode($data);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     */
    public function setField($field)
    {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\CvData $child
     *
     * @return BlockData
     */
    public function addChild(\AppBundle\Entity\CvData $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\CvData $child
     */
    public function removeChild(\AppBundle\Entity\CvData $child)
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
     * @param \AppBundle\Entity\CvData $parent
     *
     * @return BlockData
     */
    public function setParent(\AppBundle\Entity\CvData $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\CvData
     */
    public function getParent()
    {
        return $this->parent;
    }



    /**
     * Add blockData
     *
     * @param \AppBundle\Entity\BlockData $blockData
     *
     * @return CvData
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
}
