<?php

namespace ManiaLivePlugins\eXpansion\Faq\Gui\Controls;

/**
 * @abstract
 */
abstract class FaqControl extends \ManiaLive\Gui\Control {

    /** @var ManiaLib\Gui\Elements\Label */
    protected $label;
    protected $action = null;

    public function __construct($text) {
        $this->setSize(240, 4);
        $this->setAlign("left");
        $this->label = new \ManiaLib\Gui\Elements\Label(240, 5);
        $this->label->setAlign("left", "center");
        $this->label->setStyle("TextCardMedium");
        $this->label->setText($text);
        $this->label->setTextSize(1);
        $this->addComponent($this->label);
    }

    public function setBlock($index = 0) {
        $this->setPosX($index * 6);
        $this->setSize(240 - ($index * 6), 4);
    }

    public function setTopicLink($file) {

        $this->label->setTextColor("00e");
        $this->action = $this->createAction(array(\ManiaLivePlugins\eXpansion\Faq\Gui\Windows\FaqWindow::$mainPlugin, "showFaq"), $file, null);
        $this->label->setAction($this->action);
    }

    public function setText($text) {
        $this->label->setText($text);
    }

    function onIsRemoved(\ManiaLive\Gui\Container $target) {
        parent::onIsRemoved($target);
        $this->destroy();
    }

    public function destroy() {
        parent::destroy();
    }

    protected function onDraw() {
        parent::onDraw();
        $this->clearComponents();
        $this->addComponent($this->label);
    }

}

?>
