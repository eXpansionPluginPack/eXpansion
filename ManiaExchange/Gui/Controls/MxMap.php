<?php

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class MxMap extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $time;
    private $addAction;
    private $addButton;
    private $actionSearch;
    private $frame;

    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\ManiaExchange\Structures\MxMap $map, $controller, $isAdmin) {
        $sizeX = 120;
        $sizeY = 4;
        $this->isAdmin = $isAdmin;
        $id = $map->trackID;
        if (property_exists($map, "mapID"))
            $id = $map->mapID;

        $this->addAction = $this->createAction(array($controller, 'addMap'), $id);
        $this->actionSearch = $this->createAction(array($controller, 'search'), "", $map->username);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("United");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Formatting::stripColors(Formatting::stripStyles($map->name)));
        $this->frame->addComponent($this->label);

        $info = new \ManiaLib\Gui\Elements\Label(25, 4);
        $info->setAlign('left', 'center');
        $info->setText('$000' . $map->username);
        $info->setAction($this->actionSearch);
        $info->setStyle("TextCardSmallScores2");
        $info->setScriptEvents(true);
        $this->frame->addComponent($info);

        $this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setText($map->lengthName);
        $this->frame->addComponent($this->time);

        $info = new \ManiaLib\Gui\Elements\Label(4, 4);
        $info->setAlign('left', 'center');
        $info->setText($map->awardCount);
        $this->frame->addComponent($info);

        $info = new \ManiaLib\Gui\Elements\Label(16, 4);
        $info->setAlign('left', 'center');
        $info->setText($map->environmentName);
        $this->frame->addComponent($info);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        if ($this->isAdmin) {
            $this->addButton = new MyButton(16, 5);
            $this->addButton->setScale(0.7);
            $this->addButton->setText(__("Install"));
            $this->addButton->setAction($this->addAction);
            $this->frame->addComponent($this->addButton);
        }

        // disabled... todo: get remove button to refresh the tracklist
        /*
          $this->removeButton = new MyButton(16,6);
          $this->removeButton->setText("Remove");
          $this->removeButton->setAction($this->removeMap);
          $this->removeButton->setScale(0.6);
          $this->frame->addComponent($this->removeButton); */

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY) {
        //$this->bg->setSize($this->sizeX, $this->sizeY);
        $this->frame->setSize($this->sizeX, $this->sizeY + 1);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }

    function onDraw() {
        
    }

    function destroy() {
       $this->addButton->destroy();
        parent::destroy();
    }

}
?>

