<?php

namespace ManiaLivePlugins\eXpansion\Gui;

use ManiaLivePlugins\eXpansion\Helpers\Storage;
use Maniaplanet\DedicatedServer\Structures\GameInfos;

/**
 * @author       Oliver de Cramer (oliverde8 at gmail.com)
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
class Widgets extends \ManiaLib\Utils\Singleton
{

    const config_default = 'default';
    const config_trackmania = Storage::TITLE_SIMPLE_TM;
    const config_shootmania = Storage::TITLE_SIMPLE_SM;

    public $DedimaniaPanel_posX = array(self::config_default => -160,
        GameInfos::GAMEMODE_ROUNDS => -161,
        GameInfos::GAMEMODE_TEAM => -161,
        GameInfos::GAMEMODE_LAPS => -161,
        GameInfos::GAMEMODE_CUP => -161);
    public $DedimaniaPanel_posY = array(self::config_default => 64,
        GameInfos::GAMEMODE_ROUNDS => 63,
        GameInfos::GAMEMODE_TEAM => 63,
        GameInfos::GAMEMODE_LAPS => 63,
        GameInfos::GAMEMODE_CUP => 63);
    public $DedimaniaPanel_nbFields = array(self::config_default => 20,
        GameInfos::GAMEMODE_ROUNDS => 12,
        GameInfos::GAMEMODE_TEAM => 12,
        GameInfos::GAMEMODE_LAPS => 12,
        GameInfos::GAMEMODE_CUP => 12);
    public $DedimaniaPanel_nbFirstFields = array(self::config_default => 5,
        GameInfos::GAMEMODE_ROUNDS => 5,
        GameInfos::GAMEMODE_TEAM => 5,
        GameInfos::GAMEMODE_LAPS => 5,
        GameInfos::GAMEMODE_CUP => 5);

    public $CombinedPanel_posX = array(self::config_default => 120,
        GameInfos::GAMEMODE_ROUNDS => -161,
        GameInfos::GAMEMODE_TEAM => -161,
        GameInfos::GAMEMODE_LAPS => -161,
        GameInfos::GAMEMODE_CUP => -161);
    public $CombinedPanel_posY = array(self::config_default => 63,
        GameInfos::GAMEMODE_ROUNDS => 63,
        GameInfos::GAMEMODE_TEAM => 63,
        GameInfos::GAMEMODE_LAPS => 63,
        GameInfos::GAMEMODE_CUP => 63);
    public $CombinedPanel_nbFields = array(self::config_default => 13,
        GameInfos::GAMEMODE_ROUNDS => 13,
        GameInfos::GAMEMODE_TEAM => 13,
        GameInfos::GAMEMODE_LAPS => 13,
        GameInfos::GAMEMODE_CUP => 13);
    public $CombinedPanel_nbFirstFields = array(self::config_default => 10,
        GameInfos::GAMEMODE_ROUNDS => 10,
        GameInfos::GAMEMODE_TEAM => 10,
        GameInfos::GAMEMODE_LAPS => 10,
        GameInfos::GAMEMODE_CUP => 10);



    public $LocalRecordsPanel_posX = array(self::config_default => 120,
        GameInfos::GAMEMODE_ROUNDS => -161,
        GameInfos::GAMEMODE_TEAM => -161,
        GameInfos::GAMEMODE_LAPS => -161,
        GameInfos::GAMEMODE_CUP => -161);
    public $LocalRecordsPanel_posY = array(self::config_default => 64,
        GameInfos::GAMEMODE_ROUNDS => 9,
        GameInfos::GAMEMODE_TEAM => 9,
        GameInfos::GAMEMODE_LAPS => 9,
        GameInfos::GAMEMODE_CUP => 9);
    public $LocalRecordsPanel_nbFields = array(self::config_default => 15,
        GameInfos::GAMEMODE_ROUNDS => 12,
        GameInfos::GAMEMODE_TEAM => 12,
        GameInfos::GAMEMODE_LAPS => 12,
        GameInfos::GAMEMODE_CUP => 12);
    public $LocalRecordsPanel_nbFirstFields = array(self::config_default => 5,
        GameInfos::GAMEMODE_ROUNDS => 3,
        GameInfos::GAMEMODE_TEAM => 3,
        GameInfos::GAMEMODE_LAPS => 3,
        GameInfos::GAMEMODE_CUP => 3);
    public $LiveRankingsPanel_posX = array(self::config_default => 120,
        GameInfos::GAMEMODE_ROUNDS => 118,
        GameInfos::GAMEMODE_TEAM => 118,
        GameInfos::GAMEMODE_LAPS => 118,
        GameInfos::GAMEMODE_CUP => 118);
    public $LiveRankingsPanel_posY = array(self::config_default => -1,
        GameInfos::GAMEMODE_ROUNDS => 42,
        GameInfos::GAMEMODE_TEAM => 42,
        GameInfos::GAMEMODE_LAPS => 42,
        GameInfos::GAMEMODE_CUP => 42);
    public $LiveRankingsPanel_nbFields = array(self::config_default => 10,
        GameInfos::GAMEMODE_ROUNDS => 22,
        GameInfos::GAMEMODE_TEAM => 22,
        GameInfos::GAMEMODE_LAPS => 22,
        GameInfos::GAMEMODE_CUP => 22);
    public $LiveRankingsPanel_nbFirstFields = array(self::config_default => 3,
        GameInfos::GAMEMODE_ROUNDS => 10,
        GameInfos::GAMEMODE_TEAM => 10,
        GameInfos::GAMEMODE_LAPS => 10,
        GameInfos::GAMEMODE_CUP => 10);

    public $SkipandResButtons_posX = array(self::config_default => 96.5,
        self::config_shootmania => -70);
    public $SkipandResButtons_posY = array(self::config_default => 75,
        self::config_shootmania => 90);

    public $CurrentMapWidget_posX = array(self::config_default => -80);
    public $CurrentMapWidget_posY = array(self::config_default => 65);

    public $NextMap_posX = array(self::config_default => 20);
    public $NextMap_posY = array(self::config_default => 65);

    public $MapRatingsWidget_posX = array(self::config_default => 128,
        self::config_shootmania => 38);
    public $MapRatingsWidget_posY = array(self::config_default => 75,
        self::config_shootmania => 90);

    public $MXMapRatingWidget_posX = array(
        self::config_default => 128,
        self::config_shootmania => 38);
    public $MXMapRatingWidget_posY = array(
        self::config_default => 75,
        self::config_shootmania => 90);

    public $ManiaExchangePanel_posX = array(self::config_default => -160);
    public $ManiaExchangePanel_posY = array(self::config_default => 80);
    public $ManiaExchangePanel_autoCloseTimeout = array(self::config_default => 0);

    public $FaqWidget_posX = array(self::config_default => -161);
    public $FaqWidget_posY = array(self::config_trackmania => 76,
        self::config_shootmania => -31);

    public $DonatePanel_posX = array(self::config_default => -160);
    public $DonatePanel_posY = array(self::config_trackmania => -42,
        self::config_shootmania => -37);
    public $DonatePanel_autoCloseTimeout = array(self::config_default => 0);

    public $AdminPanel_posX = array(self::config_default => -160);
    public $AdminPanel_posY = array(self::config_default => -48);
    public $AdminPanel_autoCloseTimeout = array(self::config_default => 0);

    public $PersonalChatWidget_posX = array(self::config_default => -160);
    public $PersonalChatWidget_posY = array(self::config_default => -56);
    public $PersonalChatWidget_autoCloseTimeout = array(self::config_default => 0);

    public $RoundScoreWidget_posX = array(self::config_default => -126);
    public $RoundScoreWidget_posY = array(self::config_default => 58);

    public $MapinfoWidget_posX = array(self::config_default => 115);
    public $MapinfoWidget_posY = array(self::config_default => 88);

    public $ServerinfoWidget_posX = array(self::config_default => -160);
    public $ServerinfoWidget_posY = array(self::config_default => 88);

    public $BestCheckPointsWidget_posX = array(self::config_default => -112);
    public $BestCheckPointsWidget_posY = array(self::config_default => 90);

    public $AroundMePanel_posX = array(self::config_default => -15);
    public $AroundMePanel_posY = array(self::config_default => -70);

}
