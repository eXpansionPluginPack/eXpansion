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

namespace ManiaLivePlugins\eXpansion\Gui\Structures\Config;


use ManiaLib\Utils\Singleton;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeFloat;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeInt;
use ManiaLivePlugins\eXpansion\Core\types\config\types\MultiField;
use ManiaLivePlugins\eXpansion\Core\types\config\types\TypeString;

class QuadConfigVariable extends MultiField
{

    //quad styles
    const VAR_STYLE = "style";
    const VAR_SUB_STYLE = "sub_style";

    const VAR_IMG_NORMAL = "img";
    const VAR_IMG_FOCUS = "img_focus";

    const VAR_OPACITY = "opacity";
    const VAR_COLORIZE = "colorize";
    const VAR_ALIGN_VERTICAL = "align_vertical";
    const VAR_ALIGN_HORIZONTAL = "align_horizontal";

    const VAR_SIZE_OFFSET_X = "size_offset_x";
    const VAR_SIZE_OFFSET_Y = "size_offset_y";
    const VAR_POS_OFFSET_X = "size_pos_x";
    const VAR_POS_OFFSET_Y = "size_pos_y";


    public function __construct($name, $visibleName = "", $configInstance = null, $scope = true, $showMain = true)
    {
        parent::__construct($name, $visibleName, $configInstance, $scope, $showMain);

        $floatType = new TypeFloat('float');
        $stringType = new TypeString('string');

        //Declare sub variables
        $this->registerNewType(self::VAR_STYLE, $stringType);
        $this->registerNewType(self::VAR_SUB_STYLE, $stringType);
        $this->registerNewType(self::VAR_IMG_NORMAL, $stringType);
        $this->registerNewType(self::VAR_IMG_FOCUS, $stringType);

        $this->registerNewType(self::VAR_OPACITY, $floatType);
        $this->registerNewType(self::VAR_COLORIZE, $stringType);
        $this->registerNewType(self::VAR_ALIGN_VERTICAL, $stringType);
        $this->registerNewType(self::VAR_ALIGN_HORIZONTAL, $stringType);

        $this->registerNewType(self::VAR_SIZE_OFFSET_X, $floatType);
        $this->registerNewType(self::VAR_SIZE_OFFSET_Y, $floatType);
        $this->registerNewType(self::VAR_POS_OFFSET_X, $floatType);
        $this->registerNewType(self::VAR_POS_OFFSET_Y, $floatType);
    }

} 