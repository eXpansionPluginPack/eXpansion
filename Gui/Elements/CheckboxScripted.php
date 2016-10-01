<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of CheckboxScripted
 *
 * @author De Cramer Oliver
 */
class CheckboxScripted extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer
{

    private static $counter = 0;
    private static $script = null;
    private $checkboxId;
    protected $label;
    protected $button;
    protected $entry;
    protected $active = false;
    protected $enabled = true;
    protected $textWidth;
    protected $skin = "checkbox";
    protected $skinWidth = 5;

    public function __construct($sizeX = 4, $sizeY = 4, $textWidth = 25)
    {
        $this->textWidth = $textWidth;

        $config = Config::getInstance();
        $this->checkboxId = self::$counter++;
        if (self::$counter > 100000) {
            self::$counter = 0;
        }

        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setStyle('Icons64x64_1');
        $this->button->setSubStyle('GenericButton');
        $this->button->setId('eXp_CheckboxQ_' . $this->checkboxId);
        $this->button->setScriptEvents(true);
        $this->addComponent($this->button);


        $this->entry = new \ManiaLib\Gui\Elements\Entry(20, $sizeY);
        $this->entry->setName('eXp_CheckboxE_' . $this->checkboxId);
        $this->entry->setId('eXp_CheckboxE_' . $this->checkboxId);
        $this->addComponent($this->entry);
        $this->entry->setDefault($this->active ? "1" : "0");
        $this->entry->setPosX(4000);
        $this->entry->setScriptEvents(true);

        if (self::$script == null) {
            self::$script = new \ManiaLivePlugins\eXpansion\Gui\Scripts\CheckboxScript();
            self::$script->setParam("disabledActiveUrl", "<0.5,1.0,0.5>");
            self::$script->setParam("disabledUrl", "<1.0,0.5,0.5>");
            self::$script->setParam("ActiveUrl", "<0.,1.,0.>");
            self::$script->setParam("InactiveUrl", "<1.,0.,0.>");
        }

        $this->label = new \ManiaLib\Gui\Elements\Label($textWidth, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        $this->label->setScale(1.1);
        $this->label->setTextColor("fff");
        $this->label->setStyle("TextCardInfoSmall");
        $this->addComponent($this->label);

        $this->setSize($sizeX + $textWidth, 6);
    }

    public function SetIsWorking($state)
    {
        $this->enabled = $state;
    }

    public function ToogleIsWorking()
    {
        $this->enabled = !$this->enabled;
    }

    protected function onResize($oldX, $oldY)
    {
        $this->button->setSize($this->skinWidth, 5);
        $this->button->setPosition(0, 0);
        $this->label->setSize($this->textWidth, 5);
        $this->label->setPosition($this->skinWidth, 0);
        parent::onResize($this->textWidth + $this->skinWidth, 5);
    }

    protected function onDraw()
    {
        self::$script->reset();
        $config = Config::getInstance();

        if (!$this->enabled) {
            if ($this->active) {
                $this->button->setColorize("7f7");
            } else {
                $this->button->setColorize("f77");
            }
        } else {
            if ($this->active) {
                $this->button->setColorize("0f0");
            } else {
                $this->button->setColorize("f00");
            }
        }
    }

    public function setSkin($value = "ratiobutton", $width = 10)
    {
        $this->skin = $value;
        $this->skinWidth = 5;
        if (is_object(self::$script)) {
            $config = Config::getInstance();
            self::$script->setParam("disabledActiveUrl", "<0.5,1.,0.5>");
            self::$script->setParam("disabledUrl", "<1.,0.5,0.5>");
            self::$script->setParam("ActiveUrl", "<0.,1.,0.>");
            self::$script->setParam("InactiveUrl", "<1.,0.,0.>");
        }
    }

    public function setStatus($boolean)
    {
        $this->active = $boolean;
        $this->entry->setDefault($this->active ? "1" : "0");
    }

    public function getStatus()
    {
        return $this->entry->getDefault() == "1" ? true : false;
    }

    public function getText()
    {
        return $this->label->getText();
    }

    public function setText($text)
    {
        $this->label->setText($text);
    }

    public function toggleActive($login)
    {
        $this->active = !$this->active;
        if ($this->toToggle != null) {
            $this->toToggle->ToogleIsWorking($login);
        }
        $this->entry->setDefault($this->active ? "1" : "0");
        $this->redraw();
    }

    public function destroy()
    {
        parent::destroy();
    }

    public function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function getScript()
    {
        if ($this->enabled) {
            return self::$script;
        } else {
            return null;
        }
    }

    public function setArgs($args)
    {
        if (isset($args['eXp_CheckboxE_' . $this->checkboxId])) {
            $active = $args['eXp_CheckboxE_' . $this->checkboxId] == '1';
            $out = true;
            if ($active == 0 || empty($active)) {
                $out = false;
            }
            $this->setStatus($out);
        }
    }
}
