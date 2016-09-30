<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of CpItem
 *
 * @author reaby
 */
class TopsumItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    protected $label_rank;
    protected $label_nick;
    protected $label_score;
    protected $label_avgScore;
    protected $label_nbFinish;
    protected $label_login;

    protected $bg;

    public function __construct($indexNumber, $login, $data, $width = 100)
    {
        $this->sizeY = 6;
        $this->sizeX = 100;
        $this->bg = new ListBackGround($indexNumber, 100, 6);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize(160, 6);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->addComponent($this->frame);

        $this->label_rank = new \ManiaLib\Gui\Elements\Label(8, 4);
        $this->label_rank->setAlign('left', 'center');

        /** @var \ManiaLivePlugins\eXpansion\Core\ColorParser $color */
        $color = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();

        $this->label_rank->setTextColor('');
        $this->label_rank->setText($color->parseColors("#rank#") . ($indexNumber + 1));
        $this->frame->addComponent($this->label_rank);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label(60, 4);
        $this->label_nick->setAlign('left', 'center');
        if (isset($data->nickName)) {
            $this->label_nick->setText($data->nickName);
        }
        $this->frame->addComponent($this->label_nick);

        $frameCP = new \ManiaLive\Gui\Controls\Frame();
        $frameCP->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $colors = array('da3', 'ccc', 'a76');

        foreach ($data->stats as $pos => $value) {
            $label = new \ManiaLib\Gui\Elements\Label(8, 6);
            $label->setTextColor($colors[$pos]);
            $label->setText($value);
            $label->setAlign("left", "center");
            $frameCP->addComponent($label);
        }

        $this->frame->addComponent($frameCP);
    }

    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->bg->setSize($this->sizeX, $this->sizeY);
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }
}
