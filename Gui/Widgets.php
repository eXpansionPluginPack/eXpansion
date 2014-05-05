<?php

namespace ManiaLivePlugins\eXpansion\Gui;

/**
* @author      Oliver de Cramer (oliverde8 at gmail.com)
* @copyright    GNU GENERAL PUBLIC LICENSE
*                     Version 3, 29 June 2007
*
* PHP version 5.3 and above
*
* LICENSE: This program is free software: you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation, either version 3 of the License, or
*  (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program.  If not, see {http://www.gnu.org/licenses/}.
*/

/**
* Description of Widgets
*
* @author De Cramer Oliver
*/
class Widgets extends \ManiaLib\Utils\Singleton {

    public $DedimaniaPanel_posX = array('default' => -160,
   1 => -161,
   3 => -161,
   4 => -161,
   5 => -161);
    public $DedimaniaPanel_posY = array('default' => 63,
   1 => 63,
   3 => 63,
   4 => 63,
   5 => 63);
    public $DedimaniaPanel_nbFields = array('default' => 20,
   1 => 12,
   3 => 12,
   4 => 12,
   5 => 12);
    public $DedimaniaPanel_nbFirstFields = array('default' => 5,
   1 => 5,
   3 => 5,
   4 => 5,
   5 => 5);
    public $LocalRecordsPanel_posX = array('default' => 120,
   1 => -161,
   3 => -161,
   4 => -161,
   5 => -161);
    public $LocalRecordsPanel_posY = array('default' => 52,
   1 => 9,
   3 => 9,
   4 => 9,
   5 => 9);
    public $LocalRecordsPanel_nbFields = array('default' => 15,
   1 => 12,
   3 => 12,
   4 => 12,
   5 => 12);
    public $LocalRecordsPanel_nbFirstFields = array('default' => 5,
   1 => 3,
   3 => 3,
   4 => 3,
   5 => 3);
    public $LiveRankingsPanel_posX = array('default' => 120,
   1 => 118,
   3 => 118,
   4 => 118,
   5 => 118);
    public $LiveRankingsPanel_posY = array('default' => -13,
   1 => 42,
   3 => 42,
   4 => 42,
   5 => 42);
    public $LiveRankingsPanel_nbFields = array('default' => 8,
   1 => 22,
   3 => 22,
   4 => 22,
   5 => 22);
    public $LiveRankingsPanel_nbFirstFields = array('default' => 3,
   1 => 10,
   3 => 10,
   4 => 10,
   5 => 10);
    
    public $SkipandResButtons_posX = array('TM' => 90,
   'SM'=> -120);
    public $SkipandResButtons_posY = array('TM' => 78,
   'SM'=> 92);
    public $NextMap_posX = array('TM' => 126,
   'SM'=> 67.5);
    public $NextMap_posY = array('TM' => 67,
   'SM'=> 92);
    
    public $CurrentMapWidget_posX = array('default' => 144);
    public $CurrentMapWidget_posY = array('default' => 83.5);

    public $MapRatingsWidget_posX = array('TM' => 128,
   'SM'=> 38);
    public $MapRatingsWidget_posY = array('TM' => 75,
   'SM'=> 90);
    
    public $ManiaExchangePanel_posX = array('default' => -160);
    public $ManiaExchangePanel_posY = array('default' => 81);
    public $ManiaExchangePanel_autoCloseTimeout = array('default' => 0);
    
    public $FaqWidget_posX = array('default' => -161);
    public $FaqWidget_posY = array('TM' => 75, 
   'SM' => -31);
    
    public $DonatePanel_posX = array('default' => -160);
    public $DonatePanel_posY = array('TM' => 69, 
   'SM' => -37);
    public $DonatePanel_autoCloseTimeout = array('default' => 0);
    
    public $AdminPanel_posX = array('default' => -160);
    public $AdminPanel_posY = array('default' => -44);
    public $AdminPanel_autoCloseTimeout = array('default' => 0);
    
    public $PersonalChatWidget_posX = array('default' => -160);
    public $PersonalChatWidget_posY = array('default' => -56);
    public $PersonalChatWidget_autoCloseTimeout = array('default' => 0);

}