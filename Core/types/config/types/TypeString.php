<?php
namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * Description of String
 *
 * @author De Cramer Oliver
 */
class TypeString extends Variable
{

    /**
     * Sets the string value
     *
     * @param $value
     *
     * @return string|false
     */
    public function setValue($value)
    {
        if ($this->basicValueCheck($value)) {
            return $this->setRawValue($value);
        }
        return false;
    }

    /**
     * Returns value to preview;
     *
     * @return string
     */
    public function getPreviewValues()
    {
        return $this->getRawValue();
    }

    /**
     * forces class to string
     */
    public function __toString()
    {
        return strval($this->getRawValue());
    }
}
