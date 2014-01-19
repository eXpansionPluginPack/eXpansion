<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLivePlugins\eXpansion\Gui\Config;

class Button extends \ManiaLive\Gui\Control {

    private static $counter = 0;
    protected $label;
    protected $labelDesc;
    protected $activeFrame;
    protected $backGround;
    protected $icon;
    
    private $buttonId;
    private $text;
    private $description;
    private $value;
    private $isActive = false;

    /**
     * Button
     * 
     * @param int $sizeX = 24
     * @param intt $sizeY = 6
     */
    function __construct($sizeX = 24, $sizeY = 6) {
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
        $this->backGround->setAlign('left', 'center2');
        $this->backGround->setImage($config->button, true);
        //$this->backGround->setImageFocus($config->buttonActive, true);
        $this->backGround->setScriptEvents(true);
        $this->addComponent($this->backGround);


        $this->label = new \ManiaLib\Gui\Elements\Label($sizeX, $sizeY);
        $this->label->setAlign('center', 'center2');
        $this->label->setStyle("TextChallengeNameMedium");
        $this->label->setTextSize(3);
        $this->label->setFocusAreaColor1("bbba");
        $this->label->setFocusAreaColor2("fffa");
        
        $this->labelDesc = new \ManiaLib\Gui\Elements\Label(20, 6);
        $this->labelDesc->setAlign('left', 'center2');
        $this->labelDesc->setPosition(6,6);
        $this->labelDesc->setId("Desc_$this->buttonId");
        $this->labelDesc->setScriptEvents();

        $this->sizeX = $sizeX + 2;
        $this->sizeY = $sizeY + 2;
        $this->setSize($sizeX + 2, $sizeY + 2);
    }

    protected function onResize($oldX, $oldY) {
        //$this->label->setSize($this->sizeX - 2, $this->sizeY - 1);

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
        $this->clearComponents();

        if ($this->isActive)
            $this->addComponent($this->activeFrame);

        if (!empty($this->text)) {
            $this->addComponent($this->label);
            $this->label->setText($this->text);
        }
        
        if (!empty($this->description)) {
            $this->addComponent($this->labelDesc);
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

    function setDescription($description) {
        $this->description = $description;
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
        $this->label->setFocusAreaColor1($value);
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
        $this->label->setAction($action);
        $this->backGround->setAction($action);
    }

    public function setIcon($style, $subStyle = null) {
        $this->icon = new \ManiaLib\Gui\Elements\Quad($this->getSizeY(), $this->getSizeY());
        $this->icon->setAlign('left', 'center');
        $this->icon->setScriptEvents();
        if ($subStyle != null) {
            $this->icon->setStyle($style);
            $this->icon->setSubStyle($subStyle);
        } else {
            $this->icon->setImage($style);
        }
        $this->icon->setId("Icon_" . $this->buttonId);
        $this->addComponent($this->icon);

        $this->label->setPosX((($this->sizeX - 2) / 2) + ($this->getSizeY() - 1));
        $this->label->setSizeX($this->getSizeX() - ($this->getSizeY() + 1));
    }

    public function getScriptDeclares() {
        if (!empty($this->description)) {
            $script = <<<EOD

                declare CMlQuad Icon$this->buttonId <=> (Page.GetFirstChild("Icon_$this->buttonId") as CMlQuad);
                declare CMlQuad Desc$this->buttonId <=> (Page.GetFirstChild("Desc_$this->buttonId") as CMlQuad);
                Desc$this->buttonId.Hide();
                declare mouseOver$this->buttonId = False;   
EOD;
            echo $script;
            return $script;
        }else{
            return "";
        }
        
    }

    public function getScriptMainLoop() {
        if (!empty($this->description)) {
            $script = <<<EOD
                if (mouseOver$this->buttonId) {                                                                                               
                         Desc$this->buttonId.Show();
                }else{
                         Desc$this->buttonId.Hide();
                }

                MouseOver
                foreach (Event in PendingEvents) {
                    if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId == "Icon_$this->buttonId")  {
                           mouseOver$this->buttonId = true;
                   }                                   
               }  
EOD;
            return $script;
        }else{
            return "";
        }
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        parent::destroy();
    }

}

?>