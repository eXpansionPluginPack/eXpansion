<?php

/**
 * @author      Petri
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

namespace ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Gui\Controls;

use ManiaLib\Gui\Elements\Quad;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;
use ManiaLivePlugins\eXpansion\Widgets_TM_topPanel\Structures\Rating;

class MapRatings extends Control
{

	/** @var Quad */
	protected $yes_quad, $no_bg_quad, $no_quad;

	protected $lbl_yes, $lbl_no, $lbl_info, $lbl_title;

	protected $div;

	public function __construct($sizeX, $sizeY = 9)
	{
		$div = $sizeX / 3;
		$this->div = $div;

		$labelProto = new DicoLabel($div * 2, 4.3);
		$labelProto->setTextSize(1);
		$labelProto->setPosition(($div * 2 / 2), -2.25);
		$labelProto->setAlign("center", "center");
		$labelProto->setTextColor("000");

		$quadProto = new Quad($div * 2, 4.3);
		$quadProto->setAlign("left", "center");
		$quadProto->setPosition(0, -2.25);
		$quadProto->setBgcolor("FFFA");

		$this->yes_bg_quad = clone $quadProto;
		$this->yes_quad = clone $quadProto;
		$this->yes_quad->setId("yesQ");

		$this->no_bg_quad = clone $quadProto;
		$this->no_bg_quad->setPosY(-6.5);

		$this->no_quad = clone $quadProto;
		$this->no_quad->setPosY(-6.5);
		$this->no_quad->setId("noQ");


		$this->lbl_yes = clone $labelProto;
		$this->lbl_yes->setText("Yes");

		$this->lbl_no = clone $labelProto;
		$this->lbl_no->setPosY(-6.45);
		$this->lbl_no->setText("No");

		$infoProto = new DicoLabel($div * 2, 4.5);
		$infoProto->setTextSize(1);
		$infoProto->setPosition(($div * 3) - ($div / 2), -2.25);
		$infoProto->setAlign("center", "center");
		$infoProto->setTextColor("fff");
		$infoProto->setStyle("TextRaceChrono");

		$this->lbl_title = clone $infoProto;
		$this->lbl_title->setText("Ratings");

		$this->lbl_info = clone $infoProto;
		$this->lbl_info->setPosY(-6.45);
		$this->lbl_info->setId("rating");
		$this->lbl_info->setText("");

		$this->addComponent($this->yes_bg_quad);
		$this->addComponent($this->yes_quad);
		$this->addComponent($this->no_bg_quad);
		$this->addComponent($this->no_quad);

		$this->addComponent($this->lbl_yes);
		$this->addComponent($this->lbl_no);
		$this->addComponent($this->lbl_info);
		$this->addComponent($this->lbl_title);
		$this->setSize($sizeX, $sizeY);
	}

	public function setRating($rating)
	{
		if ($rating) {
			$yesSize = ($rating->yes / $rating->total) * ($this->div * 2);
			$noSize = ($rating->no / $rating->total) * ($this->div * 2);

			$this->yes_quad->setSizeX($yesSize);
			$this->no_quad->setSizeX($noSize);

			$this->lbl_info->setText($rating->yes . " /" . $rating->no);
		}
	}

}
