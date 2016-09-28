<?php

/*
 * Copyright (C) 2014 Reaby
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Windows;

/**
 * Description of TopSumsWindow
 *
 * @author Reaby
 */
class TopSumsWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    public $pager;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->pager->setSize(160, 60);
        $this->addComponent($this->pager);
    }

    public function setDatas($data)
    {
        $x = 0;
        foreach ($data as $login => $value) {
            if ($x >= 100) {
                break;
            }

            $this->items[$x] = new \ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\TopsumItem(
                $x,
                $login,
                $value,
                100
            );
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 4, $this->sizeY);
        foreach ($this->items as $item) {
            $item->setSizeX($this->sizeX - 8);
        }
    }

    public function destroy()
    {
        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
