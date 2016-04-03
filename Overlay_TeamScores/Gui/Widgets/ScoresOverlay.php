<?php

namespace ManiaLivePlugins\eXpansion\Overlay_TeamScores\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Overlay_TeamScores\Config;

class ScoresOverlay extends \ManiaLive\Gui\Window
{

    protected $background;
    protected $team1;
    protected $team2;
    protected $score1;
    protected $score2;
    protected $inputbox;
    protected $xml;
    protected $button, $reset;
    public static $status = false;
    public static $resetAction, $toggleAction;
    public static $action, $action2;

    protected function onConstruct()
    {
        $this->setPosition(0, 80);
        $this->setAlign("center", "top");

        $this->background = new \ManiaLib\Gui\Elements\Quad();
        $this->background->setImage(Config::getInstance()->imageurl, true);

        $this->background->setSize(110 * 1.5, 12 * 1.5);
        $this->background->setAlign("center", "top");
        $this->addComponent($this->background);

        $this->team1 = new \ManiaLib\Gui\Elements\Label(60, 8);
        $this->team1->setStyle("TextRankingsBig");
        $this->team1->setAlign("right", "center");
        $this->team1->setTextSize(3);
        $this->team1->setTextPrefix('$s');
        $this->team1->setPosition(-25, -7);
        $this->team1->setScriptEvents();
        $this->team1->setId("team1");
        $this->addComponent($this->team1);

        $this->team2 = new \ManiaLib\Gui\Elements\Label(60, 8);
        $this->team2->setStyle("TextRankingsBig");
        $this->team2->setAlign("left", "center");
        $this->team2->setTextSize(3);
        $this->team2->setTextPrefix('$s');
        $this->team2->setPosition(25, -7);
        $this->team2->setScriptEvents();
        $this->team2->setId("team2");
        $this->addComponent($this->team2);

        $this->score1 = new \ManiaLib\Gui\Elements\Label(20, 7);
        $this->score1->setStyle("TextRaceChrono");
        $this->score1->setAlign("right", "top");
        $this->score1->setPosition(-7, -5);
        $this->addComponent($this->score1);

        $this->score2 = new \ManiaLib\Gui\Elements\Label(20, 7);
        $this->score2->setStyle("TextRaceChrono");
        $this->score2->setAlign("left", "top");
        $this->score2->setPosition(7, -5);
        $this->addComponent($this->score2);

        $this->team1->setStyle("TextTitle2");
        $this->team2->setStyle("TextTitle2");
        $this->team1->setAction(self::$action);
        $this->team2->setAction(self::$action2);

        if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($this->getRecipient(), \ManiaLivePlugins\eXpansion\AdminGroups\Permission::game_settings)) {
            $this->button = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $this->button->colorize("0000");
            $this->button->setAlign("center", "center");
            $this->button->setPosition(-10, -($this->background->getSizeY()));
            $this->button->setAction(self::$toggleAction);
            $this->addComponent($this->button);

            $this->reset = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
            $this->reset->setText('$dddReset');
            $this->reset->colorize("000");
            $this->reset->setAlign("center", "center");
            $this->reset->setPosition(10, -($this->background->getSizeY()));
            $this->reset->setAction(self::$resetAction);
            $this->addComponent($this->reset);
        }
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Overlay_TeamScores\Structures\Team[] $teams
     */
    function setData($teams)
    {
        $this->team1->setText($teams[1]->name);
        $this->team2->setText($teams[0]->name);

        $this->score1->setText($teams[1]->score);
        $this->score2->setText($teams[0]->score);
    }

    function setEnable()
    {
        $this->team1->setStyle("TextTitle2Blink");
        $this->team2->setStyle("TextTitle2Blink");
        $this->team2->setAction(self::$action);
        $this->team1->setAction(self::$action2);
    }

    function setDisable()
    {
        $this->team1->setStyle("TextRankingsBig");
        $this->team2->setStyle("TextRankingsBig");
        $this->team1->setAction(null);
        $this->team2->setAction(null);
    }

    protected function onDraw()
    {
        $this->setDisable();
        if (is_object($this->button)) {
            $this->button->setText('$dddEnable');
        }
        if (self::$status == true) {
            $this->setEnable();
            if (is_object($this->button)) {
                $this->button->setText('$dddDisable');
            }
        }
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target)
    {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    function destroy()
    {
        parent::destroy();
    }

}

?>
