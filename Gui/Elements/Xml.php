<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use Exception;
use ManiaLivePlugins\eXpansion\Helpers\Singletons;

/**
 * Description of Xml
 *
 * @author Petri
 */
class Xml extends \ManiaLive\Gui\Elements\Xml
{

    private $errorLogin = false;

    public function setErrorLogin($login)
    {
        $this->errorLogin = $login;
    }

    public function save()
    {
        try {
            parent::save();
        } catch (Exception $ex) {
            if ($this->errorLogin) {
                $connection = Singletons::getInstance()->getDediConnection();
                $connection->chatSendServerMessage('$f00XML Error from server:');
                $connection->chatSendServerMessage($ex->getMessage(), $this->errorLogin);
            }
            \ManiaLive\Utilities\Console::println("Xml error");
            \ManiaLive\Utilities\Console::println($ex->getMessage());
            return;
        }
    }

}
