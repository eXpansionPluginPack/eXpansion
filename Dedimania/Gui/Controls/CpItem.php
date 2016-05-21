<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of RecItem
 *
 * @author reaby
 */
class CpItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $label_rank;
    protected $label_nick;
    protected $label_score;
    protected $label_avgScore;
    protected $label_nbFinish;
    protected $label_login;

    protected $bg;

    private $widths;

    public function __construct($indexNumber, $login, \ManiaLivePlugins\eXpansion\Dedimania\Structures\DediRecord $record, $widths, $offset = 0)
    {
        $this->widths = $widths;
        $this->sizeY = 6;
        $this->bg = new ListBackGround($indexNumber, 100, 6);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(160, 6);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_rank->setAlign('left', 'center');

        /** @var \ManiaLivePlugins\eXpansion\Core\ColorParser $color */
        $color = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();

        $this->label_rank->setTextColor('');
        $this->label_rank->setText($color->parseColors("#rank#") . ($indexNumber + 1));
        $this->frame->addComponent($this->label_rank);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label(10, 4);
        $this->label_nick->setAlign('left', 'center');
        $this->label_nick->setText($record->nickname);
        $this->frame->addComponent($this->label_nick);

        $frameCP = new \ManiaLive\Gui\Controls\Frame();
        $frameCP->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $cpArray = explode(",", $record->checkpoints);
        $idx = 0;
        $addLast = false;
        for ($x = $offset; $x <= $offset + 6; $x++) {
            if ($x > count($cpArray)) {
                break;
            }

            if (array_key_exists($x, $cpArray)) {
                $label = new \ManiaLib\Gui\Elements\Label(15, 6);
                $label->setText(\ManiaLive\Utilities\Time::fromTM($cpArray[$x]));
                $label->setAlign("left", "center");
                $frameCP->addComponent($label);
            }
            $idx++;
        }

        $this->frame->addComponent($frameCP);
    }

    public function onResize($oldX, $oldY)
    {
        $scaledSizes = Gui::getScaledSize($this->widths, ($this->getSizeX() / .8) - 5);
        $this->bg->setSizeX($this->getSizeX() - 5);
        $this->label_rank->setSizeX($scaledSizes[0]);
        $this->label_nick->setSizeX($scaledSizes[1]);
    }

    // manialive 3.1 override to do nothing.
    public function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    public function erase()
    {
        parent::destroy();
    }
}
