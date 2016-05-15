<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

class RatingsWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    protected $frame, $starFrame, $move, $gauge, $edgeWidget;
    protected $stars = array();
    public static $parentPlugin;

    protected function eXpOnBeginConstruct()
    {
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "top");
        // $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 20));
        $this->addComponent($this->frame);

        $bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(34, 10);
        $bg->setAction($this->createAction(array(self::$parentPlugin, "showRatingsManager")));
        $this->addComponent($bg);

        $title = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle(34, 4);
        $title->setText(eXpGetMessage('Map Rating'));
        $this->addComponent($title);

        /* $label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(34);
          $label->setText(exp_getMessage('Map Rating'));
          $label->setTextColor("ffff");
          $label->setHalign("center");
          $label->setStyle("TextRaceMessage");
          $label->setPosition(17, -1);
          $label->setTextSize(1.5);
          $this->addComponent($label);
         */

        $this->starFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->starFrame->setPosition(2, -2);
        $this->starFrame->setSize(34, 4);
        $this->frame->addComponent($this->starFrame);
        $this->gauge = new \ManiaLive\Gui\Elements\Xml();

        $this->setName("Map Ratings Widget");

        $storage = \ManiaLivePlugins\eXpansion\Helpers\Storage::getInstance();
        if ($storage->simpleEnviTitle == "TM") {
            $this->edgeWidget = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui/Scripts/EdgeWidget");
            $this->registerScript($this->edgeWidget);
        }
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    public function setStars($number, $total)
    {
        $this->frame->clearComponents();
        $login = $this->getRecipient();

        $test = ($number / 6) * 100;
        $color = "fff";
        if ($test < 30) $color = "0ad";
        if ($test >= 30) $color = "2af";
        if ($test > 60) $color = "0cf";

        $this->gauge->setContent('<gauge sizen="32 7" drawblockbg="1" style="ProgressBarSmall" color="' . $color . '" drawbg="1" rotation="0" posn="0 -3.5" grading="1" ratio="' . ($number
                / 5) . '" centered="0" />');
        $this->frame->addComponent($this->gauge);

        $score = ($number / 5) * 100;
        $score = round($score);


        $info = new \ManiaLib\Gui\Elements\Label();
        $info->setTextSize(1);
        $info->setTextColor('fff');
        $info->setAlign("center", "center");
        $info->setTextEmboss();
        $info->setText($score . "% (" . $total . ")");
        $info->setPosition(17, -7);
        $this->frame->addComponent($info);
        $this->redraw();
    }
}
