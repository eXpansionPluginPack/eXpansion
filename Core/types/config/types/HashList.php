<?php

namespace ManiaLivePlugins\eXpansion\Core\types\config\types;

use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * Description of Array
 *
 * @author De Cramer Oliver
 */
class HashList extends \ManiaLivePlugins\eXpansion\Core\types\config\Variable {

    private $keyType = null;
    private $type;

    public function setType(Variable $type) {
	$this->type = $type;
    }

    public function getType() {
	return $this->type;
    }

    public function setKeyType(Variable $type) {
	$this->keyType = $type;
    }

    public function getKeyType() {
	return $this->keyType;
    }

    public function setValue($key, $value) {
	if (!$this->keyType->basicValueCheck($key)) {
	    return false;
	}
	if (!$this->type->basicValueCheck($value)) {
	    return false;
	}

	$list = $this->getRawValue();
	if ($list == null)
	    $list = array();
	$list[$key] = $value;

	return $this->setRawValue($list);
    }

    public function getValue($key) {
	$list = $this->getRawValue();
	if ($list == null)
	    $list = array();

	return isset($list[$key]) ? $list[$key] : null;
    }

    public function getPreviewValues() {
	return implode(",", $this->getRawValue());
    }

}

?>
