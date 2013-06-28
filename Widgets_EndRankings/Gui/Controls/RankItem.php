<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Controls;

class RankItem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $label;
    private $time;
    private $frame;

    function __construct($index, $rank) {
        $sizeX = 36;
        $sizeY = 3;

        $this->label = new \ManiaLib\Gui\Elements\Label(4, 4);
        $this->label->setAlign('center', 'center');
        $this->label->setScale(0.7);
        $bold = "";
        if ($index < 3)
            $bold = '$o';
        $this->label->setText('$fff' . $bold . ($index + 1));
        $this->label->setPosX(0);
        $this->addComponent($this->label);

        $this->label = new \ManiaLib\Gui\Elements\Label(14, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setScale(0.7);
        $this->label->setPosX(2);
        $this->label->setText('$fff' . number_format($rank->tscore, 2));
        $this->addComponent($this->label);


        $this->nick = new \ManiaLib\Gui\Elements\Label(34, 4);
        $this->nick->setAlign('left', 'center');
        $this->nick->setScale(0.7);
        $this->nick->setPosX(10);
        $nickname = $rank->player_nickname;
        $nickname = \ManiaLib\Utils\Formatting::stripCodes($nickname, "wosnm");
        $this->nick->setText('$fff' . $nickname);

        $this->addComponent($this->nick);


        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    function onDraw() {
        
    }

    public function destroy() {
        try {
            $this->clearComponents();
        } catch (\Exception $e) {
            
        }
        parent::destroy();
    }

}
?>

