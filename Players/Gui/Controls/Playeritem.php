<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Controls;

use ManiaLivePlugins\eXpansion\Players\Gui\Windows\Playerlist;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class Playeritem extends \ManiaLive\Gui\Control {

    protected $bg;
    protected $forceButton;
    protected $ignoreButton;
    protected $kickButton;
    protected $banButton;
    protected $blacklistButton;
    protected $login;
    protected $nickname;
    private $ignoreAction;
    private $kickAction;
    private $banAction;
    private $blacklistAction;
    private $forceAction;
    protected $frame;
    private $recipient;
    private $widths;
    protected $team;
    protected $icon;
    private $toggleTeam = null;

    function __construct($indexNumber, \DedicatedApi\Structures\Player $player, $controller, $isAdmin, $login, $widths, $sizeX) {
        $this->recipient = $login;
        $this->widths = \ManiaLivePlugins\eXpansion\Gui\Gui::getScaledSize($widths, $sizeX);

        $sizeY = 4;
        $this->isAdmin = $isAdmin;
        $this->player = $player;
        if ($isAdmin) {
            $this->ignoreAction = $this->createAction(array($controller, 'ignorePlayer'), $player->login);
            $this->kickAction = $this->createAction(array($controller, 'kickPlayer'), $player->login);
            $this->banAction = $this->createAction(array($controller, 'banPlayer'), $player->login);
            $this->blacklistAction = $this->createAction(array($controller, 'blacklistPlayer'), $player->login);
            $this->forceAction = $this->createAction(array($controller, 'toggleSpec'), $player->login);
            $this->toggleTeam = $this->createAction(array($controller, 'toggleTeam'), $player->login);
        }

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);


        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->team = new \ManiaLib\Gui\Elements\Quad();
        $this->team->setSize(4, 4);
        $this->team->setAlign("center", "center2");
        $this->team->setStyle("Icons64x64_1");
        $this->team->setSubStyle("Empty");
        if ($player->teamId === 0) {
            $this->team->setStyle("BgRaceScore2");
            $this->team->setSubStyle("HandleBlue");
            $this->team->setAction($this->toggleTeam);
        }
        if ($player->teamId === 1) {
            $this->team->setStyle("BgRaceScore2");
            $this->team->setSubStyle("HandleRed");
            $this->team->setAction($this->toggleTeam);
        }

        $this->frame->addComponent($this->team);

        $this->icon = new \ManiaLib\Gui\Elements\Quad();
        $this->icon->setSize(4, 4);
        $this->icon->setAlign("center", "center2");

        if ($player->spectator == 1) {
            $this->icon->setStyle("Icons64x64_1");
            $this->icon->setSubStyle("Camera");
        } else {
            $this->icon->setStyle("Icons64x64_1");
            $this->icon->setSubStyle("Buddy");
        }

        $this->frame->addComponent($this->icon);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
//$this->frame->addComponent($spacer);


        $this->nickname = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->nickname->setAlign('left', 'center');
        $this->nickname->setScale(0.8);
        $this->nickname->setText($player->nickName);
        $this->frame->addComponent($this->nickname);

        $this->login = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setText($player->login);
        $this->login->setScale(0.8);
        $this->frame->addComponent($this->login);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

// admin additions
        if ($this->isAdmin) {

            if(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'player_ignore')){
                $this->ignoreButton = new MyButton(7, 5);
                $this->ignoreButton->setDescription(__('Ignore player %1$s ', $login, $player->login), 50);
                $this->ignoreButton->setTextColor("fff");
                $this->ignoreButton->colorize("a22");
                $this->ignoreButton->setScale(0.7);
                $this->ignoreButton->setAction($this->ignoreAction);
                $this->ignoreButton->setIcon('Icons128x128_1', 'Easy');
                $this->frame->addComponent($this->ignoreButton);
            }

            if(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'player_kick')){
                $this->kickButton = new MyButton(7, 5);
                $this->kickButton->setDescription(__('Kick player %1$s ', $login, $player->login), 50);
                $this->kickButton->setTextColor("fff");
                $this->kickButton->setAction($this->kickAction);
                $this->kickButton->colorize("a22");
                $this->kickButton->setScale(0.7);
                $this->kickButton->setIcon('Icons128x128_1', 'Medium');
                $this->frame->addComponent($this->kickButton);
            }

            if(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'player_ban')){
                $this->banButton = new MyButton(7, 5);
                $this->banButton->setDescription(__('Ban player %1$s ', $login, $player->login), 50);
                $this->banButton->setTextColor("fff");
                $this->banButton->colorize("a22");
                $this->banButton->setAction($this->banAction);
                $this->banButton->setScale(0.7);
                $this->banButton->setIcon('Icons128x128_1', 'Hard');
                $this->frame->addComponent($this->banButton);
            }

            if(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'player_black')){
                $this->blacklistButton = new MyButton(7, 5);
                $this->blacklistButton->setDescription(__('Blacklist player %1$s ', $login, $player->login), 50);
                $this->blacklistButton->setTextColor("fff");
                $this->blacklistButton->colorize("a22");
                $this->blacklistButton->setAction($this->blacklistAction);
                $this->blacklistButton->setScale(0.7);
                $this->blacklistButton->setIcon('Icons128x128_1', 'Extreme');
                $this->frame->addComponent($this->blacklistButton);
            }

            if(\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, 'player_spec')){
                $this->forceButton = new MyButton(6, 5);
                $this->forceButton->setAction($this->forceAction);
                $this->forceButton->setScale(0.7);
                $this->forceButton->colorize("2f2");
                $this->forceButton->setIcon('Icons64x64_1', 'Opponents');
                 $this->forceButton->setDescription(__('Force %1$s to play', $this->recipient, $this->recipient),50);
                $this->frame->addComponent($this->forceButton);
                }
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
        $this->login->setSizeX($this->widths[1]);
        $this->nickname->setSizeX($this->widths[2]);

        if ($this->forceButton != null) {
            if ($this->player->spectator == 1) {
                $this->forceButton->setIcon('Icons64x64_1', 'Opponents');
                $this->forceButton->setDescription(__('Force %1$s to play', $this->recipient, $this->recipient),50);
            } else {
                 $this->forceButton->setIcon('BgRaceScore2', 'Spectator');
                $this->forceButton->setDescription(__('Force %1$s to spectate', $this->recipient, $this->recipient),50);
            }
        }
    }

// manialive 3.1 override to do nothing.
    function destroy() {
        
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
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

