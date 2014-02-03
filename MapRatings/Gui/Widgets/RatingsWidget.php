<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;

class RatingsWidget extends \ManiaLivePlugins\eXpansion\Gui\Windows\Widget {

    protected $frame, $starFrame, $move, $gauge;
    protected $stars = array();

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "top");
        // $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 20));
        $this->addComponent($this->frame);

        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(34, 12);    
        $bg->setPosition(0,-6);
        $this->addComponent($bg);

        $label = new \ManiaLib\Gui\Elements\Label(34);
        $label->setText('$s' . __('Map Rating'));
        $label->setTextColor("ffff");
        $label->setHalign("center");
        $label->setStyle("TextRaceMessage");
        $label->setPosition(17, -1);
        $label->setTextSize(1.5);
        $this->addComponent($label);

        $this->starFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->starFrame->setPosition(2,0);
        $this->starFrame->setSize(34, 4);
        $this->frame->addComponent($this->starFrame);
        $this->gauge = new \ManiaLive\Gui\Elements\Xml();
        
        $this->setName("Map Ratings Widget");
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function destroy() {
        parent::destroy();
    }

    function setStars($number, $total) {
        $this->frame->clearComponents();
        $login = $this->getRecipient();

        $test = ($number / 6) * 100;
        $color = "fff";
        if ($test < 30)
            $color = "0ad";
        if ($test >= 30)
            $color = "2af";
        if ($test > 60)
            $color = "0cf";

        $this->gauge->setContent('<gauge scale="0.7" sizen="45 15" drawblockbg="1" color="' . $color . '" drawbg="0" rotation="0" posn="0 -3" grading="1" ratio="' . ($number / 5) . '" centered="0" />');
        $this->frame->addComponent($this->gauge);

        $score = ($number / 5) * 100;
        $score = round($score);


        $info = new \ManiaLib\Gui\Elements\Label();
        $info->setTextSize(1);
        $info->setTextColor('fff');
        $info->setAlign("center", "center");
        $info->setTextEmboss();
        $info->setText($score . "% (" . $total . ")");
        $info->setPosition(17, -8.5);
        $this->frame->addComponent($info);
        $this->redraw();
    }

}

?>
