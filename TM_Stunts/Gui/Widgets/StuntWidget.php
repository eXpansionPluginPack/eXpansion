<?php

/*
 * Copyright (C) 2015 Reaby
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

namespace ManiaLivePlugins\eXpansion\TM_Stunts\Gui\Widgets;

/**
 * Description of StuntWidget
 *
 * @author Reaby
 */
class StuntWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    protected $lbl_stuntName, $lbl_description, $frame, $script;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Stunts Widget");

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->addComponent($this->frame);

        $this->lbl_stuntName = new \ManiaLib\Gui\Elements\Label(60, 6);
        $this->lbl_stuntName->setStyle("TextRaceMessageBig");
        $this->lbl_stuntName->setTextEmboss();
        $this->lbl_stuntName->setTextSize(3);
        $this->lbl_stuntName->setAlign("center", "center");
        $this->lbl_stuntName->setPosX(30);
        $this->lbl_stuntName->setId("stuntname_1");
        $this->frame->addComponent($this->lbl_stuntName);

        $this->lbl_description = new \ManiaLib\Gui\Elements\Label(120, 6);
        $this->lbl_description->setAlign("center", "top");
        $this->lbl_description->setPosX(30);

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("TM_Stunts/Gui/Script");
        $this->registerScript($this->script);
    }

    public function setLabels($name, $description)
    {
        $this->lbl_stuntName->setText($name);
    }
}
