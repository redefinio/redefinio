<?php

namespace AppBundle\Event;


class CreateDataEvent implements Event
{
    /**
     * @var data JSON array of used variable names and their values.
     *
     */
    private $data;

    /**
     * @var int
     *
     */
    private $type;

    /**
     * @var int
     *
     */
    private $cvId;

    private $templateId;

    private $field;

    /**
     * @return data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param data $data
     */
    public function setData($data)
    {
        $this->data = $data;
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
     * @return int
     */
    public function getCvId()
    {
        return $this->cvId;
    }

    /**
     * @param int $cvId
     */
    public function setCvId($cvId)
    {
        $this->cvId = $cvId;
    }

    /**
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * @param mixed $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
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


}