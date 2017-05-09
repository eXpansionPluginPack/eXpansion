<?php

namespace ManiaLivePlugins\eXpansion\ChatAdmin\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as MyButton;
use Maniaplanet\DedicatedServer\Structures\PlayerBan;

class BannedPlayeritem extends Control
{

    protected $bg;
    protected $unbanButton;
    protected $login;
    protected $nickname;
    protected $unbanAction;
    protected $frame;
    protected $player;

    /**
     * BannedPlayeritem constructor.
     * @param $indexNumber
     * @param PlayerBan $player
     * @param $controller
     * @param $login
     */
    public function __construct(
        $indexNumber,
        PlayerBan $player,
        $controller,
        $login
    )
    {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->unbanAction = $this->createAction(array($controller, 'unbanClick'), array($player->login));

        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());

        $this->login = new Label(50, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);

        $this->frame->addComponent($this->login);

        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

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
