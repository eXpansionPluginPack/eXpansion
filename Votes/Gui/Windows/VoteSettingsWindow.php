<?php

namespace ManiaLivePlugins\eXpansion\Votes\Gui\Windows;

use ManiaLivePlugins\eXpansion\ManiaExchange\Config as MXconfig;
use ManiaLivePlugins\eXpansion\Votes\Config;
use ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote;

class VoteSettingsWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    /** @var \Maniaplanet\DedicatedServer\Connection */
    protected $connection;

    /** @var \ManiaLive\Data\Storage */
    protected $storage;

    protected $items = array();

    protected $ok;

    protected $cancel;

    protected $actionOk;

    protected $actionCancel;

    /**
     *
     * @var \ManiaLivePlugins\eXpansion\Votes\MetaData
     */
    protected $metaData;

    protected function onConstruct()
    {
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

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 5, $this->sizeY - 12);

        $this->ok->setPosition($this->sizeX - 50, -$this->sizeY + 3);
        $this->cancel->setPosition($this->sizeX - 24, -$this->sizeY + 3);
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote $votes
     */
    public function populateList($managedVotes, $metadata)
    {
        $login = $this->getRecipient();
        $this->metaData = $metadata;

        $x = count($this->items);

        foreach ($managedVotes as $vote) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Votes\Gui\Controls\ManagedVoteControl($x, $vote, $this->sizeX - 11);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function addMXvotes()
    {
        /** @var  MXconfig $config */
        $config = MXconfig::getInstance();

        $vote = new ManagedVote("mxVote", array());
        $vote->command = "mxVote";
        $vote->managed = $config->mxVote_enable;
        $vote->ratio = $config->mxVote_ratios;
        $vote->timeout = $config->mxVote_timeouts;
        $vote->voters = $config->mxVote_voters;

        $x = count($this->items);
        $this->items[$x] = new \ManiaLivePlugins\eXpansion\Votes\Gui\Controls\ManagedVoteControl($x, $vote, $this->sizeX - 11);
        $this->pager->addItem($this->items[$x]);

    }

    public function addLimits()
    {
        /** @var  Config $config */
        $config = Config::getInstance();

        $vote = new ManagedVote("mxVote", array());
        $vote->command = "mxVote";
        $vote->managed = $config->mxVote_enable;
        $vote->ratio = $config->mxVote_ratios;
        $vote->timeout = $config->mxVote_timeouts;
        $vote->voters = $config->mxVote_voters;

        $x = count($this->items);
        $this->items[$x] = new \ManiaLivePlugins\eXpansion\Votes\Gui\Controls\ManagedVoteLimit($x, "voteLimit", "Max votes per map (0 = disabled)", $config->limit_votes, $this->sizeX - 11);
        $this->items[$x + 1] = new \ManiaLivePlugins\eXpansion\Votes\Gui\Controls\ManagedVoteLimit($x, "restartLimit", "Max restarts of a map (0 = disabled)", $config->restartLimit, $this->sizeX - 11);

        $this->pager->addItem($this->items[$x]);
        $this->pager->addItem($this->items[$x + 1]);

    }


    public function Ok($login, $settings)
    {
        foreach ($settings as $key => $value) {

            $exploded = explode("_", $key);

            if ($exploded[0] == "!") {

                switch ($exploded[1]) {
                    case "voteLimit":
                        $var = $this->metaData->getVariable("limit_votes");
                        $var->setRawValue(intval($value));
                        break;
                    case "restartLimit":
                        $var = $this->metaData->getVariable("restartLimit");
                        $var->setRawValue(intval($value));
                        break;
                }

            } else {

                if ($exploded[1] == "voters") {
                    $value = intval($value) - 1;
                }

                if ($exploded[1] == "ratios") {
                    $value = floatval($value);
                } else {
                    $value = intval($value);
                }

                if ($exploded[0] == "mxVote") {
                    $meta = \ManiaLivePlugins\eXpansion\ManiaExchange\MetaData::getInstance();
                    $var = $meta->getVariable($key);
                    $var->setRawValue($value);
                }

                if ($key == "mxVote_ratios") {
                    $meta = \ManiaLivePlugins\eXpansion\ManiaExchange\MetaData::getInstance();
                    $var = $meta->getVariable('mxVote_enable');
                    if ($value == -1.) {
                        $var->setRawValue(false);
                    } else {
                        $var->setRawValue(true);
                    }
                }


                $varName = 'managedVote_' . array_pop($exploded);
                $voteName = implode('_', $exploded);

                $var = $this->metaData->getVariable($varName);
                if ($var instanceof \ManiaLivePlugins\eXpansion\Core\types\config\types\HashList) {
                    $var->setValue($voteName, $value);
                }
            }
        }

        //Save data
        \ManiaLivePlugins\eXpansion\Core\ConfigManager::getInstance()->check();

        $this->Erase($login);
    }

    public function Cancel($login)
    {
        $this->Erase($login);
    }

    public function destroy()
    {
        foreach ($this->items as $item)
            $item->destroy();

        $this->items = array();
        $this->pager->destroy();
        $this->ok->destroy();
        $this->cancel->destroy();
        $this->connection = null;
        $this->storage = null;
        $this->destroyComponents();
        parent::destroy();
    }

}
