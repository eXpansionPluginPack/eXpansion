<?php

/**
 * @author       Petri Järvisalo
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

namespace ManiaLivePlugins\eXpansion\Widgets_Voip;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {
        parent::onBeginLoad();
        $this->setName('Widget: Mumle/Teamspeak join buttons');
        $this->setDescription("Provides a Widget to join your favourite voip server");
        $this->setGroups(array('Widgets', 'Connectivity'));

        $config = Config::getInstance();
        $providers = array("mumble", "ts");
        for ($x = 1; $x <= 2; $x++) {
            $provider = $providers[$x - 1];

            $var = new Boolean($provider . 'Active', 'Active', $config, false, false);
            $var->setDefaultValue(false);
            $var->setGroup($provider);
            $this->registerVariable($var);


            $var = new TypeString($provider . 'Host', 'Host', $config, false, false);
            $var->setDefaultValue('');
            $var->setGroup($provider);
            $this->registerVariable($var);

            $var = new TypeInt($provider . 'Port', 'Port', $config, false, false);
            $var->setDefaultValue('50490');
            $var->setGroup($provider);
            $this->registerVariable($var);


            $var = new TypeString($provider . 'ImageUrl', 'Image Url', $config, false, false);
            $var->setDefaultValue('http://reaby.kapsi.fi/ml/logos/' . $provider . '.png');
            $var->setGroup($provider);
            $this->registerVariable($var);


            $var = new TypeString($provider . 'ImageFocusUrl', 'Image Url on Focus', $config, false, false);
            $var->setDefaultValue('http://reaby.kapsi.fi/ml/logos/' . $provider . '_focus.png');
            $var->setGroup($provider);
            $this->registerVariable($var);

            $var = new TypeInt($provider . 'ImageSizeX', 'Image Size X', $config, false, false);
            $var->setDescription('In Px');
            $var->setDefaultValue(128);
            $var->setGroup($provider);
            $this->registerVariable($var);

            $var = new TypeInt($provider . 'ImageSizeY', 'Image Size X', $config, false, false);
            $var->setDescription('In Px');
            $var->setDefaultValue(128);
            $var->setGroup($provider);
            $this->registerVariable($var);

            $var = new TypeInt($provider . 'Size', 'Display Size X', $config, false, false);
            $var->setDescription('In maniaplanet display units');
            $var->setDefaultValue(30);
            $var->setGroup($provider);
            $this->registerVariable($var);

            $var = new TypeInt($provider . 'X', 'Position X', $config, false, false);
            $var->setDefaultValue(-30);
            $var->setGroup($provider);
            $this->registerVariable($var);

            $var = new TypeInt($provider . 'Y', 'Position Y', $config, false, false);
            $var->setDefaultValue(90);
            $var->setGroup($provider);
            $this->registerVariable($var);
        }
    }

}
