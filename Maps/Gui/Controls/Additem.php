<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class Additem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $bg;

    protected $mapNick;

    protected $addButton;

    protected $deleteButton;

    protected $label;

    protected $time;

    protected $addMapAction;

    protected $deleteActionf;

    protected $deleteAction;

    protected $frame;

    public function __construct($indexNumber, $filename, $controller, $gbx, $login, $sizeX)
    {
        $sizeY = 6;

        try {
            $map = $gbx->read($filename);
        } catch (Exception $e) {
            Helper::log("[Maps/Additem]Error processing file : " . $e->getMessage());

            return;
        }
        $this->addMapAction = $this->createAction(array($controller, 'addMap'), array($filename, $gbx->name));
        $this->deleteActionf = $this->createAction(array($controller, 'deleteMap'), $filename);
        $this->deleteAction = \ManiaLivePlugins\eXpansion\Gui\Gui::createConfirm($this->deleteActionf);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $layout = new \ManiaLib\Gui\Layouts\Line();
        $layout->setMargin(1, 0);
        $this->frame->setLayout($layout);

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("left", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(90, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Gui::fixString($map->name));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $this->mapNick = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->mapNick->setAlign('left', 'center');

        $author = $map->author->login;
        if ($map->author->nickname) {
            $author = $map->author->nickname;
        }
        $this->mapNick->setText(Gui::fixString($author));
        $this->mapNick->setScale(0.8);
        $this->frame->addComponent($this->mapNick);

        $this->time = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText(\ManiaLive\Utilities\Time::fromTM($map->authorTime));
        $this->frame->addComponent($this->time);

        $this->time = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText($map->environment);
        $this->frame->addComponent($this->time);

        $this->time = new \ManiaLib\Gui\Elements\Label(16, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText(isset($map->playerModel) ? $map->playerModel : "");
        $this->frame->addComponent($this->time);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);


        $this->addButton = new MyButton(24, 5);
        $this->addButton->setText(__("Add", $login));
        $this->addButton->setAction($this->addMapAction);
        $this->addButton->setScale(0.5);
        $this->addButton->colorize("2a2");
        $this->frame->addComponent($this->addButton);

        $this->deleteButton = new MyButton(24, 5);
        $this->deleteButton->setAction($this->deleteAction);
        $this->deleteButton->setScale(0.5);
        $this->deleteButton->setText('$ff0' . __("Delete", $login));
        $this->deleteButton->colorize("222");
        $this->frame->addComponent($this->deleteButton);

        $this->addComponent($this->frame);
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    // manialive 3.1 override to do nothing.
    public function destroy()
    {


    }

    /*
     * custom function to remove contents.
     */
    public function erase()
    {
        ActionHandler::getInstance()->deleteAction($this->deleteAction);
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->addButton->destroy();
        $this->deleteButton->destroy();

        $this->destroyComponents();

        $this->destroy();
        parent::destroy();
    }

}
