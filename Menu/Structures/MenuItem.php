<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ManiaLivePlugins\eXpansion\Menu\Structures;

/**
 * Description of MenuItem
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class MenuItem
{

    public $name;
    public $callback;

    public function __construct($name, callable $callback, $plugin)
    {    
        
    }
}