<?php

namespace ManiaLivePlugins\eXpansion\Adm;

use \ManiaLivePlugins\eXpansion\Adm\Gui\Windows\ServerOptions;
use \ManiaLivePlugins\eXpansion\Adm\Gui\Windows\GameOptions;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Adm\Gui\Windows\AdminPanel;

class Adm extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    function exp_onReady() {
        //    $methods = get_class_methods($this->connection);
        if ($this->isPluginLoaded('Standard\Menubar'))
            $this->buildStdMenu();

        if ($this->isPluginLoaded('eXpansion\Menu')) {
            $this->callPublicMethod('eXpansion\Menu', 'addSeparator', _('Server Management'), true);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', _('Server Options'), null, array($this, 'serverOptions'), true);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', _('Game Options'), null, array($this, 'gameOptions'), true);
            $this->callPublicMethod('eXpansion\Menu', 'addItem', _('Match Settings'), null, array($this, 'matchSettings'), true);
        }

        $this->enableDedicatedEvents();

        foreach ($this->storage->players as $player)
            $this->onPlayerConnect($player->login, false);
        foreach ($this->storage->spectators as $player)
            $this->onPlayerConnect($player->login, true);
    }

    function onPlayerConnect($login, $isSpectator) {
        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
            $info = AdminPanel::Create($login);
            $info->setSize(50, 20);
            $info->setPosition(-160, -46);
            $info->show();
        }
    }

    public function onPlayerDisconnect($login) {
        AdminPanel::Erase($login);
    }

    public function buildStdMenu() {
        $this->callPublicMethod('Standard\Menubar', 'initMenu', \ManiaLib\Gui\Elements\Icons128x128_1::Options);
        $this->callPublicMethod('Standard\Menubar', 'addButton', _('Server Options'), array($this, 'serverOptions'), true);
        $this->callPublicMethod('Standard\Menubar', 'addButton', _('Game Options'), array($this, 'gameOptions'), true);
        $this->callPublicMethod('Standard\Menubar', 'addButton', _('Match Settings'), array($this, 'matchSettings'), true);
    }

    public function serverOptions($login) {
		if($this->callPublicMethod('eXpansion\AdminGroups', 'getPermission', $login, 'server_admin')){
			$window = ServerOptions::Create($login);
			$window->setTitle(_('Server Options'));
			$window->centerOnScreen();
			$window->setSize(160, 100);
			$window->show();
		}
    }
    
    public function gameOptions($login) {
		if($this->callPublicMethod('eXpansion\AdminGroups', 'getPermission', $login, 'game_settings')){
			$window = GameOptions::Create($login);
			$window->setTitle(_('Game Options'));
			$window->centerOnScreen();
			$window->setSize(160, 100);
			$window->show();
		}
    }
    
    public function matchSettings($login) {
		if($this->callPublicMethod('eXpansion\AdminGroups', 'getPermission', $login, 'game_settings')){
			$window = Gui\Windows\MatchSettings::Create($login);
			$window->setTitle(_('Match Settings'));
			$window->centerOnScreen();
			$window->setSize(120, 100);
			$window->show();
		}
    }

}

?>