<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\ActionHandler;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;


class MatchSettingsFile extends Control
{
    /** @var Frame */
    protected $frame;

    /** @var ListBackGround */
    protected $bg;

    /** @var Button */
    protected $saveButton;
    /** @var Button */
    protected $loadButton;
    /** @var Button */
    protected $deleteButton;
    /** @var Label */
    protected $label;

    protected $saveAction;

    protected $loadAction;

    protected $deleteActionf;
    protected $deleteAction;

    protected $deleteButtonf;


    /**
     * MatchSettingsFile constructor.
     * @param $indexNumber
     * @param $filename
     * @param $controller
     * @param $login
     * @param $sizeX
     */
    public function __construct($indexNumber, $filename, $controller, $login, $sizeX)
    {
        $sizeY = 6;
        $this->saveAction = $this->createAction(array($controller, 'saveSettings'), $filename);
        $this->loadAction = $this->createAction(array($controller, 'loadSettings'), $filename);

        $this->deleteActionf = ActionHandler::getInstance()
            ->createAction(array($controller, "deleteSetting"), $filename);
        $this->deleteAction = Gui::createConfirm($this->deleteActionf);

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons128x128_1");
        $spacer->setSubStyle("Challenge");
        $this->frame->addComponent($spacer);

        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new Label(75, 4);
        $this->label->setAlign('left', 'center');
        $file = explode(DIRECTORY_SEPARATOR, $filename);
        $text = utf8_encode(end($file));
        //$text = str_replace(".txt", "", $text);
        $this->label->setText($text);
        $this->label->setTextSize(1);
        $this->label->setStyle("TextCardSmallScores2");
        $this->label->setTextColor("fff");
        $this->frame->addComponent($this->label);


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);


        $this->loadButton = new Button();
        $this->loadButton->setText(__("Load", $login));
        $this->loadButton->setAction($this->loadAction);
        $this->loadButton->setScale(0.6);

        $this->frame->addComponent($this->loadButton);

        $this->saveButton = new Button();
        $this->saveButton->setText(__("Save", $login));
        $this->saveButton->setAction($this->saveAction);
        $this->saveButton->colorize("0d0");
        $this->saveButton->setScale(0.6);
        $this->frame->addComponent($this->saveButton);

        $this->deleteButton = new Button();
        $this->deleteButton->setText(__("Delete", $login));
        $this->deleteButton->colorize("d00");
        $this->deleteButton->setAction($this->deleteAction);
        $this->deleteButton->setScale(0.6);
        $this->frame->addComponent($this->deleteButton);

        $this->addComponent($this->frame);

        $this->loadButton->setVisibility(AdminGroups::hasPermission($login, Permission::GAME_MATCH_SETTINGS));
        $this->saveButton->setVisibility(AdminGroups::hasPermission($login, Permission::GAME_MATCH_SAVE));
        $this->deleteButton->setVisibility(AdminGroups::hasPermission($login, Permission::GAME_MATCH_DELETE));

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX + 2, $this->sizeY);
        $this->bg->setPosX(0);

        $this->frame->setPosX(2);
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
        ActionHandler::getInstance()->deleteAction($this->deleteActionf);
        $this->saveButton->destroy();
        $this->loadButton->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();

        parent::destroy();
    }
}
