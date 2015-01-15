<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel;

class Notice extends Window
{

	protected $ok;

	protected $cancel;

	protected $actionOk;

	private $label;

	protected $title;

	protected function onConstruct()
	{
		parent::onConstruct();
		$login = $this->getRecipient();
		$this->actionOk = $this->createAction(array($this, "Ok"));
		$this->setSize(57, 28);

		$this->ok = new OkButton();
		$this->ok->colorize("0d0");
		$this->ok->setPosition(28, -20);
		$this->ok->setText(__("Close", $login));
		$this->ok->setAction($this->actionOk);
		$this->mainFrame->addComponent($this->ok);

		$this->label = new DicoLabel(50, 20);
		$this->mainFrame->addComponent($this->label);


		$this->setTitle(__("Notice", $login));
	}

	public function setMessage(Message $message, $args)
	{
		$this->label->setText($message, $args);
	}

	public function Ok($login)
	{
		$this->Erase($login);
	}

	function destroy()
	{
		$this->ok->destroy();
		$this->destroyComponents();
		parent::destroy();
	}

}

?>
