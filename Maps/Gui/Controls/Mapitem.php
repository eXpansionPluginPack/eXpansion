<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;

class Mapitem extends \ManiaLive\Gui\Control {

    private $bg;
    private $queueButton;
    private $goButton;
    private $label;
    private $time;
    private $chooseNextMap;
    private $gotoMap;
    private $removeMap;
    private $removeButton;
    private $frame;

    function __construct($indexNumber, $login, \DedicatedApi\Structures\Map $map, $controller, $isAdmin) {
        $sizeX = 120;
        $sizeY = 4;

        $this->isAdmin = $isAdmin;
        $this->chooseNextMap = $this->createAction(array($controller, 'chooseNextMap'), $map);
        $this->gotoMap = $this->createAction(array($controller, 'gotoMap'), $map);
        $this->removeMap = $this->createAction(array($controller, 'removeMap'), $map);

        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        if ($indexNumber % 2 == 0) {
            $this->bg->setBgcolor('fff4');
        } else {
            $this->bg->setBgcolor('7774');
        }
        $this->bg->setScriptEvents(true);
        $this->addComponent($this->bg);


        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Formatting::stripColors($map->name, "999f"));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText($map->author);
        //$this->time->setText(\ManiaLive\Utilities\Time::fromTM($map->goldTime));
        $this->frame->addComponent($this->time);

        $ui = new \ManiaLib\Gui\Elements\Label(20, 4);
        $ui->setAlign('left', 'center');
        $ui->setScale(0.8);
        $ui->setText($map->mapStyle);
        //$this->time->setText(\ManiaLive\Utilities\Time::fromTM($map->goldTime));
        $this->frame->addComponent($ui);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(2, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->time = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);

        if (array_key_exists($map->uId, Maplist::$records)) {
            if (array_key_exists($login, Maplist::$records[$map->uId])) {

                $place = Maplist::$records[$map->uId]->$login->place . ".";
            } else {
                $place = "-";
            }
        } else {
            $place = "-";
        }


        $this->time->setText($place);
        $this->frame->addComponent($this->time);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->queueButton = new MyButton(16, 6);
        $this->queueButton->setText(__("Queue"));
        $this->queueButton->setAction($this->chooseNextMap);
        $this->queueButton->setScale(0.6);
        $this->frame->addComponent($this->queueButton);

        if ($this->isAdmin) {
            $this->goButton = new MyButton(16, 6);
            $this->goButton->setText(__("Go now"));
            $this->goButton->setAction($this->gotoMap);
            $this->goButton->setScale(0.6);
            $this->frame->addComponent($this->goButton);

            $this->removeButton = new MyButton(16, 6);
            $this->removeButton->setText(__("Remove"));
            $this->removeButton->setAction($this->removeMap);
            $this->removeButton->setScale(0.6);
            $this->frame->addComponent($this->removeButton);
        }

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->frame->setSize($this->sizeX, $this->sizeY);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }

    function onDraw() {
        
    }

    function destroy() {
        $this->goButton->destroy();
        $this->queueButton->destroy();
        $this->removeButton->destroy();
        
        $this->clearComponents();
        parent::destroy();
    }
}
?>

