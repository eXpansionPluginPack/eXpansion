<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Gui\Structures;

/**
 * Description of subItemData
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class ContextMenuData
{
    /**
     * @var \ManiaLivePlugins\eXpansion\Core\I18n\Message
     */
    public $message;

    /**
     * @var mixed
     */
    public $data;

    /**
     *
     * @var string
     */
    public $dataId;

    public function __construct(\ManiaLivePlugins\eXpansion\Core\I18n\Message $message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setDataId($id)
    {
        $this->dataId = $id;
    }

    public function getDataId()
    {
        return $this->dataId;
    }
}
