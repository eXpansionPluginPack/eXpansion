<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Netlost;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Gui\Group;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Events\Event as AdminGroupEvent;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Widgets_Netlost\Gui\Widgets\Netlost;
use ManiaLivePlugins\eXpansion\Widgets_Netlost\Gui\Widgets\NetlostUpdate;
use Maniaplanet\DedicatedServer\Structures\PlayerNetInfo;

class Widgets_Netlost extends ExpPlugin implements \ManiaLivePlugins\eXpansion\AdminGroups\Events\Listener
{

    private $buffer = "";

    private $group;

    public function eXpOnLoad()
    {
        $this->enableDedicatedEvents();
        $this->updateGroup();
    }

    public function eXpOnReady()
    {
        Dispatcher::register(AdminGroupEvent::getClass(), $this);
        $this->displayWidget();
    }

    /**
     * @param PlayerNetInfo[] $players
     */
    public function onPlayerNetLost($players)
    {
        $out = "";
        $comma = "";

        foreach ($players as $player) {
            $pla = $this->storage->getPlayerObject($player->login);
            $out .= $comma . '"' . Gui::fixString($pla->nickName) . ' $z$s(' . Gui::fixString($pla->login) . ') "';
            $comma = ", ";
        }

        if (empty($out)) {
            $out = "Text[]";
        } else {
            $out = "[" . $out . "]";
        }

        if ($this->buffer !== $out) {

            $recepient = null;
            if (Config::getInstance()->showOnlyAdmins)
                $recepient = $this->group;

            $update = NetlostUpdate::Create($recepient);
            $update->setPlayer($out);
            $update->setTimeout(2);
            $update->show();
            $this->buffer = $out;
        }
    }

    public function updateGroup()
    {
        $adminlist = AdminGroups::getInstance()->get();
        $this->group = Group::Create("netlost_admins", $adminlist);
        $this->displayWidget();
    }

    /**
     * displayWidget(string $login)
     *
     * @param string $login
     */
    public function displayWidget()
    {
        $recepient = null;
        if (Config::getInstance()->showOnlyAdmins)
            $recepient = $this->group;

        $info = Netlost::Create($recepient);
        $info->setSize(200, 12);
        $info->setPosition(-115, -50);
        $info->show();
    }

    public function eXpOnUnload()
    {
        Dispatcher::unregister(AdminGroupEvent::getClass(), $this);
        Netlost::EraseAll();
        NetlostUpdate::EraseAll();
        Group::Erase("netlost_admins");
        $this->group = null;
    }

    public function eXpAdminAdded($login)
    {
        $this->updateGroup();
    }

    public function eXpAdminRemoved($login)
    {
        $this->updateGroup();
        Netlost::Erase($login);
    }

}
