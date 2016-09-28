<?php

namespace ManiaLivePlugins\eXpansion\ScoreDisplay;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Core\types\ExpPlugin;
use ManiaLivePlugins\eXpansion\ScoreDisplay\Gui\Widgets\Scores;
use ManiaLivePlugins\eXpansion\ScoreDisplay\Gui\Windows\ScoreSetup;

class ScoreDisplay extends ExpPlugin
{

    public function eXpOnReady()
    {
        $this->registerChatCommand("scores", "scores", 0, true);
        $this->registerChatCommand("scores", "scores", 1, true);
    }


    public function scores($login, $value = false)
    {
        if (!AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)) {
            $this->eXpChatSendServerMessage("No Permission.", $login);
            return;
        }


        if ($value === false) {
            $this->eXpChatSendServerMessage("valid parameters: hide, setup", $login);
        }

        if ($value == "setup") {
            $window = ScoreSetup::Create($login);
            $window->setSize(120, 80);
            $window->setName("ScoreSetup");
            $window->show();
            return;
        }

        if ($value == "hide") {
            Scores::EraseAll();
        }
    }
}
