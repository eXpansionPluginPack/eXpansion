<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class GuestPlayeritem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $removeButton;

    protected $login;
    protected $nickname;

    protected $removeAction;

    protected $frame;

    public function __construct($indexNumber, \Maniaplanet\DedicatedServer\Structures\Player $player, $controller, $login)
    {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->removeAction = $this->createAction(array($controller, 'removeGuestClick'), array($player->login));

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->login = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);

        $this->frame->addComponent($this->login);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->removeButton = new MyButton();
        $this->removeButton->setText(__("Remove"));
        $this->removeButton->setAction($this->removeAction);
        $this->removeButton->setScale(0.6);
        $this->frame->addComponent($this->removeButton);


        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }
}
