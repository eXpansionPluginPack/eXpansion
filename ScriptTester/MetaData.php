<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ScriptTester;

use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

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
        $this->setName(" ManiaScript editor");
        $this->setDescription("Provides script editor /adm editor");
        $this->setGroups(array('Tools'));

        $config = Config::getInstance();

        $var = new TypeString("tester_maniascript", "ManiaScript", $config, false, false);
        $var->setVisible(false);
        $var->setValue("");
        $this->registerVariable($var);

        $var = new TypeString("tester_manialink", "ManiaLink", $config, false, false);
        $var->setVisible(false);
        $var->setValue("");
        $this->registerVariable($var);
    }
}
