<?php

namespace ManiaLivePlugins\eXpansion\Debugtool\Gui;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class testWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    private $frame;

    /** @var \ManiaLive\Data\Storage */
    private $storage;

    protected function onConstruct()
    {
        parent::onConstruct();

        // $this->setName("Network Status Widget");


        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, -3);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(60, 4));
        $this->addComponent($this->frame);

        $label = new \ManiaLib\Gui\Elements\Label(60);
        $label->setAlign("left", "top");
        $label->setPosX(0);
        $label->setText('$fffPlayer login and nick');
        $this->addComponent($label);

        $label = new \ManiaLib\Gui\Elements\Label(60);
        $label->setAlign("left", "top");
        $label->setPosX(42);
        $label->setText('$fffPlayersSlot Status');
        $this->addComponent($label);
    }

    /**
     *
     * @param \ManiaLive\Data\Player[] $players
     */
    public function setData($players)
    {
        foreach ($players as $login => $player) {
            //if ($stat->updateLatency >= 160 || $stat->updatePeriod >= 400) {
            $frame = new \ManiaLive\Gui\Controls\Frame();
            $frame->setSize(120, 5);
            $frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

            $label = new \ManiaLib\Gui\Elements\Label(16, 6);
            $label->setText($player->login);
            $frame->addComponent($label);

            $label = new \ManiaLib\Gui\Elements\Label(35, 6);
            $label->setText($player->nickName);
            $frame->addComponent($label);

            $label = new \ManiaLib\Gui\Elements\Label(16, 6);
            $status = '$d00false';
            if ($player->hasPlayerSlot)
                $status = '$0d0true';

            $label->setText($status);
            $frame->addComponent($label);


            $this->frame->addComponent($frame);
        }
    }
}
