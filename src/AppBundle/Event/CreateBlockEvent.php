<?php
/**
 * Created by PhpStorm.
 * User: svidleo
 * Date: 02/09/2017
 * Time: 22:27
 */

namespace AppBundle\Event;


class CreateBlockEvent implements Event
{

    private $wildcard;

    private $blockType;

    private $formData;

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
    public function getBlockType()
    {
        return $this->blockType;
    }

    /**
     * @param mixed $blockType
     */
    public function setBlockType($blockType)
    {
        $this->blockType = $blockType;
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @param mixed $formData
     */
    public function setFormData($formData)
    {
        $this->formData = $formData;
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