<?php

namespace ManiaLivePlugins\eXpansion\Chat_Admin\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button as myButton;
use \ManiaLib\Utils\Formatting;

class BlacklistPlayeritem extends \ManiaLive\Gui\Control
{

    private $unblackButton;
    private $login;
    private $unbanAction;
    private $frame;

    function __construct($indexNumber, \Maniaplanet\DedicatedServer\Structures\Player $player, $controller, $login)
    {
	$sizeX = 120;
	$sizeY = 6;
	$this->player = $player;

	$this->unblackAction = $this->createAction(array($controller, 'unblack'), array($player->login));

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

	$this->unblackButton = new MyButton(16, 6);
	$this->unblackButton->setText(__("unBan"));
	$this->unblackButton->setAction($this->unblackAction);
	$this->unblackButton->setScale(0.6);
	$this->frame->addComponent($this->unblackButton);


	$this->addComponent($this->frame);

	$this->sizeX = $sizeX;
	$this->sizeY = $sizeY;
	$this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
	
    }

    function onDraw()
    {
	
    }

    function __destruct()
    {
		
    }

}
?>

