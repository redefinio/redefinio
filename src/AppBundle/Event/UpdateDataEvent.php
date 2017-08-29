<?php
/**
 * Created by PhpStorm.
 * User: svidleo
 * Date: 28/08/2017
 * Time: 21:59
 */

namespace AppBundle\Event;


class UpdateDataEvent extends DataEvent
{

    private $blockId;

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



}