<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ChatAdmin;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{
    public function onBeginLoad()
    {
        parent::onBeginLoad();
        $this->setName("Chat based administration");
        $this->setDescription("Adds chat based admin commands for you to use");
        $this->setGroups(array('Core', 'Chat'));
    }
}
