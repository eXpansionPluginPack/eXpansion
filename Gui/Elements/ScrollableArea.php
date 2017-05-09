<?php

namespace ManiaLivePlugins\eXpansion\Gui\Elements;

use ManiaLib\Gui\Component;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Helpers\Maniascript;

class ScrollableArea extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\ScriptedContainer
{

    protected $area;

    protected $scrollHBg;
    protected $scrollH;
    protected $scrollHLeft;
    protected $scrollHRight;

    protected $scrollVBg;
    protected $scrollV;
    protected $scrollVDown;
    protected $scrollVUp;

    protected $myScript;

    public function __construct($sizeX = 120, $sizeY = 90)
    {
        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->area = new FrameClipable($sizeX - 1, $sizeY + 2);

        $this->area->setId("scrollableArea");

        $this->addComponent($this->area);

        $this->myScript = new Script("Gui\\Scripts\\ScrollableArea");

        $this->scrollVBg = new \ManiaLib\Gui\Elements\Quad(4, 40);
        $this->scrollVBg->setAlign("center", "top");
        $this->scrollVBg->setStyle("Bgs1InRace");
        $this->scrollVBg->setSubStyle('BgPlayerCard');
        $this->scrollVBg->setId("scrollVBg");
        $this->scrollVBg->setOpacity(0.9);
        $this->addComponent($this->scrollVBg);

        $this->scrollV = new \ManiaLib\Gui\Elements\Quad(3, 15);
        $this->scrollV->setAlign("center", "top");
        $this->scrollV->setStyle("BgsPlayerCard");
        $this->scrollV->setSubStyle('BgRacePlayerName');
        $this->scrollV->setId("scrollVBar");
        $this->scrollV->setScriptEvents();
        $this->addComponent($this->scrollV);

        $this->scrollVDown = new \ManiaLib\Gui\Elements\Quad(6.5, 6.5);
        $this->scrollVDown->setAlign("center", "top");
        $this->scrollVDown->setStyle("Icons64x64_1");
        $this->scrollVDown->setSubStyle("ArrowDown");
        $this->scrollVDown->setId("scrollVDown");
        $this->scrollVDown->setScriptEvents();
        $this->addComponent($this->scrollVDown);

        $this->scrollVUp = new \ManiaLib\Gui\Elements\Quad(6.5, 6.5);
        $this->scrollVUp->setAlign("center", "bottom");
        $this->scrollVUp->setStyle("Icons64x64_1");
        $this->scrollVUp->setSubStyle("ArrowUp");
        $this->scrollVUp->setId("scrollVUp");
        $this->scrollVUp->setScriptEvents();
        $this->addComponent($this->scrollVUp);


        $this->scrollHBg = new \ManiaLib\Gui\Elements\Quad(40, 4);
        $this->scrollHBg->setAlign("left", "top");
        $this->scrollHBg->setBgcolor("111");
        $this->scrollHBg->setId("scrollHBg");
        $this->scrollHBg->setOpacity(0.9);
        $this->addComponent($this->scrollHBg);

        $this->scrollH = new \ManiaLib\Gui\Elements\Quad(15, 3);
        $this->scrollH->setAlign("left", "top");
        $this->scrollH->setStyle("BgsPlayerCard");
        $this->scrollH->setSubStyle('BgRacePlayerName');
        $this->scrollH->setId("scrollHBar");
        $this->scrollH->setScriptEvents();
        $this->addComponent($this->scrollH);

        $this->scrollHLeft = new \ManiaLib\Gui\Elements\Quad(6.5, 6.5);
        $this->scrollHLeft->setAlign("left", "top");
        $this->scrollHLeft->setStyle("Icons64x64_1");
        $this->scrollHLeft->setSubStyle("ArrowPrev");
        $this->scrollHLeft->setId("scrollHLeft");
        $this->scrollHLeft->setScriptEvents();
        $this->addComponent($this->scrollHLeft);

        $this->scrollHRight = new \ManiaLib\Gui\Elements\Quad(6.5, 6.5);
        $this->scrollHRight->setAlign("left", "top");
        $this->scrollHRight->setStyle("Icons64x64_1");
        $this->scrollHRight->setSubStyle("ArrowNext");
        $this->scrollHRight->setId("scrollHRight");
        $this->scrollHRight->setScriptEvents();
        $this->addComponent($this->scrollHRight);


        $this->setSize($sizeX, $sizeY);
    }

    public function setSize()
    {
        $args = func_get_args();
        $this->sizeX = $args[0];
        $this->sizeY = $args[1];
        $this->area->setPositionY(-($this->area->sizeY / 2) + 3);
        $this->area->setPositionX(-($this->area->sizeX / 2) - 3);

        $this->scrollV->setPosition($this->sizeX - 3, 0);
        $this->scrollVBg->setPosition($this->sizeX - 3);
        $this->scrollVBg->setSizeY($this->sizeY - 5);

        $this->scrollVDown->setPosition($this->sizeX - 3, -($this->sizeY - 6));
        $this->scrollVUp->setPosition($this->sizeX - 3, -1);

        $this->scrollH->setPosition(0, -$this->sizeY);
        $this->scrollHBg->setPosition(0, -$this->sizeY + 0.5);
        $this->scrollHBg->setSizeX($this->sizeX - 3);

        $this->scrollHLeft->setPosition(-5, -($this->sizeY - 2));
        $this->scrollHRight->setPosition($this->sizeX - 6, -($this->sizeY - 2));

    }


    public function setContent(Component $content)
    {
        $content->setId("content");
        $this->myScript->setParam("contentSizeY", Maniascript::getReal($content->getRealSizeY()));
        $this->myScript->setParam("contentSizeX", Maniascript::getReal($content->getRealSizeX()));
        $this->area->addComponent($content);
    }


    /**
     * @return Script the script this container needs
     */
    public function getScript()
    {
        return $this->myScript;
    }


}
