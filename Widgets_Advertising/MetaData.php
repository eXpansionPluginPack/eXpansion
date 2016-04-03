<?php
/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
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


use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;
use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;

class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {
        parent::onBeginLoad();
        $this->setName("Widget: Simple Advert");
        $this->setDescription("Provides a Custom Widget to put your advertisement in");
        $this->setGroups(array('Widgets', 'Tools'));
        $config = Config::getInstance();

        $var = new BasicList('noAdUsers', "Hide ads for users", $config, false, false);
        $var->setGroup("Settings");
        $var->setType(new TypeString(""));
        $var->setDefaultValue(array());
        $this->registerVariable($var);

        for ($x = 1; $x <= 5; $x++) {

            $var = new Boolean('active_' . $x, 'Active', $config, false, false);
            $var->setDefaultValue($x == 1);
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);

            $var = new TypeString('imageUrl_' . $x, 'Image Url', $config, false, false);
            $var->setDefaultValue('http://reaby.kapsi.fi/ml/exp_small.png');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);

            $var = new TypeString('imageFocusUrl_' . $x, 'Image Url on Focus', $config, false, false);
            $var->setDefaultValue('http://reaby.kapsi.fi/ml/exp_small.png');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);


            $var = new TypeString('url_' . $x, 'External link URL for click', $config, false, false);
            $var->setDefaultValue('');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);


            $var = new TypeString('manialink_' . $x, 'Manialink URL for click', $config, false, false);
            $var->setDefaultValue('');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);


            $var = new TypeInt('imageSizeX_' . $x, 'Image Size X', $config, false, false);
            $var->setDescription('In Px');
            $var->setDefaultValue('512');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);

            $var = new TypeInt('imageSizeY_' . $x, 'Image Size Y', $config, false, false);
            $var->setDescription('In Px');
            $var->setDefaultValue('128');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);

            $var = new TypeInt('size_' . $x, 'Display Size', $config, false, false);
            $var->setDescription('In maniaplanet display units');
            $var->setDefaultValue('30');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);

            $var = new TypeInt('x_' . $x, 'Position X', $config, false, false);
            $var->setDefaultValue('-30');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);

            $var = new TypeInt('y_' . $x, 'Position Y', $config, false, false);
            $var->setDefaultValue('90');
            $var->setGroup("Widget#$x");
            $this->registerVariable($var);
        }

    }
} 