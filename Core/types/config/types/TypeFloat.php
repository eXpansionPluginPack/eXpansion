<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

/**
 * Description of Int
 *
 * @author De Cramer Oliver
 */
class TypeFloat extends \ManiaLivePlugins\eXpansion\Core\types\config\Variable
{

    public function setValue($value)
    {
        if ($this->basicValueCheck($value))
            return $this->setRawValue((float)$value);

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

?>
