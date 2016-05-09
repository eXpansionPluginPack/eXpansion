<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Widgets;

class MenuWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{
    public $items = [];
    protected $frame;
    protected $script;

    protected function onConstruct()
    {
        parent::onConstruct();
        $boundingBox = new \ManiaLib\Gui\Elements\Quad(320, 180);
        $boundingBox->setPosition(-160, 90, -50);
        $boundingBox->setId("boundingBox");
        $boundingBox->setBgcolor('0001');
        $boundingBox->setScriptEvents();
        $this->addComponent($boundingBox);
        
        $this->frame = New \ManiaLive\Gui\Controls\Frame(0, 0, new \ManiaLib\Gui\Layouts\Column(50, 20));
        $this->frame->setId("Menu");
        $this->addComponent($this->frame);

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Menu\Gui\Script");


        $version = \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION;
        $this->script->setParam("version", $version);
        $this->script->setParam("name", "Submenu");
        $storage = \ManiaLive\Data\Storage::getInstance();
        if ($storage->gameInfos->gameMode == \Maniaplanet\DedicatedServer\Structures\GameInfos::GAMEMODE_SCRIPT) {
            $this->script->setParam("gameMode", \ManiaLivePlugins\eXpansion\Gui\Gui::fixString($this->storage->gameInfos->scriptName));
        } else {
            $this->script->setParam("gameMode", $storage->gameInfos->gameMode);
        }
        $this->script->setParam("forceReset", $this->getBoolean(DEBUG));
        $this->registerScript($this->script);        

        $inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("widgetStatus");
        $inputbox->setPosition(900, 900);
        $inputbox->setScriptEvents();
        $this->addComponent($inputbox);

    }

    public function addGroup($name)
    {
        $group = new \ManiaLivePlugins\eXpansion\Menu\Gui\Controls\GroupItem($name);
        $this->frame->addComponent($group);
        return $group;
    }

    public function addItem($itemName, $handle, $plugin)
    {
        /* @var $config \ManiaLivePlugins\eXpansion\Gui\Config */
        $config = \ManiaLivePlugins\eXpansion\Gui\Config::getInstance();

        /* @var $label \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel */
        $label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(30, 5);
        $label->setText("    ".\exp_getMessage($itemName, null));
        $label->setAttribute("class", "menu item");
        $label->setScriptEvents();
        $label->setTextSize(1);
        $label->setStyle("Manialink_Body");
        $label->setBgcolor("000");
        $label->setTextColor("fff");
        $label->setAlign("left", "center");
        if (strlen($config->style_widget_bgColorize) == 6) {
            $label->setFocusAreaColor1($config->style_widget_bgColorize."aa");
        } else {
            $label->setFocusAreaColor1($config->style_widget_bgColorize);
        }

        if (strlen($config->style_widget_title_bgColorize) == 6) {
            $label->setFocusAreaColor2($config->style_widget_title_bgColorize."aa");
        } else {
            $label->setFocusAreaColor2($config->style_widget_title_bgColorize);
        }


        $label->setAction($this->createAction(array($plugin, "actionHandler"), $handle));
        $this->frame->addComponent($label);
    }
}
?>
