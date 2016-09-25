<?php

namespace ManiaLivePlugins\eXpansion\Votes\Gui\Controls;

class ManagedVoteLimit extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $bg;
    private $label;
    private $frame;
    private $ratio, $limit, $voters;

    /**
     *
     * @param type $indexNumber
     * @param \ManiaLivePlugins\eXpansion\Votes\Structures\ManagedVote $voteObject
     * @param type $sizeX
     */
    public function __construct($indexNumber, $name, $desc, $value, $sizeX)
    {
        $sizeY = 10;
        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->label = new \ManiaLib\Gui\Elements\Label(50, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($desc);
        $this->frame->addComponent($this->label);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->limit = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("!_" . $name, 14);
        $this->limit->setPosY(-1);
        $this->limit->setLabel("Limit");
        $this->limit->setText($value);
        $this->frame->addComponent($this->limit);

        $this->frame->addComponent($spacer);

        //     $this->frame->addComponent(clone $spacer);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    public function destroy()
    {
        $this->limit->destroy();

        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}

