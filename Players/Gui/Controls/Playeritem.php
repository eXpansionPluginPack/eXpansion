<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class Playeritem extends \ManiaLive\Gui\Control {

    private $bg;
    private $forceButton;
    private $kickButton;
    private $banButton;
    private $login;
    private $nickname;
    private $kickAction;
    private $banAction;
    private $forceAction;
    private $frame;

    function __construct($indexNumber, \DedicatedApi\Structures\Player $player, $controller, $isAdmin, $sizeX) {
        $sizeY = 4;
        $this->isAdmin = $isAdmin;
        $this->player = $player;
        if ($isAdmin) {
            $this->kickAction = $this->createAction(array($controller, 'kickPlayer'), $player->login);
            $this->banAction = $this->createAction(array($controller, 'banPlayer'), $player->login);
            $this->forceAction = $this->createAction(array($controller, 'toggleSpec'), $player->login);
        }
        
        $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
        $this->bg->setAlign('left', 'center');
        if ($indexNumber % 2 == 0) {
            $this->bg->setBgcolor('aaa4');
        } else {
            $this->bg->setBgcolor('7774');
        }        
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

        $this->login = new \ManiaLib\Gui\Elements\Label(20, 4);
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

            $this->banButton = new MyButton(24, 5);
            $this->banButton->setText(__("Ban"));
            $this->banButton->setTextColor("fff");
            $this->banButton->colorize("a22");
            $this->banButton->setAction($this->banAction);
            $this->banButton->setScale(0.5);
            $this->frame->addComponent($this->banButton);

            $this->kickButton = new MyButton(24, 5);
            $this->kickButton->setText(__("Kick"));
            $this->kickButton->setTextColor("fff");
            $this->kickButton->setAction($this->kickAction);
            $this->kickButton->colorize("a22");
            $this->kickButton->setScale(0.5);
            $this->frame->addComponent($this->kickButton);

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
                $this->forceButton->setText(__("Release Spec"));
            } else {
                $this->forceButton->setText(__("Force Spec"));
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

        $this->clearComponents();

        parent::destroy();
    }

}
?>

