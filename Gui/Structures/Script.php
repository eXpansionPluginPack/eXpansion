<?php

namespace ManiaLivePlugins\eXpansion\Gui\Structures;

/**
 * Description of Script
 *
 * @author De Cramer Oliver
 */
class Script
{

    private $_relPath = "";

    private $libs = array();

    function __construct($relPath)
    {
	$relPath = str_replace("\\", DIRECTORY_SEPARATOR, $relPath);
	$this->_relPath = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . $relPath;
    }

    /**
     * @return string The path to the script name
     */
    public function getRelPath()
    {
	return $this->_relPath;
    }

    /**
     * @param $relPath
     */
    public function setRelPath($relPath)
    {
	$this->_relPath = $relPath;
    }

    /**
     * @param string $name  The name of the parameter.
     * @param string $value The value
     */
    public function setParam($name, $value)
    {
	$this->$name = $value;
    }

    /**
     * @param $win        The window that creates the script
     * @param $component  The componenet in which it was crreated
     *
     * @return string The code of the script
     */
    public function getDeclarationScript($win, $component)
    {
	return $this->getScript($this->_relPath . '/declarationScript.php', $win, $component);
    }

    /**
     * @param $win        The window that creates the script
     * @param $component  The componenet in which it was crreated
     *
     * @return string The code of the script
     */
    public function getlibScript($win, $component)
    {
	return $this->getScript($this->_relPath . '/libScript.php', $win, $component);
    }

    /**
     * @param $win        The window that creates the script
     * @param $component  The componenet in which it was crreated
     *
     * @return string The code of the script
     */
    public function getWhileLoopScript($win, $component)
    {
	return $this->getScript($this->_relPath . '/whileLoopScript.php', $win, $component);
    }

    /**
     * @param $win        The window that creates the script
     *
     * @return string The code of the script
     */
    public function getEndScript($win)
    {
	return $this->getScript($this->_relPath . '/endDeclarationScript.php', $win, null);
    }

    /**
     * @param string $path       Path to the script
     * @param        $win        The window that creates the script
     * @param        $component  The componenet in which it was crreated
     *
     * @return string The code of the script
     */
    final protected function getScript($path, $win, $component)
    {
	$path = str_replace("\\", DIRECTORY_SEPARATOR, $path);
	if (file_exists($path)) {
	    ob_start();
	    include $path;

	    $script = ob_get_contents();
	    ob_end_clean();
	    return $script;
	}
    }

    /**
     * @return bool Should this script be added multiple times
     */
    public function multiply()
    {
	return false;
    }

    /**
     * Called at the end of the prepartion of a windows
     */
    public function reset()
    {

    }

    /**
     *
     * @param Script $lib Library to add
     */
    public function addLibrary(Script $lib){
	$this->libs[] = $lib;
    }

    /**
     *
     * @return Script[]
     */
    public function getLibraries(){
	return $this->libs;
    }

    /**
     * @param int $number The integer
     *
     * @return string The int transformed into a string
     */
    function getNumber($number)
    {
	return number_format((float)$number, 2, '.', '');
    }

}

?>
