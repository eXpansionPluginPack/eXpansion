<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of RecItem
 *
 * @author oliverde8
 */
class RecItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{
    protected $label_rank, $label_nick, $label_score, $label_avgScore, $label_nbFinish, $label_login;
    protected $bg, $button_report;
    private $widths;

    function __construct($indexNumber, $login, \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord $record, $widths)
    {
        $this->widths = $widths;
        $this->sizeY = 6;
        $this->bg = new ListBackGround($indexNumber, 100, 6);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(100, 6);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_rank->setAlign('left', 'center');
        $this->label_rank->setScale(1);
        $this->label_rank->setText(($indexNumber + 1) . ".");
        $this->frame->addComponent($this->label_rank);

        $this->label_score = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_score->setAlign('left', 'center');
        $this->label_score->setScale(1);
        $this->label_score->setText(\ManiaLive\Utilities\Time::fromTM($record->time));
        $this->frame->addComponent($this->label_score);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_nick->setAlign('left', 'center');
        $this->label_nick->setScale(1);
        $this->label_nick->setText($record->nickname);
        $this->frame->addComponent($this->label_nick);

        $this->label_login = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_login->setAlign('left', 'center');
        $this->label_login->setScale(1);
        $this->label_login->setText($record->login);
        $this->frame->addComponent($this->label_login);

        $this->button_report = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(6, 6);
        $this->button_report->setIcon("Icons64x64_2", "Disconnected");
        $this->button_report->setAction($this->createAction(array($this, "openRepWindow"), $record->login));
        $this->frame->addComponent($this->button_report);
    }

    public function openRepWindow($login, $reportLogin)
    {
        $window = \ManiaLivePlugins\eXpansion\Dedimania\Gui\Windows\DediReport::Create($login);
        $window->setTitle("Report for Dedimania");
        $window->setLogin($reportLogin);
        $window->setSize(100, 100);
        $window->show();
    }

    public function onResize($oldX, $oldY)
    {
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX()) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_score->setSizeX($scaledSizes[1]);
        $this->label_nick->setSizeX($scaledSizes[2]);
        $this->label_login->setSizeX($scaledSizes[3]);
        $this->button_report->setSizeX($scaledSizes[4]);
    }

    // manialive 3.1 override to do nothing.
    function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    function erase()
    {
        parent::destroy();
    }
}

?>
