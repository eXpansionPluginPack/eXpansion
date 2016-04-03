<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

class TextEdit extends \ManiaLib\Gui\Element
{

    const TEXTFORMAT_Default = "default";
    const TEXTFORMAT_Script = "script";

    protected $xmlTagName = 'textedit';
    protected $posX = 0;
    protected $posY = 0;
    protected $posZ = 0;
    protected $default = "";
    protected $showlinenumbers = false;
    protected $textformat = self::TEXTFORMAT_Default;
    protected $autonewline = false;
    protected $name = "";

    function __construct($name, $sizeX = 100, $sizeY = 100)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->name = $name;
    }

    public function setShowLineNumbers($boolean = true)
    {
        $this->showlinenumbers = $boolean;
    }

    public function getShowLineNumbers()
    {
        return $this->showlinenumbers;
    }

    public function setTextFormat($textFormat = "default")
    {
        $this->textformat = $textFormat;
    }

    public function getTextFormat()
    {
        return $this->textformat;
    }

    public function setText($text)
    {
        $this->default = $text;
    }

    public function getAutoNewline()
    {
        return $this->autonewline;
    }

    public function setAutoNewline($boolean = true)
    {
        $this->autonewline = $boolean;
    }

    public function getText()
    {
        return $this->default;
    }

    public function setName($text)
    {
        $this->name = $text;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function postFilter()
    {
        if ($this->default !== null)
            $this->xml->setAttribute('default', $this->default);

        if ($this->textformat !== null)
            $this->xml->setAttribute('textformat', $this->textformat);

        if ($this->name !== null)
            $this->xml->setAttribute('name', $this->name);

        $this->xml->setAttribute('showlinenumbers', $this->showlinenumbers ? 1 : 0);
        $this->xml->setAttribute('autonewline', $this->autonewline ? 1 : 0);
    }

}
