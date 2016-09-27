<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

/**
 * Description of BoundedInt
 *
 * @author De Cramer Oliver
 */
class BoundedTypeFloat extends TypeFloat
{

    /**
     * @var int the Maximum value allowed
     */
    private $max = null;

    /**
     * @var int the Minimum value allowed
     */
    private $min = null;

    /**
     * @return int The maximum value allowed
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return int The minimum value allowed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * Prevents values of the Variable to be greater then this value
     *
     * @param int $max Sets the maximum value allowed
     *
     * @return \ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Prevents values of the Variable to be smaller then this value
     *
     * @param int $min Sets the minumum value allowed
     *
     * @return \ManiaLivePlugins\eXpansion\Core\types\config\types\BoundedTypeInt
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    public function basicValueCheck($value)
    {
        return ($this->max == null || $value <= $this->max)
        && ($this->min == null || $value >= $this->min)
        && parent::basicValueCheck($value);
    }
}
