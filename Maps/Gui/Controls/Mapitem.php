<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;

class Mapitem extends \ManiaLive\Gui\Control {

    private $bg;
    private $queueButton;
    private $goButton;
    private $label;
    private $time;
    private $queueMap;
    private $gotoMap;
    private $removeMap;
    private $showRecsAction;
    private $removeButton;
    private $showRecsButton;
    private $frame;

    function __construct($indexNumber, $login, \DedicatedApi\Structures\Map $map, $controller, $isAdmin, $localrec, $sizeX) {
        $sizeY = 4;

        $this->isAdmin = $isAdmin;
        $this->queueMap = $this->createAction(array($controller, 'queueMap'), $map);
        $this->gotoMap = $this->createAction(array($controller, 'gotoMap'), $map);
        $this->removeMap = $this->createAction(array($controller, 'removeMap'), $map);
        $this->showRecsAction = $this->createAction(array($controller, 'showRec'), $map);
        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
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

        $this->label = new \ManiaLib\Gui\Elements\Label(70, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Formatting::stripColors($map->name, "999f"));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->time = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText($map->author);
        //$this->time->setText(\ManiaLive\Utilities\Time::fromTM($map->goldTime));
        $this->frame->addComponent($this->time);

        $ui = new \ManiaLib\Gui\Elements\Label(20, 4);
        $ui->setAlign('left', 'center');
        $ui->setScale(0.8);
        //$ui->setText($map->authorTime);
        $ui->setText(\ManiaLive\Utilities\Time::fromTM($map->goldTime));
        $this->frame->addComponent($ui);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(2, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->time = new \ManiaLib\Gui\Elements\Label(6, 4);
        $this->time->setAlign('center', 'center');
        $this->time->setScale(0.8);
        $this->time->setText($localrec);
        $this->frame->addComponent($this->time);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->queueButton = new MyButton(26, 5);
        $this->queueButton->setText(__("Queue",$login));
        $this->queueButton->setAction($this->queueMap);
        $this->queueButton->colorize('2a2');
        $this->queueButton->setScale(0.5);
        $this->frame->addComponent($this->queueButton);
        if (Maplist::$localrecordsLoaded) {
            $this->showRecsButton = new MyButton(26, 5);
            $this->showRecsButton->setText(__("Recs",$login));
            $this->showRecsButton->setAction($this->showRecsAction);
            $this->showRecsButton->colorize('2a2');
            $this->showRecsButton->setScale(0.5);
            $this->frame->addComponent($this->showRecsButton);
        }
        if ($this->isAdmin) {
            $this->goButton = new MyButton(26, 5);
            $this->goButton->setText(__("Go now",$login));
            $this->goButton->setAction($this->gotoMap);
            $this->goButton->colorize('aa2');
            $this->goButton->setScale(0.5);
            $this->frame->addComponent($this->goButton);

            $spacer = new \ManiaLib\Gui\Elements\Quad();
            $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
            $spacer->setSize(2, 4);
            $this->frame->addComponent($spacer);

            $this->removeButton = new MyButton(26, 5);
            $this->removeButton->setText('$fff' . __("Remove",$login));
            $this->removeButton->setAction($this->removeMap);
            $this->removeButton->colorize('a22');
            $this->removeButton->setScale(0.5);
            $this->frame->addComponent($this->removeButton);
        }

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }
    
    function destroy() {
        $this->queueButton->destroy();

        if (is_object($this->goButton))
            $this->goButton->destroy();
        if (is_object($this->removeButton))
            $this->removeButton->destroy();
        if (is_object($this->showRecsButton))
            $this->showRecsButton->destroy();
        
        $this->clearComponents();
        parent::destroy();
    }

}
?>

