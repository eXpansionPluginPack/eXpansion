<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted as Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class Configuration extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

	protected $pager;
	
	/** @var CheckboxScripted[] */
	protected $items = array();

	protected $ok;

	protected $cancel;

	private $actionOk;

	private $actionCancel;

	private $gameMode;

	protected function onConstruct()
	{
		parent::onConstruct();
		$login = $this->getRecipient();
		$this->setTitle(__("Configure HUD", $login));
		$this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
		$this->mainFrame->addComponent($this->pager);
		$this->actionOk = $this->createAction(array($this, "Ok"));
		$this->actionCancel = $this->createAction(array($this, "Cancel"));

		$this->ok = new OkButton();
		$this->ok->colorize("0d0");
		$this->ok->setText(__("Apply", $login));
		$this->ok->setAction($this->actionOk);
		$this->mainFrame->addComponent($this->ok);

		$this->cancel = new OkButton();
		$this->cancel->setText(__("Cancel", $login));
		$this->cancel->setAction($this->actionCancel);
		$this->mainFrame->addComponent($this->cancel);
	}

	function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$this->pager->setSize($this->sizeX, $this->sizeY - 8);
		$this->pager->setStretchContentX($this->sizeX);
		$this->ok->setPosition($this->sizeX - 44, -$this->sizeY + 6);
		$this->cancel->setPosition($this->sizeX - 26, -$this->sizeY + 6);
	}

	function setData($data)
	{
		$login = $this->getRecipient();

		foreach ($this->items as $item)
			$item->destroy();
		$this->pager->clearItems();
		$this->items = array();

		$statuses = $this->parseData($data);
		$x = 0;
		foreach ($statuses as $status) {
			$this->items[$x] = new \ManiaLivePlugins\eXpansion\Gui\Elements\CheckboxScripted(4, 4, 50);
			$this->items[$x]->setText($status->id);
			$this->items[$x]->setStatus($status->value);
			$this->pager->addItem($this->items[$x]);
			$x++;
		}
	}

	/**
	 * data is following format:
	 * 
	 * widgetname ^ ":" ^ gameMode ^ ":" ^ bool ^ "|";
	 * you can assume only one gamemode is sent at a time, so multiple gamemodes are not mixed
	 * 
	 * @param array $data
	 * @return \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem
	 */
	private function parseData($data)
	{
		if (!array_key_exists("widgetStatus", $data))
			return array();

		$entries = explode("|", $data["widgetStatus"]);
		$items = array();
		foreach ($entries as $entry) {
			if (empty($entry))
				continue;
			$val = explode(":", $entry, 3);
			$this->gameMode = $val[1];
			$items[] = new \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem($val[0], $val[1], $val[2]);
		}

		return $items;
	}

	function Ok($login, $options)
	{
		$outValues = array();

		foreach ($this->items as $component) {
			if ($component instanceof Checkbox) {
				$component->setArgs($options);
				$outValues[] = new \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem($component->getText(), $this->gameMode, $component->getStatus());
			}
		}

		$apply = HudSetVisibility::Create($login);
		$apply->setData($outValues);
		$apply->setTimeout(5);
		$apply->show();
		$this->Erase($login);
	}

	function Cancel($login)
	{
		$this->erase($login);
	}

	function destroy()
	{
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
