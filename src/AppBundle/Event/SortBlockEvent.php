<?php
/**
 * Created by PhpStorm.
 * User: svidleo
 * Date: 02/09/2017
 * Time: 22:31
 */

namespace AppBundle\Event;


class SortBlockEvent implements Event
{
    private $wildcard;

    private $position;

    private $blockId;

    private $cvId;

    private $parentTemplate;

    /**
     * @return mixed
     */
    public function getWildcard()
    {
        return $this->wildcard;
    }

    /**
     * @param mixed $wildcard
     */
    public function setWildcard($wildcard)
    {
        $this->wildcard = $wildcard;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getBlockId()
    {
        return $this->blockId;
    }

    /**
     * @param mixed $blockId
     */
    public function setBlockId($blockId)
    {
        $this->blockId = $blockId;
    }

    /**
     * @return mixed
     */
    public function getCvId()
    {
        return $this->cvId;
    }

    /**
     * @param mixed $cvId
     */
    public function setCvId($cvId)
    {
        $this->cvId = $cvId;
    }

    /**
     * @return mixed
     */
    public function getParentTemplate()
    {
        return $this->parentTemplate;
    }

    /**
     * @param mixed $parentTemplate
     */
    public function setParentTemplate($parentTemplate)
    {
        $this->parentTemplate = $parentTemplate;
    }



}