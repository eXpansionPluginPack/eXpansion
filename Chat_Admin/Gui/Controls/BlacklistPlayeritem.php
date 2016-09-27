<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class BlacklistPlayeritem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $unblackButton;

    protected $login;

    protected $unblackAction;

    protected $frame;

    protected $bg;

    function __construct($indexNumber, \Maniaplanet\DedicatedServer\Structures\Player $player, $controller, $login)
    {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->unblackAction = $this->createAction(array($controller, 'unBlacklistClick'), array($player->login));

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->login = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);
        $this->frame->addComponent($this->login);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->unblackButton = new MyButton();
        $this->unblackButton->setText(__("Remove"));
        $this->unblackButton->setAction($this->unblackAction);
        $this->frame->addComponent($this->unblackButton);


        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }
}
