<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

class TimeChooser extends \ManiaLive\Gui\Window {

    public static $plugin = null;
    private $frame;
    private $btnBest, $btnPersonal, $btnNone;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 40));
        $this->addComponent($this->frame);

        $this->btnBest = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33,6);
        $this->btnBest->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_BestOfAll));
        $this->btnBest->setText(__("Top1", $login));
        $this->btnBest->setScale(0.4);
        $this->btnBest->colorize('aaaa');
        $this->frame->addComponent($this->btnBest);

        $this->btnPersonal = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33,6);
        $this->btnPersonal->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_PersonalBest));
        $this->btnPersonal->setText(__("Personal Best", $login));
        $this->btnPersonal->colorize('fff8');
        $this->btnPersonal->setScale(0.4);
        $this->frame->addComponent($this->btnPersonal);

        $this->btnNone = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33,6);
        $this->btnNone->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_None));
        $this->btnNone->setText(__("Off", $login));
        $this->btnNone->colorize('aaaa');
        $this->btnNone->setScale(0.4);
        $this->frame->addComponent($this->btnNone);

        $this->setAlign("left", "top");
    }

    function updatePanelMode($mode) {
        $this->btnBest->colorize('aaa8');
        $this->btnPersonal->colorize('aaa8');
        $this->btnNone->colorize('aaa8');

        if ($mode == TimePanel::Mode_BestOfAll)
            $this->btnBest->colorize('fffe');

        if ($mode == TimePanel::Mode_PersonalBest)
            $this->btnPersonal->colorize('fffe');

        if ($mode == TimePanel::Mode_None)
            $this->btnNone->colorize('fffe');

        $this->redraw($this->getRecipient());
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    function onShow() {
        
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
