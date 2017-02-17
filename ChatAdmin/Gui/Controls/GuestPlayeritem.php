<?php
namespace ManiaLivePlugins\eXpansion\ChatAdmin\Gui\Controls;


use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use Maniaplanet\DedicatedServer\Structures\Player;


class GuestPlayeritem extends Control
{

    protected $bg;
    protected $removeButton;

    protected $login;
    protected $nickname;

    protected $removeAction;

    protected $frame;
    protected $player;

    public function __construct(
        $indexNumber,
        Player $player,
        $controller,
        $login
    )
    {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->removeAction = $this->createAction(array($controller, 'removeGuestClick'), array($player->login));

        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());

        $this->login = new Label(20, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);

        $this->frame->addComponent($this->login);


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->removeButton = new Button();
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
