<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class BannedPlayeritem extends \ManiaLive\Gui\Control
{

	private $bg;

	private $unbanButton;

	private $login;

	private $nickname;

	private $unbanAction;

	private $frame;

	function __construct($indexNumber, \Maniaplanet\DedicatedServer\Structures\PlayerBan $player, $controller, $login)
	{
		$sizeX = 120;
		$sizeY = 6;
		$this->player = $player;

		$this->unbanAction = $this->createAction(array($controller, 'unban'), array($player->login));

		$this->frame = new \ManiaLive\Gui\Controls\Frame();
		$this->frame->setSize($sizeX, $sizeY);
		$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

		$this->login = new \ManiaLib\Gui\Elements\Label(20, 4);
		$this->login->setAlign('left', 'center');
		$this->login->setText($player->login);
		$this->login->setScale(0.8);
		$this->frame->addComponent($this->login);


		$spacer = new \ManiaLib\Gui\Elements\Quad();
		$spacer->setSize(4, 4);
		$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

		$this->frame->addComponent($spacer);

		$this->unbanButton = new MyButton(16, 6);
		$this->unbanButton->setText(__("unBan"));
		$this->unbanButton->setAction($this->unbanAction);
		$this->unbanButton->setScale(0.6);
		$this->frame->addComponent($this->unbanButton);


		$this->addComponent($this->frame);

		$this->sizeX = $sizeX;
		$this->sizeY = $sizeY;
		$this->setSize($sizeX, $sizeY);
	}

	protected function onResize($oldX, $oldY)
	{
		
	}

	function __destruct()
	{
		
	}

}
?>

