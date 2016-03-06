<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Controls;

class RankItem extends \ManiaLivePlugins\eXpansion\Gui\Control {

    protected $bg;
    protected $nick;
    protected $label;
    protected $time;
    protected $frame;

    function __construct($index, $rank) {
        $sizeX = 36;
        $sizeY = 3;

        $this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label->setAlign('right', 'center');
        $this->label->setPosition(0, 0);
        $this->label->setStyle("TextRaceChat");
        $this->label->setText($index + 1);
        $this->label->setTextColor('fff');
        $this->label->setScale(0.75);
        $this->addComponent($this->label);

        $this->label = new \ManiaLib\Gui\Elements\Label(14, 5);
        $this->label->setPosX(1);
        $this->label->setAlign('left', 'center');
        $this->label->setStyle("TextRaceChat");
        $this->label->setText(number_format($rank->tscore + 1, 2));
        $this->label->setTextColor('ff0');
        $this->label->setScale(0.75);
        $this->addComponent($this->label);



        $this->nick = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->nick->setPosition(8, 0);
        $this->nick->setAlign('left', 'center');
        $this->nick->setStyle("TextRaceChat");
        $this->nick->setTextColor('fff');
        $nickname = $rank->player_nickname;
        $this->nick->setText($nickname);
        $this->nick->setScale(0.75);
        $this->addComponent($this->nick);


        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy() {
        $this->destroyComponents();
        parent::destroy();
    }

}
?>

