<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer;
use ManiaLivePlugins\eXpansion\SubMenu\SubMenu;
use ManiaLivePlugins\eXpansion\Gui\Gui;

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

        $this->hash  = spl_object_hash($this);
        $this->frame = new Frame();
        $this->frame->setId("subMenu_".$this->hash);
        $this->frame->setAttribute("data-hash", $this->hash);
        $this->frame->setPosition(0, 0);
        $this->frame->setAlign("left", "top");
        $this->frame->setAttribute("hidden", "1");
        $this->frame->setScriptEvents();
        $this->addComponent($this->frame);

        $this->callback = $callback;
    }

    public function addItem($label, $itemValue, $isAdmin = false)
    {
        if (is_string($label)) {
            $label = exp_getMessage($label);
        }
        $elem               = new \ManiaLivePlugins\eXpansion\Gui\Structures\ContextMenuData($label, $itemValue);
        $hash               = spl_object_hash($elem);
        $elem->setDataId($hash);
        $this->items[$hash] = $elem;
    }

    protected function onDraw()
    {
        $this->frame->clearComponents();
        $this->frame->setPosZ($this->getPosZ() + 5);
        $i = 0;
        foreach ($this->items as $itemHash => $item) {
            $label = new Label();
            $label->setText($item->message->getMessage());
            $label->setPosition(0, -($i * 6));
            $label->setBgcolor("000");
            $label->setBgcolorFocus("3af");
            $label->setTextColor("fff");
            $label->setId("menuItem");
            $label->setAttribute("data-id", $item->getDataId());
            $label->setAttribute("data-hash", $this->hash);
            $label->setScriptEvents();
            $this->frame->addComponent($label);
            $i++;
        }

        echo "adding items & callback!\n";

        Gui::$items[$this->hash]     = $this->items;
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