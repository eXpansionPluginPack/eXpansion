<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Control;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use Maniaplanet\DedicatedServer\Structures\Player;

class IgnoredPlayeritem extends Control
{

    protected $unignoreButton;
    protected $login;
    protected $unignoreAction;
    protected $frame;

    public function __construct($indexNumber, Player $player, $controller, $login) {
        $sizeX = 80;
        $sizeY = 6;
        $this->player = $player;

        $this->unignoreAction = $this->createAction(array($controller, 'unignore'), array($player->login));
        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());

        $this->login = new Label(50, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);
        $this->login->setScale(0.8);
        $this->frame->addComponent($this->login);


        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->unignoreButton = new Button();
        $this->unignoreButton->setText(__("Remove"));
        $this->unignoreButton->setAction($this->unignoreAction);
        $this->frame->addComponent($this->unignoreButton);


        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    public function destroy() {
        $this->unignoreButton->destroy();
        parent::destroy();
    }

}
