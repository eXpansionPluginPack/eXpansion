<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ESLcup\Gui\Controls;

/**
 * Description of CupScoreTableItem
 *
 * @author Reaby
 */
class CupScoreTableItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $bg, $bgg, $quit, $rank, $frame, $nickname, $score, $finalist, $winner;

    public function __construct($index, \ManiaLivePlugins\eXpansion\ESLcup\Structures\CupScore $score, $place)
    {
        $this->setSize(80, 10);

        $this->bgg = new \ManiaLib\Gui\Elements\Quad(80, 10);
        $this->bgg->setPosition(0, 0);
        $this->bgg->setBgcolor("0006");
        if ($score->finalist)
            $this->bgg->setBgcolor("f006");
        if ($score->hasWin)
            $this->bgg->setBgcolor("0f06");
        $this->addComponent($this->bgg);


        $this->bg = new \ManiaLib\Gui\Elements\Quad(10, 10);
        $this->bg->setPosition(0, 0);
        $this->bg->setBgcolor("000");
        if ($score->isConnected == false) {
            $this->bg->setBgcolor("444");
        }
        $this->addComponent($this->bg);

        $this->quit = new \ManiaLib\Gui\Elements\Quad(4, 4);
        $this->quit->setPosition(5, 5);
        $this->quit->setStyle("Icons64x64_1");
        $this->quit->setSubStyle("QuitRace");
        $this->quit->setPosition(5, -5);
        if ($score->isConnected == false) {
            $this->addComponent($this->quit);
        }

        $this->addComponent($this->bg);


        $this->rank = new \ManiaLib\Gui\Elements\Label(10, 5);
        $this->rank->setText($index);
        $this->rank->setTextColor("fff");
        $this->rank->setTextSize(3);
        $this->rank->setPosition(2.5, -2.5);
        $this->addComponent($this->rank);

        $this->nickname = new \ManiaLib\Gui\Elements\Label(50);
        $this->nickname->setPosition(14, -2.5);
        $this->nickname->setTextColor("fff");
        $this->nickname->setTextSize(3);
        $this->nickname->setText($score->nickName);
        $this->addComponent($this->nickname);

        $this->score = new \ManiaLib\Gui\Elements\Label(10);
        $this->score->setPosition(75, -3.5);
        $this->score->setTextSize(1);
        $this->score->setScale(1.2);
        $this->score->setTextColor("fff");
        $this->score->setAlign("center", "top");
        $this->score->setText($score->score);
        $this->addComponent($this->score);

        $this->place = new \ManiaLib\Gui\Elements\Label(10);
        $this->place->setPosition(70, 0);
        $this->place->setTextSize(1);
        $this->place->setScale(1.2);
        $this->place->setTextColor("fff");

        if ($place > 0) {
            switch ($place) {
                case 1:
                    $this->place->setText("Winner");
                    break;
                case 2:
                    $this->place->setText("2nd");
                    break;
                case 3:
                    $this->place->setText("3rd");
                    break;
                default:
                    $this->place->setText("");
                    break;
            }

            $this->addComponent($this->place);
        }
    }
}
