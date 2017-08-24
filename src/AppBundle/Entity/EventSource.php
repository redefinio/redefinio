<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventSource
 *
 * @ORM\Table(name="event_source")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventSourceRepository")
 */
class EventSource
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
     * @var int
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="CV")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id")
     */
    private $cv;

    /**
     * @var serialized object
     *
     * @ORM\Column(name="object", type="text")
     */
    private $object;

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
     * @return serialized
     */
    public function getObject()
    {
        return unserialize($this->object);
    }

    /**
     * @param $object
     */
    public function setObject($object)
    {
        $this->object = serialize($object);
    }


}
