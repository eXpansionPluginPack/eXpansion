<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;

class Song extends \ManiaLivePlugins\eXpansion\Gui\Control {

    private $bg;
    private $queueButton;
    private $title;
    private $artist;
    private $queueSong;
    private $frame;

    function __construct($indexNumber, $login, \ManiaLivePlugins\eXpansion\MusicBox\Structures\Song $song, $controller, $sizeX) {
        $sizeY = 6;

        $this->queueSong = $this->createAction(array($controller, 'queueSong'), $indexNumber + 1);
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

        $this->title = new \ManiaLib\Gui\Elements\Label(70, 4);
        $this->title->setAlign('left', 'center');
        $this->title->setText($song->title);
        $this->title->setScale(0.8);
        $this->frame->addComponent($this->title);

        $this->artist = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->artist->setAlign('left', 'center');
        $this->artist->setScale(0.8);
        $this->artist->setText($song->artist);
        $this->frame->addComponent($this->artist);

        $ui = new \ManiaLib\Gui\Elements\Label(20, 4);
        $ui->setAlign('left', 'center');
        $ui->setScale(0.8);
        $ui->setText($song->genre);
        $this->frame->addComponent($ui);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->queueButton = new MyButton(26, 5);
        $this->queueButton->setText(__("Queue"));
        $this->queueButton->setAction($this->queueSong);
        $this->queueButton->colorize('2a2');
        $this->queueButton->setScale(0.5);
        $this->frame->addComponent($this->queueButton);

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

    /*
     * custom function to remove contents.
     */
    function erase() {
        $this->queueButton->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}
?>

