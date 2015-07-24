<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\ScriptTester\Gui;

/**
 * Description of TesterScript
 *
 * @author Petri
 */
class TesterScript extends \ManiaLivePlugins\eXpansion\Gui\Structures\Script{

    private $str;

    public function __construct($script) {
	$this->str = $script;
    }

   
    public function getlibScript($win, $component) {
	return "";
    }

    public function getDeclarationScript($win, $component) {
	return "";
    }

    public function getEndScript($win) {
	return $this->str;
    }

    public function getWhileLoopScript($win, $component) {
	return "";
    }

}
