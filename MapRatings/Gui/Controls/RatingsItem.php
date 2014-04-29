<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls;

use \ManiaLib\Utils\Formatting;

class RatingsItem extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $time;
    private $addAction;
    private $addButton;
    private $actionSearch;
    private $queueButton;
    private $queueAction;
    private $frame;
    private $isAdmin;

    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\MapRatings\Structures\MapRating $rating) {
        $sizeY = 5.5;
	$sizeX = 110;
	
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	
        $label =  new \ManiaLib\Gui\Elements\Label();
        $label->setAlign('left', 'center');
	$label->setText($rating->map->environnement);
        $this->frame->addComponent($label);
        
        $this->label = new \ManiaLib\Gui\Elements\Label(40, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText(Formatting::stripColors(Formatting::stripStyles($rating->map->name)));
        $this->frame->addComponent($this->label);

        $info = new \ManiaLib\Gui\Elements\Label(25, 4);
        $info->setAlign('left', 'center');
        $info->setText('$000' . $rating->map->author);
        $info->setAction($this->actionSearch);
        $info->setStyle("TextCardSmallScores2");
        $info->setScriptEvents(true);
        $this->frame->addComponent($info);

        $this->time = new \ManiaLib\Gui\Elements\Label(20, 4);
        $this->time->setAlign('left', 'center');
        $this->time->setText(\ManiaLive\Utilities\Time::fromTM($rating->map->goldTime));
        $this->frame->addComponent($this->time);

        $info = new \ManiaLib\Gui\Elements\Label(16, 4);
        $info->setAlign('left', 'center');
        $info->setText($rating->rating);
        $this->frame->addComponent($info);

        $info = new \ManiaLib\Gui\Elements\Label(8, 4);
        $info->setAlign('left', 'center');
        $info->setText($rating->totalvotes);
        $this->frame->addComponent($info);
	
        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
    }

    protected function onResize($oldX, $oldY) {

        $this->frame->setSize($this->sizeX, $this->sizeY + 1);
        //  $this->button->setPosx($this->sizeX - $this->button->sizeX);
    }
    
    
    // override destroy method not to destroy its contents on manialive 3.1 
    function destroy() {
        
    }

    /**
     * custom function to destroy contents when needed.
     */
    function erase() {
        if (is_object($this->queueButton)) {
            $this->queueButton->destroy();
        }
        if ($this->isAdmin) {
            $this->addButton->destroy();
        }
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

