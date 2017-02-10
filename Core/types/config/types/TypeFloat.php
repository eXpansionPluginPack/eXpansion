<?php
namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * Description of Float
 *
 * @author De Cramer Oliver
 */
class TypeFloat extends Variable
{

    public function setValue($value)
    {
        if ($this->basicValueCheck($value)) {
            return $this->setRawValue((float)$value);
        }

        return false;
    }

    public function basicValueCheck($value)
    {
        return parent::basicValueCheck($value) && is_numeric($value) && is_float((float)$value);
    }

    public function getPreviewValues()
    {
        return $this->getRawValue();
    }

    public function castValue($value)
    {
        return (float)$value;
    }
}
