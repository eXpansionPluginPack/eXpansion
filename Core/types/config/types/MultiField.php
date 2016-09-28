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

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * It is an object with specific variable types in each key
 *
 * @author De Cramer Oliver
 */
class MultiField extends Variable
{


    protected $variableType;

    public function registerNewType($name, Variable $type)
    {
        $this->variableType[$name] = $type;
    }

    public function setValue($name, $value)
    {
        if (isset($this->variableType[$name]) && $this->variableType[$name]->basicValueCheck($value)) {
            $values = $this->getRawValue();
            $values[$name] = $value;
            $this->setRawValue($value);

            return true;
        }

        return false;
    }

    public function getValue($name)
    {
        return isset($this->variableType[$name]) ? $this->variableType[$name] : null;
    }

    function getPreviewValues()
    {
        return '';
    }

    public function hasConfWindow()
    {
        return true;
    }
}
