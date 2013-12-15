<?php

namespace ManiaLivePlugins\eXpansion\ForceMod;

use ManiaLive\Utilities\Console;

/**
 * ForceMod
 * A plugin to enable custom graphics to be forced on server
 *
 *  * @author Reaby
 */
class ForceMod extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    private $mods = array();

    /** @var Config */
    private $config;

    public function exp_onInit() {
        $this->config = Config::getInstance();
        $this->mods = $this->getMods();
    }

    public function exp_onReady() {
        $this->enableDedicatedEvents();
        $this->forceMods();
        foreach ($this->storage->players as $login => $player)
            $this->onPlayerConnect($login, false);
        foreach ($this->storage->spectators as $login => $player)
            $this->onPlayerConnect($login, true);
    }

    // main event for hiding the loadscreen
    public function onBeginRound() {
        Gui\Overlay\LoadScreen::EraseAll();
    }

    // main event for showing the loadscreen
    public function onStatusChanged($statusCode, $statusName) {
        if ($statusCode == 6) {
            if (empty($this->config->loadscreen))
                return;
            $screen = Gui\Overlay\LoadScreen::Create(null);
            $screen->setImage($this->config->loadscreen);
            $screen->setLayer(\ManiaLive\Gui\Window::LAYER_CUT_SCENE);
            $screen->show();
        }
        // main event for future usage
        if ($statusCode == 3) {
            
        }
    }

    // secondary event for showing the loadscreen
    public function onBeginMap($map, $warmUp, $matchContinuation) {
        if (empty($this->config->loadscreen))
            return;
        $screen = Gui\Overlay\LoadScreen::Create(null);
        $screen->setImage($this->config->loadscreen);
        $screen->setLayer(\ManiaLive\Gui\Window::LAYER_CUT_SCENE);
        $screen->show();
    }

    public function onPlayerConnect($login, $isSpectator) {
        $preload = Gui\Overlay\Preloader::Create($login);
        $preload->add($this->config->loadscreen);
        $preload->show();
    }

    private function forceMods() {
        try {
            Console::println("Enabling forced mods");
            $this->connection->setForcedMods(true, $this->mods);
        } catch (\Exception $e) {
            Console::println("[eXp\ForceMod] error while enabling the mod:" . $e->getMessage());
            return;
        }
    }

    private function getMods() {
        $version = $this->connection->getVersion();
        $env = "";
        switch ($version->titleId) {
            case "TMStadium":
                $env = "Stadium";
                break;
            case "TMValley":
                $env = "Valley";
                break;
            case "TMCanyon":
                $env = "Canyon";
                break;
        }

        try {
            $mods = array();
            if (!is_array($this->config->mods)) {
                $this->config->mods = array($this->config->mods);
            }
            foreach ($this->config->mods as $entry) {
                if (empty($entry))
                    continue;
                $mod = new \DedicatedApi\Structures\Mod();
                $mod->url = $entry;
                $mod->env = $env;
                //$mod->env = $env->titleId;
                $mods[] = $mod;
            }
            return $mods;
        } catch (\Exception $e) {
            return array();
        }
    }

}
