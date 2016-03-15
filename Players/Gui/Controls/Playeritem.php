<?php

namespace ManiaLivePlugins\eXpansion\Players\Gui\Controls;

use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Player;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as MyButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Structures\OptimizedPagerElement;

class Playeritem extends Control implements OptimizedPagerElement
{
    protected $bg;
    protected $forceButton;
    protected $ignoreButton;
    protected $kickButton;
    protected $banButton;
    protected $blacklistButton;
    protected $login;
    protected $nickname;
    protected $ignoreAction;
    protected $kickAction;
    protected $banAction;
    protected $blacklistAction;
    protected $forceAction;
    protected $guestAction;
    protected $frame;
    protected $recipient;
    protected $widths;
    protected $team;
    protected $icon;
    protected $toggleTeam = null;

    /** @var Player */
    protected $player;
    protected $columnCount = 1;

    function __construct($indexNumber, $login, $action)
    {
        $this->recipient = $login;
        $sizeY           = 6;
        $sizeX           = 120;
        $this->bg        = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);


        $this->frame = new Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new Line());

        $this->nickname = new Label(50, 4);
        $this->nickname->setAlign('left', 'center');
        $this->nickname->setId('column_'.$indexNumber.'_0');
        $this->nickname->setScriptEvents();
        $this->frame->addComponent($this->nickname);

        $this->login = new Label(30, 4);
        $this->login->setAlign('left', 'center');
        $this->login->setId('column_'.$indexNumber.'_1');
        $this->frame->addComponent($this->login);

        $spacer = new Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);

// admin additions
        if (AdminGroups::isInList($login)) {
            if (AdminGroups::hasPermission($login, Permission::player_ignore)) {
                $this->ignoreButton = new MyButton(7, 5);
                $this->ignoreButton->setDescription(__('Ignore player', $login), 50);
                $this->ignoreButton->setTextColor("fff");
                $this->ignoreButton->colorize("a22");
                $this->ignoreButton->setAction($action);
                if ($ignored) {
                    $this->ignoreButton->setDescription(__('UnIgnore player', $login), 50);
                    $this->ignoreButton->setIcon('Icons128x128_1', 'Beginner');
                } else $this->ignoreButton->setIcon('Icons128x128_1', 'Easy');
                $this->ignoreButton->setId('column_'.$indexNumber.'_2');
                $this->ignoreButton->setClass("eXpOptimizedPagerAction");
                $this->columnCount++;
                $this->frame->addComponent($this->ignoreButton);
            }

            if (AdminGroups::hasPermission($login, Permission::player_kick)) {
                $this->kickButton = new MyButton(7, 5);
                $this->kickButton->setDescription(__('Kick player', $login), 50);
                $this->kickButton->setTextColor("fff");
                $this->kickButton->colorize("a22");
                $this->kickButton->setAction($action);
                $this->kickButton->setIcon('Icons128x128_1', 'Medium');
                $this->kickButton->setId('column_'.$indexNumber.'_3');
                $this->kickButton->setClass("eXpOptimizedPagerAction");

                $this->columnCount++;

                $this->frame->addComponent($this->kickButton);
            }

            if (AdminGroups::hasPermission($login, Permission::player_ban)) {
                $this->banButton = new MyButton(7, 5);
                $this->banButton->setDescription(__('Ban player', $login), 50);
                $this->banButton->setTextColor("fff");
                $this->banButton->colorize("a22");
                $this->banButton->setAction($action);
                $this->banButton->setIcon('Icons128x128_1', 'Hard');
                $this->banButton->setId('column_'.$indexNumber.'_4');
                $this->banButton->setClass("eXpOptimizedPagerAction");
                $this->columnCount++;

                $this->frame->addComponent($this->banButton);
            }

            if (AdminGroups::hasPermission($login, Permission::player_black)) {
                $this->blacklistButton = new MyButton(7, 5);
                $this->blacklistButton->setDescription(__('Blacklist player', $login), 50);
                $this->blacklistButton->setTextColor("fff");
                $this->blacklistButton->colorize("a22");
                $this->blacklistButton->setAction($this->blacklistAction);
                $this->blacklistButton->setIcon('Icons128x128_1', 'Extreme');
                $this->blacklistButton->setId('column_'.$indexNumber.'_5');

                $this->blacklistButton->setClass("eXpOptimizedPagerAction");
                $this->columnCount++;
                $this->frame->addComponent($this->blacklistButton);
            }

            if (AdminGroups::hasPermission($login, Permission::player_forcespec)) {
                $this->forceButton = new MyButton(6, 5);
                $this->forceButton->setAction($action);
                $this->forceButton->colorize("2f2");
                if ($this->player->spectator == 1) {
                    $this->forceButton->setIcon('Icons64x64_1', 'Opponents');
                    $this->forceButton->setDescription(__('Force to play', $login), 50);
                } else {
                    $this->forceButton->setIcon('BgRaceScore2', 'Spectator');
                    $this->forceButton->setDescription(__('Force to spectate', $login), 50);
                }
                $this->forceButton->setId('column_'.$indexNumber.'_6');

                $this->forceButton->setClass("eXpOptimizedPagerAction");
                $this->columnCount++;
                $this->frame->addComponent($this->forceButton);
            }

            if (AdminGroups::hasPermission($login, Permission::player_guest)) {
                $this->guestButton = new MyButton(6, 5);
                $this->guestButton->setAction($action);
                $this->guestButton->colorize("2f2");
                $this->guestButton->setIcon('Icons128x128_1', 'Buddies');
                $this->guestButton->setDescription(__('Add to guest list', $login), 50);
                $this->guestButton->setId('column_'.$indexNumber.'_7');

                $this->guestButton->setClass("eXpOptimizedPagerAction");
                $this->columnCount++;
                $this->frame->addComponent($this->guestButton);
            }
        }

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->frame->setSize($this->getSizeX(), $this->getSizeY());
        $this->bg->setSize($this->getSizeX() + 4, $this->getSizeY());
    }

    function destroy()
    {
        if (is_object($this->banButton)) $this->banButton->destroy();
        if (is_object($this->forceButton)) $this->forceButton->destroy();
        if (is_object($this->kickButton)) $this->kickButton->destroy();
        if (is_object($this->blacklistButton)) $this->blacklistButton->destroy();
        if (is_object($this->ignoreButton)) $this->ignoreButton->destroy();
        if (is_object($this->guestButton)) $this->guestButton->destroy();

        $this->destroyComponents();

        parent::destroy();
    }

    public function getNbTextColumns()
    {
        return 2;
    }
}
?>

