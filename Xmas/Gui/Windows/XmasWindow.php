<?php

namespace ManiaLivePlugins\eXpansion\Xmas\Gui\Windows;

use ManiaLivePlugins\eXpansion\Xmas\Config;

class XmasWindow extends \ManiaLive\Gui\Window {

    private $quad, $xml, $frame;
    private $config;

    protected function onConstruct() {
        $this->config = Config::getInstance();
        $this->setAlign("center", "bottom");
        $this->setSize($this->config->width * $this->config->repeat, $this->config->height);
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        for ($x = 0; $x < $this->config->repeat; $x++) {
            $quad = new \ManiaLib\Gui\Elements\Quad($this->config->width, $this->config->height);
            $quad->setPosition($this->config->posX, $this->config->posY, $this->config->posZ);
            $quad->setImage($this->config->texture, true);
            $quad->setAlign("left", "top");
            $quad->setId("q" . $x);
            $quad->setScriptEvents();
            $this->frame->addComponent($quad);
        }

        $this->setScriptEvents();
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
       
        $declare = "";
        $color = "";
        for ($x = 0; $x < $this->config->repeat; $x++) {
            $declare .= 'declare CMlQuad q' . $x . ' = (Page.GetFirstChild("q' . $x . '") as CMlQuad);';
            $color .= '
         r = MathLib::Rand(2, 5)*25.5;
         g = MathLib::Rand(2, 5)*25.5;
         b = MathLib::Rand(3, 5)*25.5;
         color = <r, g, b>;
        q' . $x . '.Colorize = color;';
        }

        $xml = '
<script><!--
#Include "MathLib" as MathLib

main () {
log ("new");
   
   declare Integer lastupdate = Now + -4000;
   ' . $declare . '
       
        while (True) {
        if (Now > lastupdate + 3000) {
        lastupdate = Now;
        declare Vec3 color;
        declare Real r;
        declare Real g;
        declare Real b;
        ' . $color . '
    }
    yield;
}

}

--></script>
        ';
         $this->xml->setContent($xml);
        $this->addComponent($this->xml);
        $this->setScale($this->config->scale);
    }

}
?>
