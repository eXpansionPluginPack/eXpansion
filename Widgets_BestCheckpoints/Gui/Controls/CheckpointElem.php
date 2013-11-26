<?php

namespace ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Gui\Controls;

class CheckpointElem extends \ManiaLive\Gui\Control {

    private $bg;
    private $nick;
    private $time;

    function __construct(\ManiaLivePlugins\eXpansion\Widgets_BestCheckpoints\Structures\Checkpoint $cp) {
        $sizeX = 35;
        $sizeY = 5;

        /* $this->bg = new \ManiaLib\Gui\Elements\Quad($sizeX, $sizeY);
          $this->bg->setPosX(-2);
          $this->bg->setStyle("Bgs1InRace");
          $this->bg->setSubStyle("BgList");
          $this->bg->setAlign('left', 'center');
          $this->addComponent($this->bg); */


        $this->label = new \ManiaLib\Gui\Elements\Label(10, 3);
        $this->label->setAlign('left', 'center');
        $this->label->setTextSize(1);
        $this->label->setPosX(0);
        if ($cp->time != 0)
            $this->label->setText('$ff0' . ($cp->index + 1 ) . ' $fff' . \ManiaLive\Utilities\Time::fromTM($cp->time));

        $this->addComponent($this->label);


        $this->nick = new \ManiaLib\Gui\Elements\Label(25, 4);
        $this->nick->setAlign('left', 'center');
        $this->nick->setTextSize(1);
        $this->nick->setPosX(11);
        $nickname = \ManiaLib\Utils\Formatting::stripCodes($cp->nickname, "wosnm");
        $this->nick->setText('$fff' . $nickname);
        $this->addComponent($this->nick);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
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

