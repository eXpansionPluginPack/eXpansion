<?php

namespace ManiaLivePlugins\eXpansion\MusicBox\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\OptimizedPagerElement;

class Song extends Control implements OptimizedPagerElement
{

    protected $bg;
    protected $queueButton;
    protected $title;
    protected $artist;
    protected $genre;
    protected $queueSong;
    protected $frame;

    public function __construct($indexNumber, $login, $action)
    {
        $sizeY = 6;
        $sizeX = 140;

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $line = new \ManiaLib\Gui\Layouts\Line();
        $line->setMarginWidth(2);
        $this->frame->setLayout($line);

        $this->title = new \ManiaLib\Gui\Elements\Label(70, 4);
        $this->title->setAlign('left', 'center');
        $this->title->setScale(0.8);
        $this->title->setId('column_' . $indexNumber . '_0');
        $this->title->setAttribute("class", "eXpOptimizedPagerAction");
        $this->frame->addComponent($this->title);

        $this->artist = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->artist->setAlign('left', 'center');
        $this->artist->setScale(0.8);
        $this->artist->setId('column_' . $indexNumber . '_1');
        $this->artist->setAttribute("class", "eXpOptimizedPagerAction");
        $this->frame->addComponent($this->artist);

        $this->genre = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->genre->setAlign('left', 'center');
        $this->genre->setScale(0.8);
        $this->genre->setId('column_' . $indexNumber . '_2');
        $this->genre->setAttribute("class", "eXpOptimizedPagerAction");
        $this->frame->addComponent($this->genre);

        $this->queueButton = new MyButton();
        $this->queueButton->setText(__("Queue", $login));
        $this->queueButton->setAction($action);
        $this->queueButton->setId('column_' . $indexNumber . '_3');
        $this->queueButton->setAttribute("class", "eXpOptimizedPagerAction");
        $this->queueButton->colorize('2a2');
        $this->queueButton->setScriptEvents();
        $this->queueButton->setScale(0.5);
        $this->frame->addComponent($this->queueButton);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->frame->setSize($this->sizeX, $this->sizeY);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }

    /*
     * custom function to remove contents.
     */
    public function erase()
    {
        $this->queueButton->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

    public function getNbTextColumns()
    {
        return 3;
    }

}

