<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\SubTest;

/**
 * Description of SubMenu
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class SubTest extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public function exp_onReady()
    {
        $this->enableDedicatedEvents();
        $this->registerChatCommand("menu", "show", 0, true);
        Gui\Menu\Test::$plugin = $this;
    }

    public function show($login)
    {
        \ManiaLivePlugins\eXpansion\SubTest\Gui\Menu\Test::Erase($login);
        $win = \ManiaLivePlugins\eXpansion\SubTest\Gui\Menu\Test::Create($login);
        $win->show();
    }

    public function menu1($login, $data)
    {
        $this->connection->chatSendServerMessage("success menu1!");
    }

    public function menu2($login, $data)
    {
        $this->connection->chatSendServerMessage("success menu2!");
    }
}