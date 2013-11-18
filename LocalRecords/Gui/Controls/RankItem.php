<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of RecItem
 *
 * @author oliverde8
 */
class RankItem extends \ManiaLive\Gui\Control {

    private $label_rank, $label_nick, $label_wins, $label_score, $label_finish, $label_lastRec;
    private $bg;
    private $widths;

    function __construct($indexNumber, $login, $rank, $widths) {
        $this->widths = $widths;
        $this->sizeY = 4;
        $this->bg = new ListBackGround($indexNumber, 100, 4);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(100, 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_rank->setAlign('left', 'center');
        $this->label_rank->setScale(0.8);
        $this->label_rank->setText(($indexNumber + 1) . ".");
        $this->frame->addComponent($this->label_rank);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label(10., 4);
        $this->label_nick->setAlign('left', 'center');
        $this->label_nick->setScale(0.8);
        $this->label_nick->setText($rank->player_nickname);
        $this->frame->addComponent($this->label_nick);

        $this->label_wins = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_wins->setAlign('left', 'center');
        $this->label_wins->setScale(0.8);
        $this->label_wins->setText($rank->player_wins);
        $this->frame->addComponent($this->label_wins);

        $this->label_score = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_score->setAlign('left', 'center');
        $this->label_score->setScale(0.8);
        $this->label_score->setText($rank->tscore);
        $this->frame->addComponent($this->label_score);

        $this->label_finish = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_finish->setAlign('left', 'center');
        $this->label_finish->setScale(0.8);
        $this->label_finish->setText($rank->nbFinish);
        $this->frame->addComponent($this->label_finish);

        $this->label_nbRecords = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_nbRecords->setAlign('left', 'center');
        $this->label_nbRecords->setScale(0.8);
        $this->label_nbRecords->setText($rank->nbRecords . '/' . $rank->nbMaps);
        $this->frame->addComponent($this->label_nbRecords);

        $this->label_ptime = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_ptime->setAlign('left', 'center');
        $this->label_ptime->setScale(0.8);
        $this->label_ptime->setText($this->formatPTime($rank->player_timeplayed));
        $this->frame->addComponent($this->label_ptime);

        $this->label_lastRec = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_lastRec->setAlign('left', 'center');
        $this->label_lastRec->setScale(0.8);
        $this->label_lastRec->setText(date("j F Y", $rank->lastRec));
        $this->frame->addComponent($this->label_lastRec);
    }

    public function onResize($oldX, $oldY) {
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / .8) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_nick->setSizeX($scaledSizes[1]);
        $this->label_wins->setSizeX($scaledSizes[2]);
        $this->label_score->setSizeX($scaledSizes[3]);
        $this->label_finish->setSizeX($scaledSizes[4]);
        $this->label_nbRecords->setSizeX($scaledSizes[5]);
        $this->label_ptime->setSizeX($scaledSizes[6]);
        $this->label_lastRec->setSizeX($scaledSizes[7]);
    }

    // manialive 3.1 override to do nothing.
    function destroy() {
        
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
        parent::destroy();
    }

    public function formatPTime($time) {
        $min = (int) ($time / 60);
        $hour = (int) ($min / 60);
        $min = $min % 60;
        $day = (int) ($hour / 24);
        $hour = $hour % 24;
        return $day . 'd ' . $hour . 'h ' . $min . 'm';
    }

}

?>
