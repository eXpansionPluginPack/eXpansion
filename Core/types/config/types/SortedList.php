<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * Is actually an array with the key value going from 0 to nbElements -1 
 * This means you can remove elements from it and the keys will be maintained. 
 *
 * @author De Cramer Oliver
 */
class SortedList extends BasicList
{

	private $lowToHight = true;

	/**
	 * Adds a value to the end of the list
	 * 
	 * @param mixed $value The value to add
	 * @return bool True if the variable could be added
	 */
	public function addValue($value)
	{
		if ($this->type->basicValueCheck($value)) {
			$array = $this->getRawValue();
			$array[] = $this->type->castValue($value);

			if ($this->lowToHight && sort($array))
				$this->setRawValue($array);
			else if (!$this->lowToHight && rsort($array))
				$this->setRawValue($array);
			else
				return false;

			return true;
		}
		return false;
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
		$array = array_values($array);
		if ($this->lowToHight && sort($array))
			$this->setRawValue($array);
		else if (!$this->lowToHight && rsort($array))
			$this->setRawValue($array);
		else
			return false;
		return true;
	}

	public function getPreviewValues()
	{
		return implode(",", $this->getRawValue());
	}

	/**
	 * 
	 * @param string $order : "asc" or "desc";
	 */
	public function setOrder($order)
	{
		switch (strtolower($order)) {
			case "desc":
				$this->lowToHight = false;
				break;
			default:
				$this->lowToHight = true;
				break;
		}
	}

}

?>
