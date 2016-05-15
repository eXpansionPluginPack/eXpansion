<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class Wishitem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $queueButton;
    protected $goButton;
    protected $label;
    protected $time;
    protected $removeMap;
    protected $removeButton;
    protected $frame;

    /**
     *
     * @param type $indexNumber
     * @param type $login
     * @param \ManiaLivePlugins\eXpansion\Maps\Structures\MapWish $map
     * @param type $controller
     * @param type $isAdmin
     * @param type $sizeX
     */
    public function __construct($indexNumber, $login, $map, $controller, $isAdmin, $sizeX)
    {
        $sizeY = 5;

        $this->isAdmin = $isAdmin;
        $this->removeMap = $this->createAction(array($controller, 'dropQueue'), $map->map);
        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Label(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setText(($indexNumber + 1) . ".");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(70, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Gui::fixString(Formatting::stripColors($map->map->name, "999f")));
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);

        $ui = new \ManiaLib\Gui\Elements\Label(20, 4);
        $ui->setAlign('left', 'center');
        $ui->setScale(0.8);
        //$ui->setText($map->authorTime);
        $ui->setText(\ManiaLive\Utilities\Time::fromTM($map->map->goldTime));
        $this->frame->addComponent($ui);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(2, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->time = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setScale(0.8);
        $this->time->setText(Gui::fixString($map->player->nickName));
        //$this->time->setText(\ManiaLive\Utilities\Time::fromTM($map->goldTime));
        $this->frame->addComponent($this->time);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        if ($this->isAdmin || $map->player->login == $login) {
            $this->removeButton = new MyButton(26, 5);
            $this->removeButton->setText('$fff' . __("Drop", $login));
            $this->removeButton->setAction($this->removeMap);
            $this->removeButton->colorize('a22');
            $this->removeButton->setScale(0.5);
            $this->frame->addComponent($this->removeButton);
        }

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

// manialive 3.1 override to do nothing.
    public function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    public function erase()
    {
        if (is_object($this->removeButton))
            $this->removeButton->destroy();

        $this->destroyComponents();
        parent::destroy();
    }

}

