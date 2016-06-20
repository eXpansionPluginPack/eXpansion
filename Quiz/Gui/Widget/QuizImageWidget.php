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

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Widget;

use ManiaLib\Gui\Elements\Quad;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Helpers\Maniascript;

/**
 * Description of QuizImageWidget
 *
 * @author Reaby
 */
class QuizImageWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    protected $quad, $title, $bg, $script, $frame, $hiddenQuestion;

    protected function eXpOnBeginConstruct()
    {
        $this->setName("Quiz Widget");
        $this->setScriptEvents();

        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(24, 22);
        $this->addComponent($this->bg);

        $this->title = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle(24, 4);
        $this->title->setText(eXpGetMessage("Question"));
        $this->addComponent($this->title);

        $this->frame = new Frame(2, -4);
        $this->addComponent($this->frame);

        $this->quad = new \ManiaLib\Gui\Elements\Quad(24, 16);
        $this->quad->setId("image");
        $this->quad->setAttribute("class", "quad");
        $this->quad->setScriptEvents();
        $this->frame->addComponent($this->quad);

        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Quiz/Gui/Scripts/Widget");
        $this->registerScript($this->script);

    }

    protected function eXpOnEndConstruct()
    {
        $this->setSize(24, 24);
        $this->setPosition(-152, 80);
    }

    public function setImage($url)
    {
        $this->quad->setImage($url, true);
    }

    public function setImageSize($width, $height)
    {
        $this->quad->setSize($width, $height);

        $y = $height / 3;
        $x = $width / 3;

        $c = 0;
        $opacity = 0.;
        if ($this->hiddenQuestion) {
            $opacity = 1.;
        }
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                $quad = new Quad();
                $quad->setScriptEvents();
                $quad->setBgcolor("000");
                $quad->setOpacity($opacity);
                $quad->setSize($x, $y);
                $quad->setPosition($i * $x, -$j * $y);
                $quad->setId("quad_" . $c);
                $quad->setAttribute("class", "quad");
                $this->frame->addComponent($quad);
                $c++;
            }
        }
    }

    public function setHiddenQuestion($bool, $boxOrder)
    {
        $this->hiddenQuestion = $bool;
        $order = Maniascript::stringifyAsList($boxOrder);
        if ($order == "[]") {
            $order = "Integer[]";
        }
        $this->script->setParam("delay", 20);
        $this->script->setParam("boxOrder", $order);
        $this->script->setParam("reveal", "False");
        $this->script->setParam("isHidden", Maniascript::getBoolean($bool));
    }

    public function revealAnswer()
    {
        $this->script->setParam("reveal", "True");
        $this->setTimeout(5);
    }


}
