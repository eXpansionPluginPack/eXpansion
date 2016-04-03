<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Gui\Controls\Item;
use ManiaLivePlugins\eXpansion\Gui\Gui;

/**
 * Description of PagerWindow
 *
 * @author De Cramer Oliver
 */
abstract class PagerWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $frame;
    private $labels;
    private $pager;
    private $items = array();

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->pager->setPosX(0);
        $this->pager->setPosY(-4);
        $this->mainFrame->addComponent($this->pager);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($this->getSizeX(), 4);
        $this->frame->setPosY(0);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->mainFrame->addComponent($this->frame);

        $scaledSizes = Gui::getScaledSize($this->getWidths(), ($this->getSizeX() / 0.8) - 5);

        $i = 0;
        foreach ($scaledSizes as $sizeX) {
            $label = new \ManiaLib\Gui\Elements\Label($sizeX, 4);
            $label->setAlign('left', 'center');
            $label->setScale(0.8);
            $this->frame->addComponent($label);
            $this->labels[$i] = $label;
            $i++;
        }
    }

    public function setPagerPosition($posX, $posY)
    {
        $this->pager->setPosX($posX + 5);
        $this->pager->setPosY($posY);

        $this->frame->setPosX($posX + 5);
        $this->frame->setPosY($posY + 2);

        $this->onResize($this->getSizeX(), $this->getSizeY());
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

        $sizeX = $this->getSizeX() - $this->pager->getPosX() + 2;
        $this->pager->setSize($sizeX, $this->getSizeY() - 15 - $this->pager->getPosY());

        $scaledSizes = Gui::getScaledSize($this->getWidths(), $sizeX);
        $i = 0;
        foreach ($scaledSizes as $x) {
            $this->labels[$i]->setSizeX($x);
            $i++;
        }
        foreach ($this->items as $item)
            $item->setSizeX($sizeX);
    }

    public function onShow()
    {
        $i = 0;
        foreach ($this->labels as $label) {
            $label->setText(__($this->getLabel($i), $this->getRecipient()));
            $i++;
        }
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        $this->pager->destroy();
        $this->labels = null;
        $this->destroyComponents();
        parent::destroy();
    }

    abstract protected function getWidths();

    abstract protected function getLabel($i);

    abstract protected function getKeys();

    protected function getFormaters()
    {
        return array();
    }

    public function populateList($data)
    {
        $x = 0;
        $login = $this->getRecipient();

        while ($x < sizeof($data)) {
            $this->items[$x] = new Item($x, $login, $data[$x], $this->getWidths(), $this->getKeys(), $this->getFormaters());
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }
}

?>
