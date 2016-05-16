<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * Is actually an array with the key value going from 0 to nbElements -1
 * This means you can remove elements from it and the keys will be maintained.
 *
 * @author De Cramer Oliver
 */
class BasicList extends Variable
{

    protected $type;

    /**
     *  The type of the values it accepts to save in the list
     *
     * @param \ManiaLivePlugins\eXpansion\Core\types\config\Variable $type The type
     */
    public function setType(Variable $type)
    {
        $this->type = $type;
    }

    /**
     * The type of the values the list accepts
     *
     * @return \ManiaLivePlugins\eXpansion\Core\types\config\Variable
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Adds a value to the end of the list
     *
     * @param mixed $value The value to add
     *
     * @return bool True if the variable could be added
     */
    public function addValue($value)
    {
        if ($this->type->basicValueCheck($value)) {
            $array = $this->getRawValue();
            $array[] = $this->type->castValue($value);
            $this->setRawValue($array);

            return true;
        }

        return false;
    }

    /**
     * Returns the value at the index
     *
     * @param int $index
     *
     * @return mixed the value?
     */
    public function getValue($index)
    {
        $array = $this->getRawValue();
        if ($array == null) {
            $array = array();
        }

        return isset($array[$index]) ? $array[$index] : null;
    }

    /**
     * Removes the value at the index and shifts all elements in array to fill in the gap
     *
     * @param int $index The index at which the value must be removed
     */
    public function removeValue($index)
    {
        $array = $this->getRawValue();
        unset($array[$index]);
        $this->setRawValue(array_values($array));
    }

    /**
     * @return string
     */
    public function getPreviewValues()
    {
        if ($this->getRawValue() === null) {
            return "";
        } else {
            return implode(",", $this->getRawValue());
        }
    }

}
