<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of ServerItem
 *
 * @author oliverde8
 */
class ServerItem extends \ManiaLivePlugins\eXpansion\Gui\Control {

    private static $bgStyle = 'Bgs1';
    private static $bgStyles = array('BgList', 'BgListLine');
    private $bg_main;
    private $label_sname;
    //Server information
    private $icons_frame;
    private $icon_game, $icon_player, $icon_specs, $icon_ladder;
    private $label_nbPlayers, $label_nbSpecs, $label_ladder;
    //Server Map information
    private $info_frame;
    private $icon_map, $icon_author, $icon_envi, $icon_atime;
    private $label_map, $label_author, $label_envi, $label_atime;
    private $icon_jspec;
    private $icon_jplayer;
    private $icon_fav;
    private $icon_info;
    private $frame_main, $frame_info, $frame_map, $frame_server, $frame;

    function __construct($indexNumber, $ctr, \ManiaLivePlugins\eXpansion\ServerNeighborhood\Server $server) {
	$sizeY = 14;
	$YSpace = 0.2;
	$bsize = $sizeY / 2;

	$this->bg_main = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button($this->getSizeX(), $sizeY);	
	$this->bg_main->setIcon("Bgs1", "BgCardOnline");	
	$this->bg_main->setDescription("Click to Join server", 40);
	$this->bg_main->setManialink('maniaplanet://#qjoin=' . $server->getServer_data()->server->login . '@' . $server->getServer_data()->server->title);
	$this->addComponent($this->bg_main);

	$this->frame_main = new \ManiaLive\Gui\Controls\Frame();
	$this->addComponent($this->frame_main);

	$this->createMain($server, $sizeY);
	$this->createMap($server, $sizeY);
// 	$this->createInfos($server, $sizeY);

	$this->setSize($this->getSizeX(), $sizeY + $YSpace);

	if ($ctr != null) {
	   $action = $this->createAction(array($ctr, 'showServerPlayers'), $server);
	   $this->bg_main->setAction($action, $server);
	   $this->bg_main->setManialink(null);
	   $this->bg_main->setDescription("Click to show more info", 40);
	    // $this->icon_info->setAction($action, $server);
	}
	$this->sizeY = $sizeY;

	foreach ($this->getComponents() as $cmp) {
	    if ($cmp instanceof ListBackGround) {
		
	    } else {
		$cmp->setPosY($cmp->getPosY() + 7);
	    }
	}
    }

    public function onResize($oldX, $oldY) {
	$sizeY = 14;

	$this->bg_main->setSize($this->getSizeX(), $sizeY - 0.5);
	$this->bg_main->setAlign("left", "top");
	$this->bg_main->setScale(1);
	$this->bg_main->setPosY(0);
	$this->map_frame->setPosX($this->getSizeX() - 2);
	
	parent::onResize($oldX, $oldY);
    }

    private function createMain(\ManiaLivePlugins\eXpansion\ServerNeighborhood\Server $server, $sizeY) {
	$iSize = $sizeY / 3;

	$server_frame = new \ManiaLive\Gui\Controls\Frame(3, -1);
	$server_frame->setLayout(new \ManiaLib\Gui\Layouts\Column());

	$frame = new \ManiaLive\Gui\Controls\Frame();
	$frame->setSize(32, $iSize);
	$frame->setScale(0.8);
	$frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

	$this->label_envi = new \ManiaLib\Gui\Elements\Label(20, $iSize);
	$this->label_envi->setScale(0.8);
	$this->label_envi->setText('$111$i' . $server->getServer_data()->current->map->environment);
	$server_frame->addComponent($this->label_envi);

	$this->label_sname = new \ManiaLib\Gui\Elements\Label(50, $iSize);
	$this->label_sname->setAlign('left', 'top');
	$this->label_sname->setStyle("TextRaceMessageBig");
	$this->label_sname->setTextSize(2);
	$this->label_sname->setText($server->getServer_data()->server->name);
	$server_frame->addComponent($this->label_sname);

	$this->icon_player = new \ManiaLib\Gui\Elements\Icons64x64_1($iSize, $iSize);
	$this->icon_player->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::Buddy);
	$frame->addComponent($this->icon_player);

	$this->label_nbPlayers = new \ManiaLib\Gui\Elements\Label($iSize * 3, $sizeY * 0.6 + 0.6);
	$this->label_nbPlayers->setText($server->getServer_data()->server->players->current . '/' . $server->getServer_data()->server->players->maximum);
	$this->label_nbPlayers->setTextColor('111');
	if ((int) $server->getServer_data()->server->players->current == (int) $server->getServer_data()->server->players->maximum)
	    $this->label_nbPlayers->setTextColor('F00');
	$frame->addComponent($this->label_nbPlayers);

	$this->icon_specs = new \ManiaLib\Gui\Elements\Icons64x64_1($iSize, $iSize);
	$this->icon_specs->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::IconPlayers);
	$frame->addComponent($this->icon_specs);

	$this->label_nbSpecs = new \ManiaLib\Gui\Elements\Label($iSize * 3, $sizeY * 0.6 + 0.6);
	$this->label_nbSpecs->setTextColor('111');
	$this->label_nbSpecs->setText($server->getServer_data()->server->spectators->current . '/' . $server->getServer_data()->server->spectators->maximum);
	if ((int) $server->getServer_data()->server->players->current == (int) $server->getServer_data()->server->players->maximum)
	    $this->label_nbSpecs->setTextColor('F00');
	$frame->addComponent($this->label_nbSpecs);

	$this->icon_ladder = new \ManiaLib\Gui\Elements\Icons128x128_1($iSize, $iSize);
	$this->icon_ladder->setSubStyle("LadderPoints");
	$frame->addComponent($this->icon_ladder);

	$this->label_ladder = new \ManiaLib\Gui\Elements\Label($iSize * 3, $iSize);
	$this->label_ladder->setTextColor("111");
	$this->label_ladder->setText($server->getServer_data()->server->ladder->minimum . ' - ' . $server->getServer_data()->server->ladder->maximum . "k");
	$frame->addComponent($this->label_ladder);

	$server_frame->addComponent($frame);

	$this->frame_server = $server_frame;

	$this->frame_main->addComponent($server_frame);
    }

    public function createMap(\ManiaLivePlugins\eXpansion\ServerNeighborhood\Server $server, $sizeY) {

	$iSize = $sizeY / 3;

	$map_frame = new \ManiaLive\Gui\Controls\Frame($this->getSizeX() - 2, -1);
	$map_frame->setAlign("right", "top");
	$map_frame->setLayout(new \ManiaLib\Gui\Layouts\Column());


	$this->label_map = new \ManiaLib\Gui\Elements\Label(50, $iSize);
	$this->label_map->setAlign('right', 'top');
	$this->label_map->setStyle("TextRaceMessageBig");
	$this->label_map->setTextSize(2);
	$this->label_map->setText('$111' . $server->getServer_data()->current->map->name);
	$map_frame->addComponent($this->label_map);

	$this->label_author = new \ManiaLib\Gui\Elements\Label(20, $iSize);
	$this->label_author->setAlign('right', 'top');
	$this->label_author->setText('$111' . $server->getServer_data()->current->map->author);
	$map_frame->addComponent($this->label_author);

	$this->label_atime = new \ManiaLib\Gui\Elements\Label(20, $iSize);
	$this->label_atime->setAlign('right', 'top');
	$this->label_atime->setTextSize(1);
	$this->label_atime->setText('$111' . $server->getServer_data()->current->map->authortime);
	$map_frame->addComponent($this->label_atime);

	$this->map_frame = $map_frame;
	$this->addComponent($this->map_frame);
    }

    public function createInfos($server, $sizeY) {
	$this->frame_info = new \ManiaLive\Gui\Controls\Frame();
	$this->frame_info->setSize(32, 14);
	$this->frame_info->setLayout(new \ManiaLib\Gui\Layouts\Column());

	$this->frame_main->addComponent($this->frame_info);

	$join = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(24, 6);
	$join->setScale(0.6);
	$join->setText("Join");
	$this->frame_info->addComponent($join);

	$spec = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(24, 6);
	$spec->setScale(0.6);
	$spec->setText("Spec");
	$this->frame_info->addComponent($spec);


	$fav = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(24, 6);
	$fav->setScale(0.6);
//	$fav->setStyle("Icons64x64_1");
//	$fav->setSubStyle("StateFavourite");
	$fav->setText("Fav");
	$this->frame_info->addComponent($fav);

	/* $this->icon_info = new \ManiaLib\Gui\Elements\Quad($bsize, $bsize);
	  $this->icon_info->setStyle("Icons64x64_1");
	  $this->icon_info->setSubStyle("TrackInfo");
	  $this->icon_info->setPosY(-$sizeY / 2 + $this->icon_info->getSizeY() / 2);
	  $this->addComponent($this->icon_info); */


	//$this->icon_jspec->setManialink('maniaplanet://#spectate=' . $server->getServer_data()->server->login . '@' . $server->getServer_data()->server->title);
	//$this->icon_fav->setManialink('maniaplanet://#addfavourite=' . $server->getServer_data()->server->login);
    }

    public function destroy() {
	parent::destroy();
    }

}

?>
