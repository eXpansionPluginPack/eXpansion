<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class NextMapWidget extends \ManiaLive\Gui\Window {

    private $mapName;
    private $mapAuthor;
    private $labelName;
    private $labelAuthor;

    /** @var \DedicatedApi\Structures\Map */
    private $map;

    protected function onConstruct() {
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setPosY(0);
       // $frame->setLayout(new \ManiaLib\Gui\Layouts\Column());

        $label = new \ManiaLib\Gui\Elements\Label();
        $label->setText('$222' . __('Next map'));
        $label->setAlign("left", "top");
        $label->setPosX(10);
        $this->addComponent($label);

        $row = new \ManiaLive\Gui\Controls\Frame(20, 6);
        $row->setPosY(-4);
        $this->labelName = new \ManiaLib\Gui\Elements\Label();
        $this->labelName->setText('$ddd' . $this->mapName);
        $this->labelName->setAlign("right", "top");
        $this->labelName->setPosX(0);
        $row->addComponent($this->labelName);

        $icon = new \ManiaLib\Gui\Elements\Quad(6,6);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("Challenge");
        $icon->setAlign("left", "top");
        $icon->setPosX(2);
        $row->addComponent($icon);
        $frame->addComponent($row);

        $row = new \ManiaLive\Gui\Controls\Frame(20, 6);
        $row->setPosY(-8);
        $this->labelAuthor = new \ManiaLib\Gui\Elements\Label();
        $this->labelAuthor->setText('$ddd' . $this->mapAuthor);
        $this->labelAuthor->setAlign("right", "top");
        $this->labelAuthor->setPosX(0);
        $row->addComponent($this->labelAuthor);

        $icon = new \ManiaLib\Gui\Elements\Quad(6,6);
        $icon->setStyle("UIConstructionSimple_Buttons");
        $icon->setSubStyle("Author");
        $icon->setAlign("left", "top");
        $icon->setPosX(2);
        $row->addComponent($icon);
        $frame->addComponent($row);

        $this->addComponent($frame);
        $this->setScale(0.8);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function setMap(\DedicatedApi\Structures\Map $map) {
        $this->map = $map;
        $this->labelName->setText('$ddd' . $this->map->name);
        $this->labelAuthor->setText('$ddd' . $this->map->author);
    }

    function destroy() {
        parent::destroy();
    }

}

?>
