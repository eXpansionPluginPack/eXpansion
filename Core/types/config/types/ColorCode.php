<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

/**
 * Description of String
 *
 * @author De Cramer Oliver
 */
class ColorCode extends TypeString
{

    private $sample = "Sample Text ...";

    private $digits = 3;

    private $usePrefix = true;

    public function getSample()
    {
        return $this->sample;
    }

    public function setSample($sample)
    {
        $this->sample = $sample;
    }

    public function getUsePrefix()
    {
        return $this->usePrefix;
    }

    public function setUsePrefix($bool = true)
    {
        $this->usePrefix = $bool;
    }

    public function getUseFullHex()
    {
        return $this->digits;
    }

    public function setUseFullHex($value = true)
    {
        if (is_numeric($value)) {
            $this->digits = $value;
        } else {
            $this->digits = 3;
            if ($value === true) {
                $this->digits = 6;
            }
        }
    }

    public function getPreviewValues()
    {
        return $this->getRawValue() . $this->sample;
    }
}
