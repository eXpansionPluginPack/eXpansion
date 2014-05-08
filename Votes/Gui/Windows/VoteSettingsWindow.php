<?php

namespace ManiaLivePlugins\eXpansion\Votes\Gui\Windows;

class VoteSettingsWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $pager;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    private $connection;

    /** @var \ManiaLive\Data\Storage */
    private $storage;
    private $items = array();
    private $ok;
    private $cancel;
    private $actionOk;
    private $actionCancel;

    protected function onConstruct() {
	parent::onConstruct();
	$login = $this->getRecipient();

	$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
	$this->pager->setPosX(5);

	$this->addComponent($this->pager);
	$this->actionOk = $this->createAction(array($this, "Ok"));
	$this->actionCancel = $this->createAction(array($this, "Cancel"));

	$this->ok = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
	$this->ok->colorize("0d0");
	$this->ok->setText(__("Apply", $login));
	$this->ok->setAction($this->actionOk);
	$this->addComponent($this->ok);

	$this->cancel = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
	$this->cancel->setText(__("Cancel", $login));
	$this->cancel->setAction($this->actionCancel);
	$this->addComponent($this->cancel);
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
	$this->pager->setSize($this->sizeX - 5, $this->sizeY - 12);

	$this->ok->setPosition($this->sizeX - 38, -$this->sizeY + 3);
	$this->cancel->setPosition($this->sizeX - 20, -$this->sizeY + 3);
    }

    /**
     * 
     * @param \ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote $votes
     */
    function populateList($managedVotes) {
	$login = $this->getRecipient();

	foreach ($this->items as $item)
	    $item->erase();
	$this->pager->clearItems();
	$this->items = array();


	$x = 0;

	foreach ($managedVotes as $vote) {
	    $this->items[$x] = new \ManiaLivePlugins\eXpansion\Votes\Gui\Controls\ManagedVoteControl($x, $vote, $this->sizeX - 11);
	    $this->pager->addItem($this->items[$x]);
	    $x++;
	}
    }

    function Ok($login, $settings) {	
	/*
	  Array
	  (
	  [NextMap_timeout] => 30
	  [NextMap_ratio] => 0.5
	  [NextMap_voters] => 1
	  [RestartMap_timeout] => 30
	  [RestartMap_ratio] => 0.5
	  [RestartMap_voters] => 1
	  [Kick_timeout] => 30
	  [Kick_ratio] => 0.6
	  [Kick_voters] => 1
	  [Ban_timeout] => 30
	  [Ban_ratio] => -1
	  [Ban_voters] => 1
	  [SetModeScriptSettingsAndCommands_timeout] => 60
	  [SetModeScriptSettingsAndCommands_ratio] => -1
	  [SetModeScriptSettingsAndCommands_voters] => 1
	  [JumpToMapIdent_timeout] => 60
	  [JumpToMapIdent_ratio] => -1
	  [JumpToMapIdent_voters] => 1
	  [SetNextMapIdent_timeout] => 30
	  [SetNextMapIdent_ratio] => -1
	  [SetNextMapIdent_voters] => 1
	  [AutoTeamBalance_timeout] => 30
	  [AutoTeamBalance_ratio] => 0.5
	  [AutoTeamBalance_voters] => 1
	  )
	 */
	$array = array();	
	
	foreach ($settings as $key => $value) {
	    
	    
	}
	
	echo "THIS FEATURE NEEDS TO BE IMPLEMENTED.\n";
	
	$this->Erase($login);
    }

    function Cancel($login) {
	$this->Erase($login);
    }

    function destroy() {
	foreach ($this->items as $item)
	    $item->destroy();

	$this->items = array();
	$this->pager->destroy();
	$this->ok->destroy();
	$this->cancel->destroy();
	$this->connection = null;
	$this->storage = null;
	$this->clearComponents();
	parent::destroy();
    }

}

?>
