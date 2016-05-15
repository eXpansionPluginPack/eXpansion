<?php

namespace ManiaLivePlugins\eXpansion\Votes\Gui\Windows;

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

        foreach ($this->items as $item)
            $item->destroy();
        $this->pager->clearItems();
        $this->items = array();


        $x = 0;

        foreach ($managedVotes as $vote) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Votes\Gui\Controls\ManagedVoteControl($x, $vote, $this->sizeX - 11);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function Ok($login, $settings)
    {
        $array = array();

        foreach ($settings as $key => $value) {

            $exploded = explode("_", $key);

            $varName = 'managedVote_' . array_pop($exploded);
            $voteName = implode('_', $exploded);

            $var = $this->metaData->getVariable($varName);
            if ($var instanceof \ManiaLivePlugins\eXpansion\Core\types\config\types\HashList) {
                $var->setValue($voteName, $value);
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
