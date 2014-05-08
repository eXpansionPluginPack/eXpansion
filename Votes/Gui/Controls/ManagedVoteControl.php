<?php

namespace ManiaLivePlugins\eXpansion\Votes\Gui\Controls;

class ManagedVoteControl extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $inputbox;
    private $frame;
    private $ratio, $timeout, $voters;

    /**
     * 
     * @param type $indexNumber
     * @param \ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote $voteObject
     * @param type $sizeX
     */
    function __construct($indexNumber, \ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote $vote, $sizeX) {
	$sizeY = 10;
	$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
	$this->addComponent($this->bg);

	$this->frame = new \ManiaLive\Gui\Controls\Frame();
	$this->frame->setSize($sizeX, $sizeY);
	$this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

	$this->label = new \ManiaLib\Gui\Elements\Label(50, 4);
	$this->label->setAlign('left', 'center');
	$this->label->setText($vote->command);
	$this->frame->addComponent($this->label);

	$spacer = new \ManiaLib\Gui\Elements\Quad();
	$spacer->setSize(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

	$this->timeout = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox($vote->command . "_timeouts", 12);
	$this->timeout->setPosY(-1);
	$this->timeout->setLabel("Timeout");
	$this->timeout->setText($vote->timeout);
	$this->frame->addComponent($this->timeout);

	$this->frame->addComponent($spacer);

	$this->ratio = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox($vote->command . "_ratios", 12);
	$this->ratio->setLabel("Ratio");
	$this->ratio->setPosY(-1);
	$this->ratio->setText($vote->ratio);
	$this->frame->addComponent($this->ratio);

	$this->frame->addComponent(clone $spacer);
	
	$this->voters = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox($vote->command . "_voters", 12);
	$this->voters->setLabel("Voters");
	$this->voters->setPosY(-1);
	$this->voters->setText($vote->voters);
	$this->frame->addComponent($this->voters);
	
	$this->frame->addComponent(clone $spacer);

	$spacer = new \ManiaLib\Gui\Elements\Quad();
	$spacer->setSize(4, 4);
	$spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);


	$this->addComponent($this->frame);

	$this->sizeX = $sizeX;
	$this->sizeY = $sizeY;
	$this->setSize($sizeX, $sizeY);
    }

    function destroy() {
	$this->ratio->destroy();
	$this->timeout->destroy();
	$this->voters->destroy();

	$this->frame->clearComponents();
	$this->frame->destroy();
	$this->clearComponents();
	parent::destroy();
    }

}
?>


