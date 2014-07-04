<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Maps\Gui\Windows\Maplist;
use ManiaLivePlugins\eXpansion\Gui\Gui;
use \ManiaLivePlugins\eXpansion\Gui\Structures\OptimizedPagerElement;
use \ManiaLive\Gui\Control;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;

class Mapitem extends Control implements OptimizedPagerElement {

    public static $ColumnWidths;
    protected $bg;
    protected $queueButton;
    protected $goButton;
    protected $showRecsButton;
    protected $removeButton;
    public $label_map, $label_envi, $label_author, $label_authortime, $label_localrec, $label_rating;
    protected $frame;
    protected $actionsFrame;

    function __construct($indexNumber, $login, $action) {
	$sizeY = 6.5;
	$sizeX = 170;

	$scaledSizes = Gui::getScaledSize(self::$ColumnWidths, ($sizeX) - 7);

	$this->bg = new ListBackGround($indexNumber, $sizeX, $sizeY);
	$this->addComponent($this->bg);

	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setSize($sizeX, $sizeY);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

	$this->label_author = new \ManiaLib\Gui\Elements\Label($scaledSizes[1], 4);
	$this->label_author->setAlign('left', 'center');
	$this->label_author->setId('column_' . $indexNumber . '_1');
	$this->frame->addComponent($this->label_author);

	$this->label_map = new \ManiaLib\Gui\Elements\Label($scaledSizes[0], 4);
	$this->label_map->setAlign('left', 'center');
	$this->label_map->setStyle('TextCardSmallScores2');
	$this->label_map->setFocusAreaColor1('0000');
	$this->label_map->setFocusAreaColor2('2af6');
	$this->label_map->setTextPrefix('$s');
	$this->label_map->setId('column_' . $indexNumber . '_0');
	$this->label_map->setAction($action);
	$this->label_map->setAttribute("class", "eXpOptimizedPagerAction");
	$this->label_map->setScriptEvents(1);
	$this->frame->addComponent($this->label_map);

	$this->label_envi = new \ManiaLib\Gui\Elements\Label($scaledSizes[2], 4);
	$this->label_envi->setAlign('left', 'center');
	$this->label_envi->setId('column_' . $indexNumber . '_2');
	$this->frame->addComponent($this->label_envi);
	
	$this->label_authortime = new \ManiaLib\Gui\Elements\Label($scaledSizes[3], 4);
	$this->label_authortime->setAlign('left', 'center');
	$this->label_authortime->setId('column_' . $indexNumber . '_3');
	$this->frame->addComponent($this->label_authortime);

	$this->label_localrec = new \ManiaLib\Gui\Elements\Label($scaledSizes[4], 4);
	$this->label_localrec->setAlign('center', 'center');
	$this->label_localrec->setId('column_' . $indexNumber . '_4');
	$this->frame->addComponent($this->label_localrec);

	$this->label_rating = new \ManiaLib\Gui\Elements\Label($scaledSizes[5], 4);
	$this->label_rating->setAlign('center', 'center');
	$this->label_rating->setId('column_' . $indexNumber . '_5');
	$this->frame->addComponent($this->label_rating);

	$this->actionsFrame = new \ManiaLive\Gui\Controls\Frame();
	$this->actionsFrame->setSize($scaledSizes[5], 4);
	$this->actionsFrame->setLayout(new \ManiaLib\Gui\Layouts\Line());
	$this->frame->addComponent($this->actionsFrame);

	if (Maplist::$localrecordsLoaded) {
	    $this->showRecsButton = new MyButton(5, 5);
	    $this->showRecsButton->setDescription(__('Show Records', $login), 40);
	    $this->showRecsButton->setAction($action);
	    $this->showRecsButton->setIcon('BgRaceScore2', 'ScoreLink');
	    $this->showRecsButton->setId('column_' . $indexNumber . '_6');
	    $this->showRecsButton->setClass("eXpOptimizedPagerAction");
	    $this->actionsFrame->addComponent($this->showRecsButton);
	}

	if (\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::map_removeMap)) {

	    $spacer = new \ManiaLib\Gui\Elements\Quad();
	    $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
	    $spacer->setSize(2, 4);
	    $this->actionsFrame->addComponent($spacer);

	    $this->removeButton = new MyButton(5, 5);
	    $this->removeButton->setDescription(__('$F22Remove this map from server', $login), 70);
	    $this->removeButton->setAction($action);
	    $this->removeButton->colorize('a22');
	    $this->removeButton->setIcon('Icons64x64_1', 'Close');
	    $this->removeButton->setId('column_' . $indexNumber . '_7');
	    $this->removeButton->setClass("eXpOptimizedPagerAction");
	    $this->actionsFrame->addComponent($this->removeButton);
	}

	$this->addComponent($this->frame);
	$this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {
	$this->bg->setSize($this->getSizeX(), $this->getSizeY());
	$scaledSizes = Gui::getScaledSize(self::$ColumnWidths, ($this->getSizeX()));
	$this->label_author->setSizeX($scaledSizes[0]);
	$this->label_map->setSizeX($scaledSizes[1]);
	$this->label_envi->setSizeX($scaledSizes[2]);
	$this->label_authortime->setSizeX($scaledSizes[3]);
	$this->label_localrec->setSizeX($scaledSizes[4]);
	$this->label_rating->setSizeX($scaledSizes[5]);
	$this->actionsFrame->setSizeX($scaledSizes[6]);
	$this->frame->setSize($this->getSizeX() - 5, $this->getSizeY());
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

    public function getNbTextColumns() {
	return 6;
    }

}
?>

