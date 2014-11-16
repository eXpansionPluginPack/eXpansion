<?php

namespace ManiaLivePlugins\eXpansion\PersonalMessages\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;

class MessagesPanel extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

	/** @var \Maniaplanet\DedicatedServer\Connection */
	private $connection;

	/** @var \ManiaLive\Data\Storage */
	private $storage;
	private $actionPlayers;
	private $actionSend;
	private $_windowFrame;
	private $_mainWindow;
	private $_minButton;
	private $frame;
	private $labelPlayer;
	private $inputboxMessage;
	private $buttonSend;
	private $status = "True";
	private $minMaxAction;
	private $labelReciever;
	private $widgetScript;
	private $sendscript;

	/** @var \Maniaplanet\DedicatedServer\Structures\Player */
	private $targetPlayer = false;

	protected function exp_onBeginConstruct()
	{
		parent::exp_onBeginConstruct();
		$this->setName("Personal Chat Widget");
		$config = Config::getInstance();

		$this->setScriptEvents(true);
		$this->setAlign("left", "top");

		$dedicatedConfig = \ManiaLive\DedicatedApi\Config::getInstance();
		$this->connection = \Maniaplanet\DedicatedServer\Connection::factory($dedicatedConfig->host, $dedicatedConfig->port);
		$this->storage = \ManiaLive\Data\Storage::getInstance();

		$this->actionPlayers = $this->createAction(array($this, 'players'));
		$this->actionSend = $this->createAction(array($this, 'send'));

		$this->_windowFrame = new \ManiaLive\Gui\Controls\Frame();
		$this->_windowFrame->setAlign("left", "top");
		$this->_windowFrame->setId("Frame");
		$this->_windowFrame->setScriptEvents(true);

		$this->_mainWindow = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(100, 10);
		$this->_mainWindow->setId("MainWindow");
		$this->_mainWindow->setScriptEvents();
		$this->_windowFrame->addComponent($this->_mainWindow);

		$frame = new \ManiaLive\Gui\Controls\Frame(6, 0);
		$frame->setAlign("left", "top");
		$line = new \ManiaLib\Gui\Layouts\Line();
		$line->setMargin(2, 0);

		$frame->setLayout($line);

		$this->labelReciever = new \ManiaLib\Gui\Elements\Label(40);
		$this->labelReciever->setAlign("left", "top");
		$this->labelReciever->setText("");
		$this->labelReciever->setStyle("TextCardSmallScores2");
		$this->_windowFrame->addComponent($this->labelReciever);


		$this->labelPlayer = new myButton();
		$this->labelPlayer->setAlign("left", "top");
		$this->labelPlayer->setTextColor('fff');
		$this->labelPlayer->setText("Select...");
		$this->labelPlayer->setPosY(-3);
		$this->labelPlayer->setDescription("Select player whom to send the message", 35);
		$this->labelPlayer->setAction($this->actionPlayers);
		$frame->addComponent($this->labelPlayer);

		$this->inputboxMessage = new \ManiaLib\Gui\Elements\Entry(85, 6);
		$this->inputboxMessage->setAlign("left", "top");
		$this->inputboxMessage->setId("messagebox");
		$this->inputboxMessage->setName("message");
		$this->inputboxMessage->setScale(0.8);
		$this->inputboxMessage->setPosY(-0.5);
		$this->inputboxMessage->setTextColor('fff');
		$this->inputboxMessage->setScriptEvents(true);
		$frame->addComponent($this->inputboxMessage);

		$this->_windowFrame->addComponent($frame);

		$this->_minButton = new \ManiaLib\Gui\Elements\Quad(5, 5);
		$this->_minButton->setId("minimizeButton");
		$this->_minButton->setStyle("Icons64x64_1");
		$this->_minButton->setSubStyle("Outbox");
		$this->_minButton->setScriptEvents(true);
		$this->_minButton->setAlign("left", "center");

		$this->_windowFrame->addComponent($this->_minButton);

		$this->addComponent($this->_windowFrame);
	}

	protected function exp_onSettingsLoaded()
	{
		$this->widgetScript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\TrayWidget");
		$this->widgetScript->setParam('isMinimized', $this->status);
		$this->widgetScript->setParam('autoCloseTimeout', $this->getParameter('autoCloseTimeout'));
		$this->widgetScript->setParam('posXMin', -92);
		$this->widgetScript->setParam('posX', -92);
		$this->widgetScript->setParam('posXMax', -4);
		$this->registerScript($this->widgetScript);

		$this->sendscript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("PersonalMessages\Gui\Script");
		$this->sendscript->setParam("sendAction", $this->actionSend);
		$this->registerScript($this->sendscript);
		parent::exp_onSettingsLoaded();
	}

	function onResize($oldX, $oldY)
	{
		parent::onResize($oldX, $oldY);
		$this->_mainWindow->setSize(102, 6);
		$this->_minButton->setPosition(100 - 4, -2.5);
	}

	function sendPm($login, $target)
	{
		$this->targetPlayer = $target;
		$targetPlayer = $this->storage->getPlayerObject($target);
		$this->labelPlayer->setText($targetPlayer->nickName);
		\ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection::Erase($login);
		$this->onResize($this->sizeX, $this->sizeY);
		$this->redraw($this->getRecipient());
	}

	function players($login, $args = array())
	{
		// $this->status = "False";
		$window = \ManiaLivePlugins\eXpansion\Gui\Windows\PlayerSelection::Create($login);
		$window->setController($this);
		$window->setTitle('Select Player to send message');
		$window->setSize(85, 100);
		$window->populateList(array($this, 'sendPm'), 'send');
		$window->centerOnScreen();
		$window->show();
	}

	function send($login, $args)
	{
		try {
			// $this->status = "False";
			$target = $this->targetPlayer;
			if ($target == false) {
				$this->connection->chatSendServerMessage('Select a player to send pm first by clicking!', $login);
				return;
			}
			if (empty($args['message'])) {
				$this->connection->chatSendServerMessage('Empty message!', $login);
				return;
			}

			$message = $args['message'];
			$targetPlayer = $this->storage->getPlayerObject($target);
			$sourcePlayer = $this->storage->getPlayerObject($login);
			\ManiaLivePlugins\eXpansion\PersonalMessages\PersonalMessages::$reply[$login] = $target;
			$color = '$z$s' . \ManiaLivePlugins\eXpansion\Core\Config::getInstance()->Colors_personalmessage;
			$this->connection->chatSendServerMessage('$fff' . $sourcePlayer->nickName . $color . ' »» $fff' . $targetPlayer->nickName . $color . " " . $message, $login);
			$this->connection->chatSendServerMessage('$fff' . $sourcePlayer->nickName . $color . ' »» $fff' . $targetPlayer->nickName . $color . " " . $message, $target);
		} catch (\Exception $e) {
			$this->connection->chatSendServerMessage('$f00$oError $z$s$fff' . $e->getMessage(), $login);
		}
	}

	function setTargetPlayer($login)
	{
		$this->targetPlayer = $login;
		$this->labelReciever->setText($targetPlayer->nickName);
		$this->onResize($this->sizeX, $this->sizeY);
		// $this->status = "False";
		$this->redraw($this->getRecipient());
	}

	function destroy()
	{
		parent::destroy();
	}

}

?>
