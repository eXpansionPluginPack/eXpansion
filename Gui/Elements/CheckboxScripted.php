<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

/**
 * Description of CheckboxScripted
 *
 * @author De Cramer Oliver
 */
class CheckboxScripted extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer {

    private static $counter = 0;
    private static $script = null;
    private $checkboxId;
    private $label;
    private $button;
    private $entry;
    private $active = false;
    private $enabled = true;
    private $textWidth;
    private $skin = "checkbox";
    private $skinWidth = 5;

    function __construct($sizeX = 4, $sizeY = 4, $textWidth = 25) {
        $this->textWidth = $textWidth;

        $config = Config::getInstance();
        $this->checkboxId = self::$counter++;
        if (self::$counter > 100000)
            self::$counter = 0;

        $this->button = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->button->setAlign('left', 'center2');
        $this->button->setImage($config->getImage($this->skin, "normal_off.png"), true);
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
        //$this->entry->setVisibility(false);

        if (self::$script == null) {
            self::$script = new \ManiaLivePlugins\eXpansion\Gui\Scripts\CheckboxScript();
            self::$script->setParam("disabledActiveUrl", $config->getImage($this->skin, "disabled_on.png"));
            self::$script->setParam("disabledUrl", $config->getImage($this->skin, "disabled_off.png"));
            self::$script->setParam("ActiveUrl", $config->getImage($this->skin, "normal_on.png"));
            self::$script->setParam("InactiveUrl", $config->getImage($this->skin, "normal_off.png"));
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

    public function SetIsWorking($state) {
        $this->enabled = $state;
    }

    public function ToogleIsWorking() {
        $this->enabled = !$this->enabled;
    }

    protected function onResize($oldX, $oldY) {
        $this->button->setSize($this->skinWidth, 5);
        $this->button->setPosition(0, 0);
        $this->label->setSize($this->textWidth, 5);
        $this->label->setPosition($this->skinWidth, 0);
        parent::onResize($this->textWidth + $this->skinWidth, 5);
    }

    function onDraw() {
        self::$script->reset();
        $config = Config::getInstance();

        if (!$this->enabled) {
            if ($this->active) {
                $this->button->setImage($config->getImage($this->skin, "disabled_on.png"), true);
            } else {
                $this->button->setImage($config->getImage($this->skin, "disabled_off.png"), true);
            }
        } else {
            if ($this->active) {
                $this->button->setImage($config->getImage($this->skin, "normal_on.png"), true);
            } else {
                $this->button->setImage($config->getImage($this->skin, "normal_off.png"), true);
            }
        }
    }

    function setSkin($value = "ratiobutton", $width = 10) {
        $this->skin = $value;
        $this->skinWidth = $width;
        if (is_object(self::$script)) {
            $config = Config::getInstance();
            self::$script->setParam("disabledActiveUrl", $config->getImage($this->skin, "disabled_on.png"));
            self::$script->setParam("disabledUrl", $config->getImage($this->skin, "disabled_off.png"));
            self::$script->setParam("ActiveUrl", $config->getImage($this->skin, "normal_on.png"));
            self::$script->setParam("InactiveUrl", $config->getImage($this->skin, "normal_off.png"));
        }
    }

    function setStatus($boolean) {
        $this->active = $boolean;
        $this->entry->setDefault($this->active ? "1" : "0");
    }

    function getStatus() {
        return $this->entry->getDefault() == "1" ? true : false;
    }

    function getText() {
        return $this->label->getText();
    }

    function setText($text) {
        $this->label->setText($text);
    }

    function toggleActive($login) {
        $this->active = !$this->active;
        if ($this->toToggle != null)
            $this->toToggle->ToogleIsWorking($login);
        $this->entry->setDefault($this->active ? "1" : "0");
        $this->redraw();
    }

    public function destroy() {
        parent::destroy();
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function getScript() {
        if ($this->enabled)
            return self::$script;
        else
            return null;
    }

    public function setArgs($args) {
        if (isset($args['eXp_CheckboxE_' . $this->checkboxId])) {
            $active = $args['eXp_CheckboxE_' . $this->checkboxId] == '1';
            $out = true;
            if ($active == 0 || empty($active))
                $out = false;
            $this->setStatus($out);
        }
    }

}

?>
