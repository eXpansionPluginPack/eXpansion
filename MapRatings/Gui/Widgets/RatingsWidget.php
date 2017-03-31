<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Widgets;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use ManiaLivePlugins\eXpansion\Helpers\Storage;

class RatingsWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{
    /** @var  Frame */
    protected $frame;
    protected $starFrame;
    protected $move;
    protected $gauge;
    protected $edgeWidget;
    protected $stars = array();
    public static $parentPlugin;

    protected function eXpOnBeginConstruct()
    {
        $this->frame = new Frame();
        $this->frame->setAlign("left", "top");
        $this->addComponent($this->frame);

        $bg = new WidgetBackGround(34, 6);
        $bg->setPosition(0, -4);
        $bg->setAction($this->createAction(array(self::$parentPlugin, "showRatingsManager")));
        // $this->addComponent($bg);

        $title = new WidgetTitle(34, 4);
        $title->setText(eXpGetMessage('Map Rating'));
        //$this->addComponent($title);


        $this->starFrame = new Frame(0, 0, new Line());
        $this->starFrame->setSize(34, 6);
        $this->frame->addComponent($this->starFrame);

        $this->setName("Map Ratings Widget");

        /*
         $storage = Storage::getInstance();
         if ($storage->simpleEnviTitle == "TM") {
             $this->edgeWidget = new Script("Gui/Scripts/EdgeWidget");
             $this->registerScript($this->edgeWidget);
         } */
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
    }

    public function setStars($number, $total)
    {
        $this->starFrame->clearComponents();

        for ($x = 0; $x < floor($number); $x++) {
            $star = new Label();
            $star->setSize(6, 6);
            $star->setText('');
            $star->setTextSize(5);
            $this->starFrame->addComponent($star);
        }

        $fraction = $number - floor($number);

        $star = new Label();
        $star->setSize(6, 6);
        $star->setTextSize(5);
        $star->setText('');

        if ($fraction < 0.33) {
            $star->setText('');
        }

        if ($fraction >= 0.66) {
            $star->setText('');

        }
        if ($fraction != 0) {
            $this->starFrame->addComponent($star);
        }
        for ($x = 0; $x < floor(5 - $number); $x++) {
            $star = new Label();
            $star->setSize(6, 6);
            $star->setText('');
            $star->setTextSize(5);
            $this->starFrame->addComponent($star);
        }

       /* $info = new Label();
        $info->setTextColor('fff');
        $info->setAlign("left", "top");
        $info->setTextEmboss();
        $info->setTextSize(3);
        $info->setText("number:" . $number);
        $this->starFrame->addComponent($info);
        $this->redraw(); */
    }
}
