<?php

namespace ManiaLivePlugins\eXpansion\ScoreDisplay\Gui\Windows;

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;
use ManiaLivePlugins\eXpansion\ScoreDisplay\Gui\Widgets\Scores;

class ScoreSetup extends Window
{

    protected $frame;


    public function onConstruct()
    {
        parent::onConstruct();

        $this->frame = new Frame(0, -8, new Column());
        $this->addComponent($this->frame);

        $input = new Inputbox("team1Country");
        $input->setLabel("Team1 Country");
        $this->frame->addComponent($input);

        $input = new Inputbox("team1Name");
        $input->setLabel("Team1 Name");
        $this->frame->addComponent($input);

        $input = new Inputbox("team2Country");
        $input->setLabel("Team2 Country");
        $this->frame->addComponent($input);

        $input = new Inputbox("team2Name");
        $input->setLabel("Team2 Name");
        $this->frame->addComponent($input);

        $button = new Button();
        $button->setText("Ok");
        $button->setAction($this->createAction(array($this, "ok")));
        $this->frame->addComponent($button);

    }

    public function Ok($login, $data)
    {
        $this->EraseAll();
        $scale = 0.8;
        $scores = Scores::Create(null);
        $scores->setData($data);
        $scores->setName("ScoreWidget");
        $scores->setScale($scale);
        $scores->setPosition(-70 * $scale, 80);
        $scores->show();
    }
}