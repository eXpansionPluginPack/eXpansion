<?php
/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
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

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising;


use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;

class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData{

	public function onBeginLoad() {
		parent::onBeginLoad();
		$this->setName("Simple Advert");
		$this->setDescription("Provides a Custom Widget to put your advertisement in");
		$config = Config::getInstance();

		$var = new String('imageUrl', 'Image Url', $config, false, false);
		$var->setDefaultValue('http://reaby.kapsi.fi/ml/exp_small.png');
		$this->registerVariable($var);

		$var = new String('imageUrl', 'Image Url on Focus', $config, false, false);
		$var->setDefaultValue('http://reaby.kapsi.fi/ml/exp_small.png');
		$this->registerVariable($var);

		$var = new Int('imageSizeX', 'Image Size X', $config, false, false);
		$var->setDescription('In Px');
		$var->setDefaultValue('512');
		$this->registerVariable($var);

		$var = new Int('imageSizeY', 'Image Size X', $config, false, false);
		$var->setDescription('In Px');
		$var->setDefaultValue('128');
		$this->registerVariable($var);

		$var = new Int('size', 'Display Size X', $config, false, false);
		$var->setDescription('In maniaplanet display units');
		$var->setDefaultValue('30');
		$this->registerVariable($var);

		$var = new Int('x', 'Position X', $config, false, false);
		$var->setDefaultValue('-30');
		$this->registerVariable($var);

		$var = new Int('y', 'Position Y', $config, false, false);
		$var->setDefaultValue('90');
		$this->registerVariable($var);

	}
} 