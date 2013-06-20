<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Controls;

use ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class Playeritem extends \ManiaLive\Gui\Control {

    private $bg;
    private $forceButton;
    private $ignoreButton;
    private $kickButton;
    private $banButton;
    private $blacklistButton;
    private $login;
    private $nickname;
    private $ignoreAction;
    private $kickAction;
    private $banAction;
    private $blacklistAction;
    private $forceAction;
    private $frame;
    private $recipient;

    function __construct($indexNumber, \DedicatedApi\Structures\Player $player, $controller, $isAdmin, $login, $sizeX) {
        $this->recipient = $login;


        $sizeY = 4;
        $this->isAdmin = $isAdmin;
        $this->player = $player;
        if ($isAdmin) {
            $this->ignoreAction = $this->createAction(array($controller, 'ignorePlayer'), $player->login);
            $this->kickAction = $this->createAction(array($controller, 'kickPlayer'), $player->login);
            $this->banAction = $this->createAction(array($controller, 'banPlayer'), $player->login);
            $this->blacklistAction = $this->createAction(array($controller, 'blacklistPlayer'), $player->login);
            $this->forceAction = $this->createAction(array($controller, 'toggleSpec'), $player->login);
        }

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);


        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setAlign("center", "center2");
        $spacer->setStyle("Icons64x64_1");

        if ($player->forceSpectator == 1 || $player->isSpectator)
            $spacer->setSubStyle("Camera");
        else
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

        $this->nickname = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->nickname->setAlign('left', 'center');
        $this->nickname->setScale(0.8);
        $this->nickname->setText($player->nickName);
        $this->frame->addComponent($this->nickname);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

// admin additions
        if ($this->isAdmin) {

            $this->ignoreButton = new MyButton(24, 5);
            $this->ignoreButton->setText(__("Ignore", $login));
            $this->ignoreButton->setTextColor("fff");
            $this->ignoreButton->colorize("a22");
            $this->ignoreButton->setScale(0.5);
            $this->ignoreButton->setAction($this->ignoreAction);
            $this->frame->addComponent($this->ignoreButton);

            $this->kickButton = new MyButton(24, 5);
            $this->kickButton->setText(__("Kick", $login));
            $this->kickButton->setTextColor("fff");
            $this->kickButton->setAction($this->kickAction);
            $this->kickButton->colorize("a22");
            $this->kickButton->setScale(0.5);
            $this->frame->addComponent($this->kickButton);

            $this->banButton = new MyButton(24, 5);
            $this->banButton->setText(__("Ban", $login));
            $this->banButton->setTextColor("fff");
            $this->banButton->colorize("a22");
            $this->banButton->setAction($this->banAction);
            $this->banButton->setScale(0.5);
            $this->frame->addComponent($this->banButton);

            $this->blacklistButton = new MyButton(24, 5);
            $this->blacklistButton->setText(__("Blacklist", $login));
            $this->blacklistButton->setTextColor("fff");
            $this->blacklistButton->colorize("a22");
            $this->blacklistButton->setAction($this->blacklistAction);
            $this->blacklistButton->setScale(0.5);
            $this->frame->addComponent($this->blacklistButton);

            $this->forceButton = new MyButton(24, 5);
            $this->forceButton->setAction($this->forceAction);
            $this->forceButton->setScale(0.5);
            $this->forceButton->colorize("2f2");
            $this->frame->addComponent($this->forceButton);
        }

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->frame->setSize($this->sizeX, $this->sizeY);
        $this->bg->setPosX(-2);
        $this->bg->setSize($this->sizeX, $this->sizeY);
        if ($this->isAdmin) {
            if ($this->player->forceSpectator == 1 || $this->player->isSpectator) {
                $this->forceButton->setText(__("Release Spec", $this->recipient));
            } else {
                $this->forceButton->setText(__("Force Spec", $this->recipient));
            }
        }
    }

    function onDraw() {
        
    }

    function destroy() {
        if (is_object($this->banButton))
            $this->banButton->destroy();
        if (is_object($this->forceButton))
            $this->forceButton->destroy();
        if (is_object($this->kickButton))
            $this->kickButton->destroy();
        if (is_object($this->blacklistButton))
            $this->blacklistButton->destroy();
        if (is_object($this->ignoreButton))
            $this->ignoreButton->destroy();

        $this->clearComponents();

        parent::destroy();
    }

}
?>

