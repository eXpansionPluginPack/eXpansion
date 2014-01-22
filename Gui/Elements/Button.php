<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Button extends \ManiaLive\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer{

    private static $counter = 0;
    
    private static $script = null;
    
    protected $label;
    protected $labelDesc;
    protected $activeFrame;
    protected $backGround;
    protected $backGroundDesc;
    protected $frameDescription;
    protected $icon;
    private $buttonId;
    private $text;
    private $description;
    private $value;
    private $isActive = false;
    private $action = 0;

    /**
     * Button
     * 
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 24, $sizeY = 6) {
        
        if(self::$script == null){
            self::$script = new \ManiaLivePlugins\eXpansion\Gui\Scripts\ButtonScript();
        }
        
        $config = Config::getInstance();
        $this->buttonId = self::$counter++;
        if (self::$counter > 100000)
            self::$counter = 0;

        $this->activeFrame = new \ManiaLib\Gui\Elements\Quad($sizeX + 2, $sizeY + 2.5);
        $this->activeFrame->setPosition(-1, 0);
        $this->activeFrame->setAlign('left', 'center');
        $this->activeFrame->setStyle("Icons128x128_Blink");
        $this->activeFrame->setSubStyle("ShareBlink");

        $this->backGround = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->backGround->setAlign('center', 'center2');
        //$this->backGround->setImage($config->button, true);
        //$this->backGround->setImageFocus($config->buttonActive, true);
        $this->backGround->setStyle("Bgs1");
        $this->backGround->setSubStyle(\ManiaLib\Gui\Elements\Bgs1::BgCardSystem);
        $this->backGround->setScriptEvents(true);
        $this->addComponent($this->backGround);


        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->label->setAlign('center', 'center2');
        $this->label->setStyle("TextCardScores2");
        $this->label->setTextSize(3);
        $this->label->setTextEmboss();
        $this->label->setTextColor("222");
        //$this->label->setFocusAreaColor1("");
        //$this->label->setFocusAreaColor2("fffa");

        $this->frameDescription = new \ManiaLib\Gui\Elements\Frame();
        $this->frameDescription->setId("Desc_Icon_" . $this->buttonId);
        $this->frameDescription->setPositionZ(10);
        $this->frameDescription->setAttribute('hidden', 'true');

        $this->labelDesc = new \ManiaLib\Gui\Elements\Label(20, 6);
        $this->labelDesc->setAlign('left', 'center2');
        $this->labelDesc->setPosition(7, 3);
        $this->labelDesc->setPositionZ(5);
        $this->frameDescription->add($this->labelDesc);

        $this->backGroundDesc = new \ManiaLib\Gui\Elements\Quad(32, 6);
        $this->backGroundDesc->setAlign('left', 'center2');
        $this->backGroundDesc->setStyle('Bgs1');
        $this->backGroundDesc->setSubStyle('BgCardPlayer');
        $this->backGroundDesc->setPosition(5, 3);
        $this->backGroundDesc->setPositionZ(1);
        $this->frameDescription->add($this->backGroundDesc);

        $this->sizeX = $sizeX + 2;
        $this->sizeY = $sizeY + 2;
        $this->setSize($sizeX + 2, $sizeY + 2);
    }

    protected function onResize($oldX, $oldY) {
        //$this->label->setSize($this->sizeX - 2, $this->sizeY - 1);
        $this->backGround->setPosX(($this->sizeX - 2) / 2);

        if ($this->icon == null) {
            $this->label->setPosX(($this->sizeX - 2) / 2);
            $this->label->setPosZ($this->posZ);
        } else {
            $this->label->setPosX((($this->sizeX - 2) / 2) + ($this->getSizeY() - 1));
            $this->label->setSizeX($this->getSizeX() - ($this->getSizeY() + 1));
        }

        $this->setScale(0.7);
    }

    function onDraw() {
        self::$script->reset();
        parent::onDraw();
        $this->clearComponents();

        if ($this->isActive)
            $this->addComponent($this->activeFrame);
        
        if ($this->icon == null) {
            $this->addComponent($this->backGround);
        }
        
        if (!empty($this->text)) {
            $this->addComponent($this->label);
            $this->label->setText($this->text);
        }

        if (!empty($this->description)) {
            $this->addComponent($this->frameDescription);
            $this->labelDesc->setText($this->description);
        }

        if ($this->icon != null)
            $this->addComponent($this->icon);
    }

    function getText() {
        return $this->text;
    }

    function setText($text) {
        $this->text = $text;
    }

    function setDescription($description, $sizeX = 30) {
        $this->description = "$000" . $description;
        $this->labelDesc->setSizeX($sizeX);
        $this->backGroundDesc->setSizeX($sizeX + 2);
    }
    
    public function getDescription(){
        return $this->description;
    }

    function setActive($bool = true) {
        $this->isActive = $bool;
    }

    function getValue() {
        return $this->value;
    }

    /**
     * Colorize the button background     
     * @param string $value 4-digit RGBa code
     */
    function colorize($value) {
        // $this->label->setFocusAreaColor1($value);
        // $this->backGround->setColorize($value);
    }

    /**
     * Sets text color 
     * @param string $value 4-digit RGBa code
     */
    function setTextColor($textcolor) {
        $this->label->setTextColor($textcolor);
    }

    function setValue($text) {
        $this->value = $text;
    }

    function setAction($action) {
        // $this->label->setAction($action);
        $this->backGround->setAction($action);
        $this->action = $action;
        if ($this->icon != null)
            $this->icon->setAction($action);
    }

    public function setIcon($style, $subStyle = null) {
        $this->icon = new \ManiaLib\Gui\Elements\Quad($this->getSizeY(), $this->getSizeY());
        $this->icon->setAlign('left', 'center');
        $this->icon->setScriptEvents();
        if ($subStyle != null) {
            $this->icon->setStyle($style);
            $this->icon->setSubStyle($subStyle);
        } else {
            $this->icon->setImage($style, true);
        }
        $this->icon->setId("Icon_" . $this->buttonId);
        $this->icon->setAction($this->action);
        $this->addComponent($this->icon);

        $this->label->setPosX((($this->sizeX - 2) / 2) + ($this->getSizeY() - 1));
        $this->label->setSizeX($this->getSizeX() - ($this->getSizeY() + 1));
    }
  
    function getButtonId(){
        return $this->buttonId;
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }

    public function getScript() {
        return self::$script;
    }

}

?>
