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
     * @ORM\ManyToOne(targetEntity="CV", inversedBy="cv_datas")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id")
     */
    private $cv;

    /**
     * @ORM\ManyToMany(targetEntity="BlockData", mappedBy="cv_datas")
     */
    private $block_datas;

    /**
     * @var data JSON array of used variable names and their values.
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;

    public function __construct() {
        $this->block_datas = new ArrayCollection();
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
     * @return data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param data $data
     * @return CvData
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }




}