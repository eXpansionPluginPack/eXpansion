<?php

namespace ManiaLivePlugins\eXpansion\ScoreDisplay\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Helpers\Countries;

class Scores extends Widget
{

    protected $points1, $points2;
    protected $frame, $frame2;
    private $counter = array(0, 0);

    public function eXpOnBeginConstruct()
    {
        parent::eXpOnBeginConstruct();

        $this->frame = new Frame(0, 0, new Line());
        $this->frame->setAlign("center", "top");
        $this->addComponent($this->frame);

        $this->frame2 = new Frame(0, -12, new Line());
        $this->frame2->setAlign("center", "top");
        $this->addComponent($this->frame2);

        $spacer = new Label(16, 6);
        $this->frame2->addComponent($spacer);


        $btn = new Button(9, 6);
        $btn->setText("+");
        $btn->setAction($this->createAction(array($this, "add"), 0));
        $this->frame2->addComponent($btn);

        $btn = new Button(9, 6);
        $btn->setText("-");
        $btn->setAction($this->createAction(array($this, "sub"), 0));
        $this->frame2->addComponent($btn);

        $spacer = new Label(52, 6);
        $this->frame2->addComponent($spacer);

        $btn = new Button(9, 6);
        $btn->setText("+");
        $btn->setAction($this->createAction(array($this, "add"), 1));
        $this->frame2->addComponent($btn);

        $btn = new Button(9, 6);
        $btn->setText("-");
        $btn->setAction($this->createAction(array($this, "sub"), 1));
        $this->frame2->addComponent($btn);
    }

    public function setData($data)
    {
        $data = (object)$data;
        $team1 = new Quad(12, 12);

        $cname = Countries::getCountryFromCode($data->team1Country);

        $team1->setImage("http://reaby.kapsi.fi/ml/flags/" . $cname . ".dds", true);
        $this->frame->addComponent($team1);

        $team1Name = New Label(40, 6);
        $team1Name->setText($data->team1Name);
        $team1Name->setStyle("TextRaceMessageBig");
        $this->frame->addComponent($team1Name);

        $this->points1 = new Label(18, 6);
        $this->points1->setPosition(9, -2);
        $this->points1->setAlign("center", "top");
        $this->points1->setText($this->counter[0]);
        $this->points1->setStyle("TextRaceChrono");
        $this->frame->addComponent($this->points1);


        $this->points2 = new Label(18, 6);
        $this->points2->setPosition(9, -2);
        $this->points2->setAlign("center", "top");
        $this->points2->setText($this->counter[1]);
        $this->points2->setStyle("TextRaceChrono");
        $this->frame->addComponent($this->points2);

        $team2Name = New Label(40, 6);
        $team2Name->setText($data->team2Name);
        $team2Name->setStyle("TextRaceMessageBig");
        $this->frame->addComponent($team2Name);

        $team2 = new Quad(12, 12);
        $cname = Countries::getCountryFromCode($data->team2Country);
        $team2->setImage("http://reaby.kapsi.fi/ml/flags/" . $cname . ".dds", true);
        $this->frame->addComponent($team2);

    }


    public function add($login, $team)
    {

        if (!AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)) {
            return;
        }
        $this->counter[$team]++;
        $this->points1->setText($this->counter[0]);
        $this->points2->setText($this->counter[1]);
        $this->redraw(null);
    }

    public function sub($login, $team)
    {
        if (!AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)) {
            return;
        }
        $this->counter[$team]--;
        $this->points1->setText($this->counter[0]);
        $this->points2->setText($this->counter[1]);
        $this->redraw(null);
    }
}