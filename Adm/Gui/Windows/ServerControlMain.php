<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Helpers\Storage;

/**
 * Server Control panel Main window
 *
 * @author Petri
 */
class ServerControlMain extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    /** @var \ManiaLivePlugins\eXpansion\Adm\Adm */
    public static $mainPlugin;
    private $frame;
    private $actions;


    public function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->setTitle(__('Control Panel', $login));
        $btnX = 40;
        $btnY = 5.5;

        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, -7);
        $flow = new \ManiaLib\Gui\Layouts\Flow(150, $btnY + 2);
        $flow->setMargin(2, 1);

        $this->frame->setLayout($flow);

        $this->actions = new \stdClass();
        $this->actions->serverOptions = $this->createAction(array(self::$mainPlugin, "serverOptions"));
        $this->actions->gameOptions = $this->createAction(array(self::$mainPlugin, "gameOptions"));
        $this->actions->matchSettings = $this->createAction(array(self::$mainPlugin, "matchSettings"));
        $this->actions->serverManagement = $this->createAction(array(self::$mainPlugin, "serverManagement"));
        $this->actions->adminGroups = $this->createAction(array(self::$mainPlugin, "adminGroups"));
        $this->actions->scriptSettings = $this->createAction(array(self::$mainPlugin, "scriptSettings"));
        $this->actions->forceScores = $this->createAction(array(self::$mainPlugin, "forceScores"));
        $this->actions->roundPoints = $this->createAction(array(self::$mainPlugin, "roundPoints"));
        $this->actions->dbTools = $this->createAction(array(self::$mainPlugin, "dbTools"));
        $this->actions->expSettings = $this->createAction(array(self::$mainPlugin, "showExpSettings"));
        $this->actions->votesConfig = $this->createAction(array(self::$mainPlugin, "showVotesConfig"));
        $this->actions->pluginManagement = $this->createAction(array(self::$mainPlugin, "showPluginManagement"));


        $btn = new myButton($btnX, $btnY);
        $btn->setText(__("Deciated Control", $login));
        $btn->setAction($this->actions->serverManagement);
        $this->frame->addComponent($btn);

        $btn = new myButton($btnX, $btnY);
        $btn->setText(__("Server options", $login));
        $btn->setAction($this->actions->serverOptions);
        $this->frame->addComponent($btn);

        if (!$this->eXpIsRelay()) {
            $btn = new myButton($btnX, $btnY);
            $btn->setText(__("Game options", $login));
            $btn->setAction($this->actions->gameOptions);
            $this->frame->addComponent($btn);
        }

        $btn = new myButton($btnX, $btnY);
        $btn->setText(__("Admin Groups", $login));
        $btn->setAction($this->actions->adminGroups);
        $this->frame->addComponent($btn);

        if (!$this->eXpIsRelay()) {
            $btn = new myButton($btnX, $btnY);
            $btn->setText(__("Match settings", $login));
            $btn->setAction($this->actions->matchSettings);
            $this->frame->addComponent($btn);

            $btn = new myButton($btnX, $btnY);
            $btn->setText(__("ScriptMode settings", $login));
            $btn->setAction($this->actions->scriptSettings);
            $this->frame->addComponent($btn);

            $btn = new myButton($btnX, $btnY);
            $btn->setText(__("Force Scores", $login));
            $btn->setAction($this->actions->forceScores);
            $this->frame->addComponent($btn);

            $btn = new myButton($btnX, $btnY);
            $btn->setText(__("Round points", $login));
            $btn->setAction($this->actions->roundPoints);
            $this->frame->addComponent($btn);
        }

        $btnDb = new myButton($btnX, $btnY);
        $btnDb->setText(__("Database tools", $login));
        $btnDb->setAction($this->actions->dbTools);
        $this->frame->addComponent($btnDb);

        $btnDb = new myButton($btnX, $btnY);
        $btnDb->setText(__("eXpansion Settings", $login));
        $btnDb->setAction($this->actions->expSettings);
        $this->frame->addComponent($btnDb);

        $btnDb = new myButton($btnX, $btnY);
        $btnDb->setText(__("Plugin Management", $login));
        $btnDb->setAction($this->actions->pluginManagement);
        $this->frame->addComponent($btnDb);

        if (!$this->eXpIsRelay()) {
            $btn = new myButton($btnX, $btnY);
            $btn->setText(__("Configure Votes", $login));
            $btn->setAction($this->actions->votesConfig);
            $this->frame->addComponent($btn);
        }

        $this->addComponent($this->frame);

    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    public function destroy()
    {

        $this->frame->clearComponents();
        $this->connection = null;
        $this->storage = null;

        parent::destroy();
    }

    public function eXpIsRelay()
    {
        return Storage::getInstance()->isRelay;
    }
}
