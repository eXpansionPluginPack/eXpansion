<?php

namespace ManiaLivePlugins\eXpansion\Adm\Gui\Windows;


use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

use \ManiaLive\Gui\Controls\Pager;
use ManiaLive\Gui\ActionHandler;

class ServerOptions extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $serverName, $serverComment, $maxPlayers, $maxSpec, $minLadder, $maxLadder, $serverPass, $serverSpecPass, $refereePass;
    private $cbPublicServer, $cbLadderServer, $cbAllowMapDl, $cbAllowp2pDown, $cbAllowp2pUp, $cbValidation, $cbReferee;
    private $frameCb;
    private $frameInputbox, $frameLadder;
    private $buttonOK, $buttonCancel;
    private $connection;
    private  $actionOK, $actionCancel;

    function onConstruct() {
        parent::onConstruct();
        $config = \ManiaLive\DedicatedApi\Config::getInstance();
        $this->connection = \DedicatedApi\Connection::factory($config->host, $config->port);
        $this->actionOK = ActionHandler::getInstance()->createAction(array($this, "serverOptionsOk"));        
        $this->actionCancel = ActionHandler::getInstance()->createAction(array($this, "serverOptionsCancel"));
        
        $this->setTitle('Server Options');


        $this->inputboxes();
        $this->checkboxes();

        $this->mainFrame->addComponent($this->frameCb);
        $this->mainFrame->addComponent($this->frameInputbox);
    }

    // Generate all inputboxes
    private function inputboxes() {
        $server = \ManiaLive\Data\Storage::getInstance()->server;
        $this->frameInputbox = new \ManiaLive\Gui\Controls\Frame();
        $this->frameInputbox->setAlign("left", "top");
        $this->frameInputbox->setLayout(new \ManiaLib\Gui\Layouts\Column());
		
		$this->serverName = new Inputbox("serverName");
        $this->serverName->setLabel("Server Name");
        $this->serverName->setText($server->name);
        $this->frameInputbox->addComponent($this->serverName);
		
        $this->serverComment = new Inputbox("serverComment");
        $this->serverComment->setLabel("Server comment");
        $this->serverComment->setText($server->comment);
        $this->frameInputbox->addComponent($this->serverComment);


        // Players Min & Max goes to same row
        $this->framePlayers = new \ManiaLive\Gui\Controls\Frame();
        $this->framePlayers->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->framePlayers->setSize(100, 11);

        $this->maxPlayers = new Inputbox("maxPlayers", 12);
        $this->maxPlayers->setLabel("Players");
        $this->maxPlayers->setText($server->currentMaxPlayers);
        $this->framePlayers->addComponent($this->maxPlayers);

        $spacer = new \ManiaLib\Gui\Elements\Quad(3, 16);
        $this->framePlayers->addComponent($spacer);

        $this->maxSpec = new Inputbox("maxSpec", 12);
        $this->maxSpec->setLabel("Spectators");
        $this->maxSpec->setText($server->currentMaxSpectators);
        $this->framePlayers->addComponent($this->maxSpec);

        $this->frameInputbox->addComponent($this->framePlayers);
        // end of players
        // Ladder Points goes to same row
        $this->frameLadder = new \ManiaLive\Gui\Controls\Frame();
        $this->frameLadder->setLayout(new \ManiaLib\Gui\Layouts\Line());
        $this->frameLadder->setSize(100, 11);

        $this->minLadder = new Inputbox("ladderMin");
        $this->minLadder->setLabel("Ladderpoints minimum");
        $this->minLadder->setText($server->ladderServerLimitMin);
        $this->frameLadder->addComponent($this->minLadder);

        $spacer = new \ManiaLib\Gui\Elements\Quad(3, 16);
        $this->frameLadder->addComponent($spacer);

        $this->maxLadder = new Inputbox("ladderMax");
        $this->maxLadder->setLabel("Ladderpoints Maximum");
        $this->maxLadder->setText($server->ladderServerLimitMax);
        $this->frameLadder->addComponent($this->maxLadder);

        $this->frameInputbox->addComponent($this->frameLadder);
        // end of ladder points
        // server password
        $this->serverPass = new Inputbox("serverPass");
        $this->serverPass->setLabel("Password for server");
        $this->serverPass->setText($server->password);
        $this->frameInputbox->addComponent($this->serverPass);

        // spectator password
        $this->serverSpecPass = new Inputbox("serverSpecPass");
        $this->serverSpecPass->setLabel("Password for spectators");
        $this->serverSpecPass->setText($server->passwordForSpectator);
        $this->frameInputbox->addComponent($this->serverSpecPass);

        // referee password
        $this->refereePass = new Inputbox("refereePass");
        $this->refereePass->setLabel("Referee password");
        $this->refereePass->setText($server->refereePassword);
        $this->frameInputbox->addComponent($this->refereePass);
    }

// Generate all checkboxes
    private function checkboxes() {
        $server = \ManiaLive\Data\Storage::getInstance()->server;

        $this->frameCb = new \ManiaLive\Gui\Controls\Frame();
        $this->frameCb->setAlign("left", "top");
        $this->frameCb->setLayout(new \ManiaLib\Gui\Layouts\Column());

        // checkbox for public server 
        $publicServer = true;
        if ($server->hideServer > 0)
            $publicServer = false;  // 0 = visible, 1 = hidden 2 = hidden from nations
        $this->cbPublicServer = new Checkbox(4, 4, 50);
        $this->cbPublicServer->setStatus($publicServer);
        $this->cbPublicServer->setText("Show Server in public server list");
        $this->frameCb->addComponent($this->cbPublicServer);

        // checkbox for ladder server
        $this->cbLadderServer = new Checkbox();
        $this->cbLadderServer->setStatus($server->currentLadderMode);
        $this->cbLadderServer->setText("Ladder server");
        $this->frameCb->addComponent($this->cbLadderServer);

        // checkbox for allow map download
        $this->cbAllowMapDl = new Checkbox(4, 4, 50);
        $this->cbAllowMapDl->setStatus($server->allowMapDownload);
        $this->cbAllowMapDl->setText("Allow map download using ingame menu");
        $this->frameCb->addComponent($this->cbAllowMapDl);

        // checkbox for p2p download
        $this->cbAllowp2pDown = new Checkbox(4, 4, 50);
        $this->cbAllowp2pDown->setStatus($server->isP2PDownload);
        $this->cbAllowp2pDown->setText("Allow Peer-2-Peer download");
        $this->frameCb->addComponent($this->cbAllowp2pDown);

        // checkbox for p2p upload
        $this->cbAllowp2pUp = new Checkbox(4, 4, 50);
        $this->cbAllowp2pUp->setStatus($server->isP2PUpload);
        $this->cbAllowp2pUp->setText("Allow Peer-2-Peer upload");
        $this->frameCb->addComponent($this->cbAllowp2pUp);

        // checkbox for changing validation seed
        $this->cbValidation = new Checkbox(4, 4, 50);
        $this->cbValidation->setStatus($server->useChangingValidationSeed);
        $this->cbValidation->setText("Allow changing validation seed");
        $this->frameCb->addComponent($this->cbValidation);

        // checkbox for Enable referee mode
        $this->cbReferee = new Checkbox(4, 4, 50);
        $this->cbReferee->setStatus($server->refereeMode);
        $this->cbReferee->setText("Enable Referee-mode");
        $this->frameCb->addComponent($this->cbReferee);

        // spacer
        $quad = new \ManiaLib\Gui\Elements\Quad(20, 16);
        $quad->setStyle(\ManiaLib\Gui\Elements\Bgs1::BgEmpty);
        $this->frameCb->addComponent($quad);

        // Ok and Cancel buttons goes for own row
        $frame = new \ManiaLive\Gui\Controls\Frame();
        $frame->setAlign("left", "top");
        $frame->setSize(40, 20);
        $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->buttonOK = new OkButton();
        $this->buttonOK->setText("Apply");
        $this->buttonOK->setAction($this->actionOK);
        $frame->addComponent($this->buttonOK);

        $this->buttonCancel = new OkButton();
        $this->buttonCancel->setText("Cancel");
        $this->buttonCancel->setAction($this->actionCancel);
        $frame->addComponent($this->buttonCancel);

        $this->frameCb->addComponent($frame);
    }

    function onDraw() {
        parent::onDraw();
		$login = $this->getRecipient();

		$this->serverName->setVisibility(AdminGroups::hasPermission($login, 'server_name')); 
		$this->serverComment->setVisibility(AdminGroups::hasPermission($login, 'server_comment'));
		$this->maxPlayers->setVisibility(AdminGroups::hasPermission($login, 'server_maxplayer'));
		$this->maxSpec->setVisibility(AdminGroups::hasPermission($login, 'server_maxspec'));
		$this->minLadder->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->maxLadder->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->serverPass->setVisibility(AdminGroups::hasPermission($login, 'server_password'));
		$this->serverSpecPass->setVisibility(AdminGroups::hasPermission($login, 'server_specpwd'));
		$this->refereePass->setVisibility(AdminGroups::hasPermission($login, 'server_refpwd'));
		$this->cbPublicServer->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->cbLadderServer->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->cbAllowMapDl->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->cbAllowp2pDown->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->cbAllowp2pUp->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->cbValidation->setVisibility(AdminGroups::hasPermission($login, 'server_admin'));
		$this->cbReferee->setVisibility(AdminGroups::hasPermission($login, 'server_refmode'));
	}

    function destroy() {
        ActionHandler::getInstance()->deleteAction($this->actionOK);
        ActionHandler::getInstance()->deleteAction($this->actionCancel);
        parent::destroy();
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        //   $this->pager->setSize($this->sizeX - 4, $this->sizeY -12);
        $this->serverName->setSizeX($this->sizeX - 8);
        $this->serverComment->setSizeX($this->sizeX - 8);
        $this->serverPass->setSizeX(($this->sizeX - 8) / 2);
        $this->serverSpecPass->setSizeX(($this->sizeX - 8) / 2);
        $this->refereePass->setSizeX(($this->sizeX - 8) / 2);
        $this->frameInputbox->setPosition(12, -14);
        $this->frameCb->setPosition($this->sizeX / 2 + 20, -$this->sizeY / 2);
    }

    public function serverOptionsOk($login) {              
        $server = \ManiaLive\Data\Storage::getInstance()->server;

        $serverOptions = Array(
            "Name" => !AdminGroups::hasPermission($login, 'server_name')					
				? $server->name		: $this->serverName->getText(),
			
            "Comment" => !AdminGroups::hasPermission($login, 'server_comment')				
				? $server->comment	: $this->serverComment->getText(),
			
            "Password" => !AdminGroups::hasPermission($login, 'server_password')			
				? $server->password : $this->serverPass->getText(),
			
            "PasswordForSpectator" => !AdminGroups::hasPermission($login, 'server_specpwd')	
				? $server->passwordForSpectator : $this->serverSpecPass->getText(),
			
            "NextCallVoteTimeOut" => !AdminGroups::hasPermission($login, 'server_vote')		
				? $server->nextCallVoteTimeOut : $server->nextCallVoteTimeOut ,
			
            "CallVoteRatio" => !AdminGroups::hasPermission($login, 'server_vote')	
				? $server->callVoteRatio : $server->callVoteRatio,
			
            "RefereePassword" => !AdminGroups::hasPermission($login, 'server_refpwd') 
				? $server->refereePassword : $this->refereePass->getText(),
			
            "IsP2PUpload" => !AdminGroups::hasPermission($login, 'server_admin') 
				? $server->isP2PUpload : $this->cbAllowp2pUp->getStatus(),
			
            "IsP2PDownload" => !AdminGroups::hasPermission($login, 'server_admin') 
				? $server->isP2PDownload : $this->cbAllowp2pDown->getStatus(),
			
            "AllowMapDownload" => !AdminGroups::hasPermission($login, 'server_admin') 
				? $server->allowMapDownload : $this->cbAllowMapDl->getStatus(),
			
            "NextMaxPlayer" => !AdminGroups::hasPermission($login, 'server_maxplayer') 
				? $server->nextMaxPlayers : $this->maxPlayers->getText(),
			
            "NextMaxSpectator" => !AdminGroups::hasPermission($login, 'server_maxspec') 
				? $server->nextMaxSpectators : $this->maxSpec->getText(),
			
            "RefereeMode" => !AdminGroups::hasPermission($login, 'server_refmode') 
				? $server->refereeMode : $this->cbReferee->getStatus()
        );


        $this->connection->setServerOptions($serverOptions);
        $this->Erase($this->getRecipient());
        
    }

    public function serverOptionsCancel($login) {
        $this->Erase($this->getRecipient());
    }
}
