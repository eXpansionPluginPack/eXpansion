<?php

namespace ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Widgets;

use ManiaLib\Gui\Layouts\Column;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Widgets\Widget;
use ManiaLivePlugins\eXpansion\Widgets_EndRankings\Gui\Controls\GenericItem;

class GenericPanel extends Widget
{
    protected $frame;
    protected $items = array();
    protected $bg;
    protected $bgTitle;
    protected $quad;
    protected $lbl;
    protected $lineHeight = 3.1;
    protected $totalLines;

    protected function onConstruct()
    {

        $sizeX = 38;
        $sizeY = 95;

        $this->bg = new WidgetBackGround(38, 95);
        $this->addComponent($this->bg);

        $this->bgTitle = new WidgetTitle($sizeX, $sizeY);
        $this->bgTitle->setText(eXpGetMessage("Generic Panel"));
        $this->addComponent($this->bgTitle);

        $this->frame = new Frame(4, -5);
        $this->frame->setLayout(new Column(-1));
        $this->addComponent($this->frame);

        $this->setName("Generic Panel");
        $this->sizeX = 40;
        parent::onConstruct();
    }

    public function setTitle(Message $title)
    {
        $this->setName($title->getMessage('en'));
        $this->bgTitle->setText($title);
    }

    public function setLines($value)
    {
        $this->totalLines = $value;
        $this->sizeX = 40;
        $this->sizeY = ($this->lineHeight * $this->totalLines) + 5;

        $this->setSize($this->sizeX, ($this->lineHeight * $this->totalLines) + 5);
    }

    public function onResize($oldX, $oldY)
    {
        $this->bg->setSize($this->sizeX, ($this->lineHeight * $this->totalLines) + 5);
        $this->bgTitle->setSize($this->sizeX, 4.2);
        parent::onResize($oldX, $oldY);
    }

    /**
     *
     * @param GenericItem[] $items
     */
    public function setData($items)
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = array();
        $this->frame->clearComponents();

        $x = 0;
        foreach ($items as $item) {

            $this->items[$x] = new GenericItem($x, $item);
            $this->frame->addComponent($this->items[$x]);
            $x++;
            if ($x == $this->totalLines) break;
        }
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = array();
        $this->destroyComponents();

        parent::destroy();
    }
}
