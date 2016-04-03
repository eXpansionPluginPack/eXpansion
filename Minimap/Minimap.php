<?php

namespace ManiaLivePlugins\eXpansion\Minimap;

use ManiaLivePlugins\eXpansion\Minimap\Gui\Windows\MapWindow;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use \ManiaLivePlugins\eXpansion\Core\Events\ScriptmodeEvent;

class Minimap extends ExpPlugin
{

    public function exp_onReady()
    {
        $this->enableScriptEvents(array("LibXmlRpc_BeginPlaying", "LibXmlRpc_BeginPodium"));
        $this->show();
    }

    public function onBeginMatch()
    {
        $this->hide();
        $this->show();
    }

    public function onEndMatch($rankings, $winnerTeamOrMap)
    {
        $this->hide();
    }

    public function exp_onModeScriptCallback($param1, $param2)
    {
        $this->debug($param1);
        $this->debug($param2);
    }

    public function LibXmlRpc_BeginPlaying()
    {
        $this->console("begin playing");
        $this->onBeginMatch();
    }

    public function LibXmlRpc_BeginPodium()
    {
        $this->onEndMatch(null, null);
    }

    public function show()
    {
        $window = MapWindow::Create(null);
        $window->show();
    }

    public function hide()
    {
        MapWindow::EraseAll();
    }

    public function onSettingsChanged(Variable $var)
    {
        if ($var->getPluginId() === $this->getId()) {
            MapWindow::EraseAll();
            $window = MapWindow::Create(null);
            // $window->setLayer('Markers');
            $window->show();
        }
    }

    public function exp_onUnload()
    {
        MapWindow::EraseAll();
        parent::exp_onUnload();
    }

}

?>
