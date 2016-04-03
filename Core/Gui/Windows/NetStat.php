<?php

namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Column;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Data\Storage;
use ManiaLive\Event\Dispatcher;
use ManiaLive\Features\Tick\Event as TickEvent;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\Core\Core;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class Netstat extends Window
{

    protected $frame;

    /** @var Storage */
    private $storage;
    private $lastUpdate = 0;

    protected function onConstruct()
    {
        parent::onConstruct();
        Dispatcher::register(TickEvent::getClass(), $this);

        $this->setTitle("Network Statistics");

        $this->storage = Storage::getInstance();

        $this->frame = new Frame(5, -2);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\VerticalFlow(50, 100));
        $this->addComponent($this->frame);
        $this->lastUpdate = time();
    }

    public function onTick()
    {
        if ($this->lastUpdate + 5 < time()) {
            $this->redraw($this->getRecipient());
            $this->lastUpdate = time();
        }
    }

    protected function onDraw()
    {
        parent::onDraw();
        $this->frame->clearComponents();

        $netstat = Core::$netStat;
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortAsc($netstat, "login");
        $index = 0;

        foreach ($netstat as $login => $stat) {
            if ($index > 50) {
                continue;
            }
            $line = new Frame();
            $line->setSize(70, 4);
            $line->setLayout(new Line());

            $player = new Label(35, 6);
            if (isset($this->storage->players[$login])) {
                $player->setText(($index + 1) . ". " . $this->storage->players[$login]->nickName);
            } else {
                $player->setText(($index + 1) . ". " . $login);
            }
            $line->addComponent($player);

            $status = new Label(16, 6);
            $color = '$f00';
            if ($stat->updateLatency < 300) {
                $color = '$0f0';
            }

            $status->setText($color . $stat->updateLatency . "ms");
            $line->addComponent($status);

            $status = new Label(20, 6);
            $status->setText('$fff' . $stat->latestNetworkActivity);
            $line->addComponent($status);

            $this->frame->addComponent($line);
            $index++;
        }
    }

    public function destroy()
    {
        parent::destroy();
        Dispatcher::unregister(TickEvent::getClass(), $this);
    }

}

?>
