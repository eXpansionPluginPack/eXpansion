<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of RecItem
 *
 * @author oliverde8
 */
class RecItem extends \ManiaLivePlugins\eXpansion\Gui\Control implements \ManiaLivePlugins\eXpansion\Gui\Structures\OptimizedPagerElement
{

    /** @var \ManiaLib\Gui\Elements\Label */
    private $label_rank, $label_nick, $label_score, $label_avgScore, $label_nbFinish;

    private $button_delete;

    private $bg;

    public static $widths;

    function __construct($indexNumber, $login, $action)
    {
        $this->sizeY = 6;
        $this->bg = new ListBackGround($indexNumber, 100, 6);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(100, 6);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_rank->setAlign('left', 'center');
        $this->label_rank->setId('column_' . $indexNumber . '_0');
        $this->frame->addComponent($this->label_rank);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label(10., 6);
        $this->label_nick->setAlign('left', 'center');
        $this->label_nick->setId('column_' . $indexNumber . '_1');
        $this->frame->addComponent($this->label_nick);

        $this->label_score = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_score->setAlign('left', 'center');
        $this->label_score->setId('column_' . $indexNumber . '_2');
        $this->frame->addComponent($this->label_score);

        $this->label_avgScore = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_avgScore->setAlign('left', 'center');
        $this->label_avgScore->setId('column_' . $indexNumber . '_3');
        $this->frame->addComponent($this->label_avgScore);

        $this->label_nbFinish = new \ManiaLib\Gui\Elements\Label(10, 6);
        $this->label_nbFinish->setAlign('left', 'center');
        $this->label_nbFinish->setId('column_' . $indexNumber . '_4');
        $this->frame->addComponent($this->label_nbFinish);

        if (AdminGroups::hasPermission($login, Permission::localRecrods_delete)) {
            $this->button_delete = new Label(15, 6);
            $this->button_delete->setId('column_' . $indexNumber . '_5');
            $this->button_delete->setAlign('left', 'center');
            $this->button_delete->setAttribute('class', "eXpOptimizedPagerAction");
            $this->button_delete->setAction($action);
            $this->button_delete->setScriptEvents(true);
            $this->button_delete->setTextColor("F22");
            $this->frame->addComponent($this->button_delete);
        }

        $this->setSizeX(120);
    }

    public function onResize($oldX, $oldY)
    {
        $scaledSizes = Gui::getScaledSize(self::$widths, ($this->getSizeX()) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_nick->setSizeX($scaledSizes[1]);
        $this->label_score->setSizeX($scaledSizes[2]);
        $this->label_avgScore->setSizeX($scaledSizes[3]);
        $this->label_nbFinish->setSizeX($scaledSizes[4]);
        if ($this->button_delete != null) {
            $this->button_delete->setSizeX($scaledSizes[5]);
        }
    }

    // manialive 3.1 override to do nothing.
    function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    function erase()
    {
        parent::destroy();
    }

    public function getNbTextColumns()
    {
        return 6;
    }

}

?>
