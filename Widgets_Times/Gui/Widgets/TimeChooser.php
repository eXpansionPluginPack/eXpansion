<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Times\Gui\Widgets;

class TimeChooser extends \ManiaLive\Gui\Window {

    public static $plugin = null;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->btnBest = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnBest->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_BestOfAll));
        $this->btnBest->setText(__("Top1", $login));
        $this->btnBest->setPosX(0);
        $this->btnBest->setScale(0.7);
        $this->btnBest->colorize('aaaa');
        $this->addComponent($this->btnBest);

        $this->btnPersonal = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnPersonal->setAction($this->createAction(array(self::$plugin, "setMode"), TimePanel::Mode_PersonalBest));
        $this->btnPersonal->setText(__("Personal Best", $login));
        $this->btnPersonal->setPosX(20);
        $this->btnPersonal->colorize('0a0');
        $this->btnPersonal->setScale(0.7);
        $this->addComponent($this->btnPersonal);

        $this->setAlign("center", "top");
    }

    function updatePanelMode($mode) {
        $this->btnBest->colorize('aaaa');
        $this->btnPersonal->colorize('aaaa');

        if ($mode == TimePanel::Mode_BestOfAll)
            $this->btnBest->colorize('0a0');

        if ($mode == TimePanel::Mode_PersonalBest)
            $this->btnPersonal->colorize('0a0');

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
