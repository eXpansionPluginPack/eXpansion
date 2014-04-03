<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class NextMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    private $bg;
    private $mapName;
    private $mapAuthor;
    private $labelName;
    private $labelAuthor;

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    private $map;

    protected function onConstruct() {
        parent::onConstruct();
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setPosY(1);
        $this->setSize(44,15);
        // $frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        // $login = $this->getRecipient();

        $this->bg = new \ManiaLib\Gui\Elements\Quad(42, 13);
        $this->bg->setAlign("left", "center");
        $this->bg->setStyle("Bgs1InRace");
        $this->bg->setSubStyle("BgList");
        $this->bg->setPosition(4, -8);
        $this->addComponent($this->bg);

        /*$label = new \ManiaLib\Gui\Elements\Label(30);
        $label->setText('$ddd' . 'Next', $login));        
        $label->setAlign("right", "top");
        $label->setPosX(-8);
        $label->setPosY(1);
        // $this->addComponent($label); */

        $row = new \ManiaLive\Gui\Controls\Frame(0, -4);
        $this->labelName = new \ManiaLib\Gui\Elements\Label(30, 7);
        $this->labelName->setText('$ddd' . $this->mapName);
        $this->labelName->setPosX(36);
        $this->labelName->setAlign("right", "top");
        $this->labelName->setPosY(0);
        $row->addComponent($this->labelName);

        $icon = new \ManiaLib\Gui\Elements\Quad(6, 6);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("Challenge");
        $icon->setPosX(36);
        $icon->setPosY(1);
        $row->addComponent($icon);
        $frame->addComponent($row);

        $row = new \ManiaLive\Gui\Controls\Frame(0, -8);
        $this->labelAuthor = new \ManiaLib\Gui\Elements\Label(28, 7);
        $this->labelAuthor->setText('$ddd' . $this->mapAuthor);
        $this->labelAuthor->setAlign("right", "top");
        $this->labelAuthor->setPosX(36);
        $this->labelAuthor->setPosY(-1);
        $row->addComponent($this->labelAuthor);

        $icon = new \ManiaLib\Gui\Elements\Quad(6, 6);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("Author");
        $icon->setPosX(36);
        $row->addComponent($icon);
        $frame->addComponent($row);

        $this->addComponent($frame);
        $this->setScale(0.8);
        
        $this->setName("Next Map");        
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function setAction($action) {
        $this->bg->setAction($action);
    }


    function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map) {
	$this->map = $map;
	$this->labelName->setText('$ddd' . $this->map->name);
	$this->labelAuthor->setText('$ddd' . $this->map->author);
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
