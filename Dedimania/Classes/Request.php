<?php

namespace ManiaLivePlugins\eXpansion\Dedimania\Classes;

class Request {

    private $requests = array();

    function __construct($method, $args) {
        $this->requests[] = $this->generate($method, $args);
    }

    function add($method, $args) {
        $this->requests[] = $this->generate($method, $args);        
    }
    
    function generate($method, $args) {
        if ($args == null)
            return array('methodName' => $method);
        return array('methodName' => $method, 'params' => $args);
    }

    function getXml() {

        $params = $this->requests;

        array_push($params, $this->generate("dedimania.WarningsAndTTR", null));

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>'
                . "\n<methodCall>\n<methodName>system.multicall</methodName>\n<params>\n";
        foreach ($params as $key => $param) {

            $xml .= "<param>\n<value>";
            $v = new IXR_Value($param);
            $xml .= $v->getXml();
            $xml .= "</value>\n</param>\n";
        }

        $xml .= "</params>\n</methodCall>";
        return $xml;
    }

}

/*
 * Originally from:
  IXR - The Incutio XML-RPC Library - (c) Incutio Ltd 2002
  Version 1.61 - Simon Willison, 11th July 2003 (htmlentities -> htmlspecialchars)
  Site:   http://scripts.incutio.com/xmlrpc/
  Manual: http://scripts.incutio.com/xmlrpc/manual.php
  Made available under the Artistic License: http://www.opensource.org/licenses/artistic-license.php
 * 	  
 */
class IXR_Base64 {
	private $data;
        
	function __construct($data) {
		$this->data = $data;               
	}

	function getXml() {
		return '<base64>'.base64_encode($this->data).'</base64>';
	}
}

class IXR_Value {

    private $data;
    private $type;

    function __construct($data, $type = false) {
        $this->data = $data;
        if (!$type) {
            $type = $this->calculateType();
        }
        $this->type = $type;
        if ($type == 'struct') {
            // warning : in some case changing directly the array values modify also the last entry of original array !!! so build a new array...
            $this->data = array();
            // Turn all the values in the array into new IXR_Value objects
            foreach ($data as $key => $value) {
                $this->data[$key] = new IXR_Value($value);
            }
        }
        if ($type == 'array') {
            // warning : in some case changing directly the array values modify also the last entry of original array !!! so build a new array...
            $this->data = array();
            for ($i = 0, $j = count($data); $i < $j; $i++) {
                $this->data[$i] = new IXR_Value($data[$i]);
            }
        }
    }

    function calculateType() {
        if ($this->data === true || $this->data === false) {
            return 'boolean';
        }
        if (is_integer($this->data)) {
            return 'int';
        }
        if (is_double($this->data)) {
            return 'double';
        }
        // Deal with IXR object types base64 and date
        if (is_object($this->data) && is_a($this->data, 'IXR_Date')) {
            return 'date';
        }
        if (is_object($this->data) && is_a($this->data, 'ManiaLivePlugins\eXpansion\Dedimania\Classes\IXR_Base64')) {
            return 'base64';
        }
        // If it is a normal PHP object convert it into a struct
        if (is_object($this->data)) {
            $this->data = get_object_vars($this->data);
            return 'struct';
        }
        if (!is_array($this->data)) {
            return 'string';
        }
        // We have an array - is it an array or a struct?
        if ($this->isStruct($this->data)) {
            return 'struct';
        } else {
            return 'array';
        }
    }

    function getXml() {
        // Return XML for this value
        switch ($this->type) {
            case 'boolean':
                return '<boolean>' . ($this->data ? '1' : '0') . '</boolean>';
                break;
            case 'int':
                return '<int>' . $this->data . '</int>';
                break;
            case 'double':
                return '<double>' . $this->data . '</double>';
                break;
            case 'string':
                return '<string>' . htmlspecialchars($this->data) . '</string>';
                break;
            case 'array':
                $xml = '';
                foreach ($this->data as $item) {
                    $xml .= '<value>' . $item->getXml() . '</value>';
                }
                return '<array><data>' . $xml . '</data></array>';
                break;
            case 'struct':
                $xml = '';
                foreach ($this->data as $name => $value) {
                    $xml .= '<member><name>' . $name . '</name><value>' . $value->getXml() . '</value></member>';
                }
                return '<struct>' . $xml . '</struct>';
                break;
            case 'date':
            case 'base64':                
                return $this->data->getXml();
                break;
        }
        return false;
    }

    function isStruct($array) {
        // Nasty function to check if an array is a struct or not
        $expected = 0;
        foreach ($array as $key => $value) {
            if ((string) $key != (string) $expected) {
                return true;
            }
            $expected++;
        }
        return false;
    }

}

?>