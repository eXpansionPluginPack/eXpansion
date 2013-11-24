<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

class TimeChooser extends \ManiaLive\Gui\Window {

    public static $plugin = null;
    protected $frame;
    protected $btnBest, $btnPersonal, $btnNone, $btnAudio;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(20, 40));
        $this->addComponent($this->frame);

        $this->btnBest = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33, 6);
        $this->btnBest->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_BestOfAll));
        $this->btnBest->setText(__("Top1", $login));
        $this->btnBest->setScale(0.4);
        $this->btnBest->colorize('aaaa');
        $this->frame->addComponent($this->btnBest);

        $this->btnPersonal = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33, 6);
        $this->btnPersonal->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_PersonalBest));
        $this->btnPersonal->setText(__("Personal Best", $login));
        $this->btnPersonal->colorize('fff8');
        $this->btnPersonal->setScale(0.4);
        $this->frame->addComponent($this->btnPersonal);

        $this->btnNone = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33, 6);
        $this->btnNone->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_None));
        $this->btnNone->setText(__("Off", $login));
        $this->btnNone->colorize('aaaa');
        $this->btnNone->setScale(0.4);
        $this->frame->addComponent($this->btnNone);

        $this->btnAudio = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button(33, 6);
        $this->btnAudio->setText(__("Audio: off", $login));
        $this->btnAudio->colorize('a00a');
        $this->btnAudio->setScale(0.4);
        $this->frame->addComponent($this->btnAudio);

        $this->setAlign("left", "top");
    }

    function updatePanelMode($mode, $audiomode) {
        $login = $this->getRecipient();
        $this->btnBest->colorize('aaa8');
        $this->btnPersonal->colorize('aaa8');
        $this->btnNone->colorize('aaa8');

        if ($mode == TimePanel::Mode_BestOfAll)
            $this->btnBest->colorize('fffe');

        if ($mode == TimePanel::Mode_PersonalBest)
            $this->btnPersonal->colorize('fffe');

        if ($mode == TimePanel::Mode_None)
            $this->btnNone->colorize('fffe');

        if ($audiomode) {
            $this->btnAudio->setAction($this->createAction(array(self::$plugin, "setAudioMode"), false));
            $this->btnAudio->setText(__("Audio: on", $login));
            $this->btnAudio->colorize('0a0a');
        } else {
            $this->btnAudio->setAction($this->createAction(array(self::$plugin, "setAudioMode"), true));
            $this->btnAudio->setText(__("Audio: off", $login));
            $this->btnAudio->colorize('a00a');
        }
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
