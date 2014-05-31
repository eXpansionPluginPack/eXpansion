<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Controls;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of ServerItem
 *
 * @author oliverde8
 */
class ServerItem extends \ManiaLive\Gui\Control{
    
    private static $bgStyle = 'Bgs1';
    private static $bgStyles = array('BgList', 'BgListLine');
    
    
    private $bg_main;
    private $label_sname;
    
    //Server information
    private $icons_frame;
    private $icon_status, $icon_game, $icon_player, $icon_specs, $icon_ladder;
    private $label_nbPlayers, $label_nbSpecs, $label_ladder;
    
    //Server Map information
    private $info_frame;
    private $map_frame;
    private $icon_map, $icon_author, $icon_envi, $icon_atime, $icon_gtime, $icon_stime, $icon_btime;
    private $label_map, $label_author, $label_envi, $label_atime, $label_gtime, $label_stime, $label_btime;
    
    
    private $bg_jspec;
    private $icon_jspec;
    
    private $bg_jplayer;
    private $icon_jplayer;
    
    private $bg_fav;
    private $icon_fav;
    
    private $bg_info;
    private $icon_info;
    
    function __construct($indexNumber, $ctr, \ManiaLivePlugins\eXpansion\ServerNeighborhood\Server $server) {
        
        $sizeY = 14;
        $YSpace = 0.2;
        $bsize = $sizeY/2;
          
        $this->bg_main = new ListBackGround($indexNumber, $this->getSizeX()-(3*$bsize)-1, $sizeY);
        $this->bg_main->setPosX(.5);
        $this->addComponent($this->bg_main);
        
        $this->bg_jplayer = new ListBackGround($indexNumber, $bsize,$sizeY);
        $this->addComponent($this->bg_jplayer);
        
        $this->icon_jplayer = new \ManiaLib\Gui\Elements\Quad($bsize, $bsize+1);
        $this->icon_jplayer->setStyle("Icons128x128_1");
        $this->icon_jplayer->setSubStyle("Multiplayer");
        $this->icon_jplayer->setPosY(-$sizeY/2 + $this->icon_jplayer->getSizeY()/2);
        $this->addComponent($this->icon_jplayer);
        
        $this->bg_jspec = new ListBackGround($indexNumber, $bsize,$sizeY/2-$YSpace);
        $this->addComponent($this->bg_jspec);
        
        $this->icon_jspec = new \ManiaLib\Gui\Elements\Quad($bsize, $bsize);
        $this->icon_jspec->setStyle("Icons128x128_1");
        $this->icon_jspec->setSubStyle("Buddies");
        $this->addComponent($this->icon_jspec);

        $this->bg_fav = new ListBackGround($indexNumber, $bsize,$sizeY/2-$YSpace);
        $this->addComponent($this->bg_fav);
        
        $this->icon_fav = new \ManiaLib\Gui\Elements\Quad($bsize, $bsize);
        $this->icon_fav->setStyle("Icons64x64_1");
        $this->icon_fav->setSubStyle("StateFavourite");
        $this->addComponent($this->icon_fav);
        
        $this->bg_info = new ListBackGround($indexNumber, $bsize,$sizeY);
        $this->addComponent($this->bg_info);
        
        $this->icon_info = new \ManiaLib\Gui\Elements\Quad($bsize, $bsize);
        $this->icon_info->setStyle("Icons64x64_1");
        $this->icon_info->setSubStyle("TrackInfo");
        $this->icon_info->setPosY(-$sizeY/2 + $this->icon_info->getSizeY()/2);
        $this->addComponent($this->icon_info);
        
        $this->createMain($server, $sizeY, $bsize);
        $this->createMap($server, $sizeY, $bsize);
        
        $this->setSize($this->getSizeX(), $sizeY+$YSpace);
        
        //$this->bg_jplayer->setManialink('maniaplanet://#qjoin='.$server->getServer_data()->server->login.'@'.$server->getServer_data()->server->title);
        $this->icon_jplayer->setManialink('maniaplanet://#qjoin='.$server->getServer_data()->server->login.'@'.$server->getServer_data()->server->title);
        
        //$this->bg_jspec->setManialink('maniaplanet://#spectate='.$server->getServer_data()->server->login.'@'.$server->getServer_data()->server->title);
        $this->icon_jspec->setManialink('maniaplanet://#spectate='.$server->getServer_data()->server->login.'@'.$server->getServer_data()->server->title);
        
        //$this->bg_fav->setManialink('maniaplanet://#addfavourite='.$server->getServer_data()->server->login);
        $this->icon_fav->setManialink('maniaplanet://#addfavourite='.$server->getServer_data()->server->login);
        
        if($ctr != null){
            $action = $this->createAction(array($ctr, 'showServerPlayers'), $server);
            //$this->bg_info->setAction($action, $server);
            $this->icon_info->setAction($action, $server);
        }
        $this->sizeY = $sizeY;

        foreach($this->getComponents() as $cmp){
            if($cmp instanceof ListBackGround){

            }else{
                $cmp->setPosY($cmp->getPosY() + 7);
            }
        }
    }
    
    public function onResize($oldX, $oldY) {
        $sizeY = 14;
        $YSpace = 0.5;
        $bsize = $sizeY/2;
       
        $this->bg_main->setSizeX($this->getSizeX()-(3*$bsize)-2);
        
        $this->bg_info->setPosition($this->getSizeX() - $bsize -1, 0);
        $this->icon_info->setPosX($this->bg_info->getPosX()-1);
        
        $this->bg_fav->setPosition($this->getSizeX() - 2*$bsize -1, -$sizeY/2 - .5  + 4);
        $this->icon_fav->setPosY((-$sizeY/4)*3 + $this->icon_fav->getSizeY() + 4);
        $this->icon_fav->setPosX($this->bg_fav->getPosX()-1);
        
        $this->bg_jspec->setPosition($this->getSizeX() - 2*$bsize - 1, 3);
        $this->icon_jspec->setPosY(-$sizeY/4 + $this->icon_jspec->getSizeY() +4);
        $this->icon_jspec->setPosX($this->bg_jspec->getPosX()-1);
        
        $this->bg_jplayer->setPosition($this->getSizeX() - 3*$bsize -1 , 0);
        $this->icon_jplayer->setPosX($this->bg_jplayer->getPosX()-1);
        
        $this->icon_status->setPosX($this->getSizeX()-$bsize);
        $this->icon_game->setPosX($this->getSizeX()-3*$bsize - $this->icon_game->getSizeX() - 2);
        
        $this->icons_frame->setPositionX($this->bg_main->getPosX() + $this->bg_main->getSizeX()/2 - $sizeY);
        $this->icon_specs->setPosX(($this->bg_main->getSizeX()/2-2)/2 +2 );
        $this->label_nbSpecs->setPosX($this->icon_specs->getPosX()+$this->icon_specs->getSizeX());
        $this->icon_ladder->setPosX($this->bg_main->getSizeX()/2 - 10);
        $this->label_ladder->setPosX($this->icon_ladder->getPosX()+$this->icon_specs->getSizeX());
        
        $this->label_sname->setSizeX($this->bg_main->getSizeX()/2- $sizeY);
        
        $this->icon_author->setPosX(( ($this->bg_main->getSizeX() -10)/16)*6);
        $this->label_author->setPosX($this->icon_author->getPosX()+$this->icon_author->getSizeX());
        
        $this->icon_envi->setPosX(( ($this->bg_main->getSizeX() -10)/16)*10);
        $this->label_envi->setPosX($this->icon_envi->getPosX()+$this->icon_envi->getSizeX());
        
        $this->icon_atime->setPosX(( ($this->bg_main->getSizeX() -10)/16)*13);
        $this->label_atime->setPosX($this->icon_atime->getPosX()+$this->icon_atime->getSizeX());
        
        $this->icon_gtime->setPosX(( ($this->bg_main->getSizeX() -10)/16)*7);
        $this->label_gtime->setPosX($this->icon_gtime->getPosX()+$this->icon_gtime->getSizeX());
        
        $this->icon_stime->setPosX(( ($this->bg_main->getSizeX() -10)/16)*10);
        $this->label_stime->setPosX($this->icon_stime->getPosX()+$this->icon_stime->getSizeX());
        
        $this->icon_btime->setPosX(( ($this->bg_main->getSizeX() -10)/16)*13);
        $this->label_btime->setPosX($this->icon_btime->getPosX()+$this->icon_btime->getSizeX());
        
        parent::onResize($oldX, $oldY);
    }
    
    private function createMain(\ManiaLivePlugins\eXpansion\ServerNeighborhood\Server $server, $sizeY, $bsize){
        
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setPosY(-1);
        $this->info_frame = $frame;
        $iSize = $sizeY/3 - 1;
        
        
        $this->icon_status = new \ManiaLib\Gui\Elements\Quad($sizeY+0.6, $sizeY+0.6);
        $this->icon_status->setPosY(-.2);
        if($server->getServer_data()->server->login == \ManiaLive\Data\Storage::getInstance()->serverLogin){
            $this->icon_status->setStyle('Icons128x128_1');
            $this->icon_status->setSubStyle('Back');
            $this->addComponent($this->icon_status);
        }else if($server->getServer_data()->server->private == 'true'){
            $this->icon_status->setStyle('Icons128x128_1');
            $this->icon_status->setSubStyle('Padlock');
            $this->addComponent($this->icon_status);
        }
        
        $this->icon_game = new \ManiaLib\Gui\Elements\Quad($sizeY/2+1,$sizeY/2+1);
        $this->icon_game->setPosY(-$sizeY/2 + $this->icon_game->getSizeY()/2); 
        $this->icon_game->setStyle('Icons128x32_1');
        $this->icon_game->setSubStyle(\ManiaLivePlugins\eXpansion\ServerNeighborhood\ServerNeighborhood::$gamemodes[(int)$server->getServer_data()->server->gamemode]['icon']);
        $this->addComponent($this->icon_game);
        
        $this->icon_player = new \ManiaLib\Gui\Elements\Icons64x64_1($iSize,$iSize);
        $this->icon_player->setPosX($bsize+2);
        $this->icon_player->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::Buddy);
        $frame->addComponent($this->icon_player);
        
        $this->label_nbPlayers = new \ManiaLib\Gui\Elements\Label($iSize*2, $sizeY*0.6+0.6);
        $this->label_nbPlayers->setPosX($this->icon_player->getPosX()+$this->icon_player->getSizeX());
        $this->label_nbPlayers->setPosY($this->icon_player->getPosY()-.5);
        $this->label_nbPlayers->setScale(.8);
        if((int)$server->getServer_data()->server->players->current == (int)$server->getServer_data()->server->players->maximum)
            $this->label_nbPlayers->setText ('$F00'.$server->getServer_data()->server->players->current.'/'.$server->getServer_data()->server->players->maximum);
        else 
            $this->label_nbPlayers->setText ('$111'.$server->getServer_data()->server->players->current.'/'.$server->getServer_data()->server->players->maximum);
        $frame->addComponent($this->label_nbPlayers);
        
        $this->icon_specs = new \ManiaLib\Gui\Elements\Icons64x64_1($iSize,$iSize);
        $this->icon_specs->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::IconPlayers);
        $frame->addComponent($this->icon_specs);
        
        $this->label_nbSpecs = new \ManiaLib\Gui\Elements\Label($iSize*2, $sizeY*0.6+0.6);
        $this->label_nbSpecs->setScale(.8);
        $this->label_nbSpecs->setPosY($this->icon_specs->getPosY()-.5);
        if((int)$server->getServer_data()->server->players->current == (int)$server->getServer_data()->server->players->maximum)
            $this->label_nbSpecs->setText ('$F00'.$server->getServer_data()->server->spectators->current.'/'.$server->getServer_data()->server->spectators->maximum);
        else
            $this->label_nbSpecs->setText ('$111'.$server->getServer_data()->server->spectators->current.'/'.$server->getServer_data()->server->spectators->maximum);
        $frame->addComponent($this->label_nbSpecs);
        
        $this->icon_ladder = new \ManiaLib\Gui\Elements\Icons64x64_1($iSize,$iSize);
        $this->icon_ladder->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::ToolLeague1);
        $frame->addComponent($this->icon_ladder);
        
        $this->label_ladder = new \ManiaLib\Gui\Elements\Label($iSize*2,$iSize);
        $this->label_ladder->setScale(.8);
        $this->label_ladder->setPosY($this->icon_ladder->getPosY()-.5);
        $this->label_ladder->setText('$111'.$server->getServer_data()->server->ladder->minimum.'/'.$server->getServer_data()->server->ladder->maximum);
        $frame->addComponent($this->label_ladder);
        
        $this->addComponent($frame);
        $this->icons_frame = $frame;
        
        $this->label_sname = new \ManiaLib\Gui\Elements\Label($this->bg_main->getSizeX()/2-2, $sizeY/2);
        $this->label_sname->setPosX(2);
        $this->label_sname->setPosY(-1*.6);
        $this->label_sname->setAlign('left', 'top');
        $this->label_sname->setScale(1);
        $this->label_sname->setText('$AAA'.$server->getServer_data()->server->name);
        $this->addComponent($this->label_sname);
    }
    
    public function createMap(\ManiaLivePlugins\eXpansion\ServerNeighborhood\Server $server, $sizeY, $bsize){
        
        $iSize = $sizeY/3 - 1;
        
        $map_frame = new \ManiaLive\Gui\Controls\Frame();
        $map_frame->setPosY(-$sizeY/3);
        $map_frame->setPosX(2);
        
        $this->icon_map = new \ManiaLib\Gui\Elements\Quad($iSize+1, $iSize+1);
        $this->icon_map->setStyle('Icons128x128_1');
        $this->icon_map->setSubStyle('NewTrack');
        $this->icon_map->setPosY(-$sizeY/6 + ($iSize)/2-1);
        $map_frame->addComponent($this->icon_map);
        
        $this->label_map = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_map->setScale(.8);
        $this->label_map->setPosX($this->icon_map->getPosX()+$this->icon_map->getSizeX());
        $this->label_map->setPosY($this->icon_map->getPosY()-1);
        $this->label_map->setText('$111'.$server->getServer_data()->current->map->name);
        $map_frame->addComponent($this->label_map);
        
        $this->icon_author = new \ManiaLib\Gui\Elements\Quad($iSize, $iSize);
        $this->icon_author->setStyle('Icons128x128_1');
        $this->icon_author->setSubStyle('Solo');
        $map_frame->addComponent($this->icon_author);
        
        $this->label_author = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_author->setScale(.7);
        $this->label_author->setPosY($this->icon_author->getPosY()-.5);
        $this->label_author->setText('$111'.$server->getServer_data()->current->map->author);
        $map_frame->addComponent($this->label_author);
        
        $this->icon_envi = new \ManiaLib\Gui\Elements\Quad($iSize, $iSize);
        $this->icon_envi->setStyle('Icons128x128_1');
        $this->icon_envi->setSubStyle('Nations');
        $map_frame->addComponent($this->icon_envi);
        
        $this->label_envi = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_envi->setScale(.7);
        $this->label_envi->setPosY($this->icon_author->getPosY()-.5);
        $this->label_envi->setText('$111'.$server->getServer_data()->current->map->environment);
        $map_frame->addComponent($this->label_envi);
        
        $this->icon_atime = new \ManiaLib\Gui\Elements\Quad($iSize, $iSize);
        $this->icon_atime->setStyle('MedalsBig');
        $this->icon_atime->setSubStyle('MedalAuthor');
        $map_frame->addComponent($this->icon_atime);
        
        $this->label_atime = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_atime->setScale(.7);
        $this->label_atime->setPosY($this->icon_author->getPosY()-.5);
        $this->label_atime->setText('$111'.$server->getServer_data()->current->map->authortime);
        $map_frame->addComponent($this->label_atime);
        
        $this->icon_gtime = new \ManiaLib\Gui\Elements\Quad($iSize, $iSize);
        $this->icon_gtime->setStyle('MedalsBig');
        $this->icon_gtime->setSubStyle('MedalGold');
        $this->icon_gtime->setPosY(-$sizeY/3);
        $map_frame->addComponent($this->icon_gtime);
        
        $this->label_gtime = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_gtime->setScale(.7);
        $this->label_gtime->setPosY($this->icon_gtime->getPosY()-.5);
        $this->label_gtime->setText('$111'.$server->getServer_data()->current->map->goldtime);
        $map_frame->addComponent($this->label_gtime);
        
        $this->icon_stime = new \ManiaLib\Gui\Elements\Quad($iSize, $iSize);
        $this->icon_stime->setStyle('MedalsBig');
        $this->icon_stime->setSubStyle('MedalSilver');
        $this->icon_stime->setPosY(-$sizeY/3);
        $map_frame->addComponent($this->icon_stime);
        
        $this->label_stime = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_stime->setScale(.7);
        $this->label_stime->setPosY($this->icon_stime->getPosY()-.5);
        $this->label_stime->setText('$111'.$server->getServer_data()->current->map->silvertime);
        $map_frame->addComponent($this->label_stime);
     
        $this->icon_btime = new \ManiaLib\Gui\Elements\Quad($iSize, $iSize);
        $this->icon_btime->setStyle('MedalsBig');
        $this->icon_btime->setSubStyle('MedalBronze');
        $this->icon_btime->setPosY(-$sizeY/3);
        $map_frame->addComponent($this->icon_btime);
        
        $this->label_btime = new \ManiaLib\Gui\Elements\Label(20,$iSize);
        $this->label_btime->setScale(.7);
        $this->label_btime->setPosY($this->icon_btime->getPosY()-.5);
        $this->label_btime->setText('$111'.$server->getServer_data()->current->map->bronzetime);
        $map_frame->addComponent($this->label_btime);
        
        $this->map_frame = $map_frame;
        $this->addComponent($map_frame);
    }
    
    public function destroy() {
        parent::destroy();
        $this->info_frame->destroy();
        $this->map_frame->destroy();
        $this->info_frame->destroy();
    }
}

?>
