<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use \ManiaLib\Utils\Formatting;
use ManiaLivePlugins\eXpansion\Gui\Gui;

class Mapitem extends \ManiaLive\Gui\Control {

    protected $bg;
    protected $queueButton;
    protected $goButton;
    protected $showRecsButton;
    protected $removeButton;
    protected $label_map, $label_author, $label_authortime, $label_localrec, $label_rating;
    protected $frame;
    protected $actionsFrame;
    private $queueMapAction;
    private $gotoMapAction;
    private $removeMapAction;
    private $showRecsAction;
    private $widths;

    function __construct($indexNumber, $login, \ManiaLivePlugins\eXpansion\Maps\Structures\SortableMap $sortableMap, $controller, $isAdmin, $widths, $sizeX) {
        $sizeY = 5.5;
        $this->isAdmin = $isAdmin;
        $this->widths = $widths;

        $scaledSizes = Gui::getScaledSize($this->widths, ($sizeX / .8) - 7);

        $this->queueMapAction = $this->createAction(array($controller, 'queueMap'), $sortableMap->map);
        $this->gotoMapAction = $this->createAction(array($controller, 'gotoMap'), $sortableMap->map);
        $this->removeMapAction = $this->createAction(array($controller, 'removeMap'), $sortableMap->map);
        $this->showRecsAction = $this->createAction(array($controller, 'showRec'), $sortableMap->map);
        $this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->label_map = new \ManiaLib\Gui\Elements\Label($scaledSizes[0], 4);
        $this->label_map->setAlign('left', 'center');
        $this->label_map->setText(Formatting::stripColors($sortableMap->name));
        $this->label_map->setScale(0.8);
        $this->frame->addComponent($this->label_map);

        $this->label_author = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
        $this->label_author->setAlign('left', 'center');
        $this->label_author->setScale(0.8);
        $this->label_author->setText($sortableMap->author);
        $this->frame->addComponent($this->label_author);

        $this->label_authortime = new \ManiaLib\Gui\Elements\Label($scaledSizes[2], 4);
        $this->label_authortime->setAlign('left', 'center');
        $this->label_authortime->setScale(0.8);
        $this->label_authortime->setText(\ManiaLive\Utilities\Time::fromTM($sortableMap->goldTime));
        $this->frame->addComponent($this->label_authortime);

        $this->label_localrec = new \ManiaLib\Gui\Elements\Label($scaledSizes[3], 4);
        $this->label_localrec->setAlign('center', 'center');
        $this->label_localrec->setScale(0.8);
        $this->label_localrec->setText($sortableMap->localrecord);
        $this->frame->addComponent($this->label_localrec);

        $this->label_rating = new \ManiaLib\Gui\Elements\Label($scaledSizes[4], 4);
        $this->label_rating->setAlign('center', 'center');
        $this->label_rating->setScale(0.8);

        $rate = ($sortableMap->rating->rating / 5) * 100;
        $rate = round($rate) . "%";
        if ($sortableMap->rating->rating == -1)
            $rate = "-";
        $this->label_rating->setText($rate);
        $this->frame->addComponent($this->label_rating);

        $this->actionsFrame = new \ManiaLive\Gui\Controls\Frame();
        $this->actionsFrame->setSize($scaledSizes[5], 4);
        $this->actionsFrame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->frame->addComponent($this->actionsFrame);

        $this->queueButton = new MyButton(26, 5);
        $this->queueButton->setText(__("Queue", $login));
        $this->queueButton->setAction($this->queueMapAction);
        $this->queueButton->colorize('2a2');
        $this->queueButton->setScale(0.5);
        $this->actionsFrame->addComponent($this->queueButton);
        if (Maplist::$localrecordsLoaded) {
            $this->showRecsButton = new MyButton(26, 5);
            $this->showRecsButton->setText(__("Recs", $login));
            $this->showRecsButton->setAction($this->showRecsAction);
            $this->showRecsButton->colorize('2a2');
            $this->showRecsButton->setScale(0.5);
            $this->actionsFrame->addComponent($this->showRecsButton);
        }
        if ($this->isAdmin) {
            $this->goButton = new MyButton(26, 5);
            $this->goButton->setText(__("Go now", $login));
            $this->goButton->setAction($this->gotoMapAction);
            $this->goButton->colorize('aa2');
            $this->goButton->setScale(0.5);
            //  $this->actionsFrame->addComponent($this->goButton);

            $spacer = new \ManiaLib\Gui\Elements\Quad();
            $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
            $spacer->setSize(2, 4);
            $this->actionsFrame->addComponent($spacer);

            $this->removeButton = new MyButton(26, 5);
            $this->removeButton->setText('$fff' . __("Remove", $login));
            $this->removeButton->setAction($this->removeMapAction);
            $this->removeButton->colorize('a22');
            $this->removeButton->setScale(0.5);
            $this->actionsFrame->addComponent($this->removeButton);
        }

        $this->addComponent($this->frame);
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
        $this->bg->setSize($this->getSizeX() - 5, $this->getSizeY());
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / 0.8) - 12);
        $this->label_map->setSizeX($scaledSizes[0]);
        $this->label_author->setSizeX($scaledSizes[1]);
        $this->label_authortime->setSizeX($scaledSizes[2]);
        $this->label_localrec->setSizeX($scaledSizes[3]);
        $this->label_rating->setSizeX($scaledSizes[4]);
        $this->actionsFrame->setSizeX($scaledSizes[5]);
        $this->frame->setSize($this->getSizeX() - 5, $this->getSizeY());
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->erase();
    }

// manialive 3.1 override to do nothing.
    function destroy() {
        
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
        $this->queueButton->destroy();

        if (is_object($this->goButton))
            $this->goButton->destroy();
        if (is_object($this->removeButton))
            $this->removeButton->destroy();
        if (is_object($this->showRecsButton))
            $this->showRecsButton->destroy();

        $this->clearComponents();
        parent::destroy();
    }

}
?>

