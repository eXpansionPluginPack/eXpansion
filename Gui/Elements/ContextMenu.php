<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer;
use ManiaLivePlugins\eXpansion\SubMenu\SubMenu;

/**
 * Description of menu
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class ContextMenu extends Control implements ScriptedContainer
{
    /** @var \ManiaLivePlugins\eXpansion\Gui\Structures\ContextMenuData[] */
    protected $items;
    protected $frame;
    protected static $script;
    public $hash;
    protected $callback;

    public function __construct($callback)
    {

        if (self::$script === null) {
            self::$script = new Script("Gui/Scripts/ContextMenu");
        }

        $this->hash = spl_object_hash($this);
        $this->frame = new Frame();
        $this->frame->setId("subMenu_" . $this->hash);
        $this->frame->setAttribute("data-hash", $this->hash);
        $this->frame->setAttribute("class", "contextMenu");
        $this->frame->setHidden(true);
        $this->frame->setPosition(0, 0);
        $this->frame->setAlign("left", "top");

        $this->addComponent($this->frame);

        $this->callback = $callback;
    }

    public function addItem($label, $itemValue, $isAdmin = false)
    {
        if (is_string($label)) {
            $label = eXpGetMessage($label);
        }
        $elem = new \ManiaLivePlugins\eXpansion\Gui\Structures\ContextMenuData($label, $itemValue);
        $hash = spl_object_hash($elem);
        $elem->setDataId($hash);
        $this->items[$hash] = $elem;
    }

    protected function onDraw()
    {
        $config = Config::getInstance();

        $i = 0;
        foreach ($this->items as $itemHash => $item) {
            $quad = new \ManiaLib\Gui\Elements\Quad(30, 5);
            $quad->setPosition(0, -($i * 5));
            $quad->setBgcolor($config->style_widget_bgColorize);
            $quad->setBgcolorFocus($config->style_widget_title_bgColorize);
            $quad->setOpacity(0.75);
            $quad->setId("menuItem");
            $quad->setAttribute("data-id", $item->getDataId());
            $quad->setAttribute("data-hash", $this->hash);
            $quad->setScriptEvents();
            $this->frame->addComponent($quad);

            $label = new Label(30, 5);
            $label->setText($item->message->getMessage());
            $label->setPosition(0, -($i * 5) - (5 / 2));
            $label->setTextColor("fff");
            $label->setTextSize(1);
            $label->setPosX(3);
            $label->setAlign("left", "center");
            $label->setStyle("TextCardScores2");
            $this->frame->addComponent($label);
            $i++;
        }

        Gui::$items[$this->hash] = $this->items;
        Gui::$callbacks[$this->hash] = $this->callback;
        parent::onDraw();
    }

    public function getScript()
    {
        return self::$script;
    }

    public function destroy()
    {
        if (array_key_exists($this->hash, Gui::$items)) {
            unset(Gui::$items[$this->hash]);
            unset(Gui::$callbacks[$this->hash]);
        }
        parent::destroy();
    }
}
