<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

/**
 * Description of String
 *
 * @author De Cramer Oliver
 */
class String extends \ManiaLivePlugins\eXpansion\Core\types\config\Variable
{

	/**
	 * Sets the string value
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function setValue($value)
	{
		if ($this->basicValueCheck($value))
			return $this->setRawValue($value);

		return false;
	}

	/**
	 * Returns value to preview;
	 *
	 * @return mixed
	 */
	public function getPreviewValues()
	{
		return $this->getRawValue();
	}

}

?>
