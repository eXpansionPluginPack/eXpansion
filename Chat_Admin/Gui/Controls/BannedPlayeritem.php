<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class BannedPlayeritem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg;
    protected $unbanButton;
    protected $login;
    protected $nickname;
    protected $unbanAction;
    protected $frame;

    public function __construct($indexNumber, \Maniaplanet\DedicatedServer\Structures\PlayerBan $player, $controller, $login)
    {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->unbanAction = $this->createAction(array($controller, 'unbanClick'), array($player->login));

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

        $this->unbanButton = new MyButton();
        $this->unbanButton->setText(__("Remove"));
        $this->unbanButton->setAction($this->unbanAction);
        $this->frame->addComponent($this->unbanButton);


        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

}

