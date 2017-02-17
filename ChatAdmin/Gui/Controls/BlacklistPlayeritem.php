<?php
namespace ManiaLivePlugins\eXpansion\ChatAdmin\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as MyButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use Maniaplanet\DedicatedServer\Structures\Player;

class BlacklistPlayeritem extends Control
{

    protected $unblackButton;

    protected $login;

    protected $unblackAction;

    protected $frame;

    protected $bg;
    protected $player;

    public function __construct(
        $indexNumber,
        Player $player,
        $controller,
        $login
    ) {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->unblackAction = $this->createAction(array($controller, 'unBlacklistClick'), array($player->login));

        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);
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
