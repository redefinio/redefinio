<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Data
 *
 * @ORM\Table(name="data")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DataRepository")
 */
class Data
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
     * @var string
     * @ORM\Column(name="field", type="text", nullable=true)
     */
    private $field;

    /**
     * @ORM\ManyToOne(targetEntity="CV")
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
     * @var data JSON array of used variable names and their values.
     *
     * @ORM\Column(name="data", type="text")
     */
    private $data;


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
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = $field;
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
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Data
     */
    public function getData()
    {
        return json_decode($this->data);
    }

    /**
     * @param Data $data
     */
    public function setData($data)
    {
        $this->data = json_encode($data);
    }



}
