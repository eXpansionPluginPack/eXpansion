<?php

namespace ManiaLivePlugins\eXpansion\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class Playeritem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $sendButton;
    private $login;
    private $nickname;
    private $sendAction;
    private $frame;

    public function __construct($indexNumber, \ManiaLive\Data\Player $player, $callback, $text)
    {
        $sizeX = 60;
        $sizeY = 6;
        $this->player = $player;

        $this->sendAction = \ManiaLive\Gui\ActionHandler::getInstance()->createAction($callback, $player->login);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons64x64_1");
        $spacer->setSubStyle("Buddy");
        $this->frame->addComponent($spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->login = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);
        $this->login->setScale(0.8);
        $this->frame->addComponent($this->login);

        $this->nickname = new \ManiaLib\Gui\Elements\Label(40, 4);
        $this->nickname->setAlign('left', 'center');
        $this->nickname->setScale(0.8);
        $this->nickname->setText($player->nickName);
        $this->frame->addComponent($this->nickname);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

        $this->sendButton = new MyButton(20, 6);
        $this->sendButton->setAction($this->sendAction);
        $this->sendButton->setScale(0.6);
        $this->sendButton->setText($text);

        $this->frame->addComponent($this->sendButton);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
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
        $this->sendButton->destroy();
        parent::destroy();
    }
}
