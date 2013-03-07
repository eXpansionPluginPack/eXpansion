<?php

namespace ManiaLivePlugins\eXpansion\LocalRecords\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Config;
use ManiaLivePlugins\eXpansion\LocalRecords\Gui\Controls\Recorditem;

class LRPanel extends \ManiaLive\Gui\Window {

    /** @var \ManiaLive\Gui\Controls\Frame */
    private static $frame;
    public static $records;

    protected function onConstruct() {
        parent::onConstruct();

        $this->setAlign("left", "top");

        self::$frame = new \ManiaLive\Gui\Controls\Frame();
        self::$frame->setAlign("left", "top");
        self::$frame->setPosition(3, -4);
        self::$frame->setLayout(new \ManiaLib\Gui\Layouts\Column(-1));
        $this->addComponent(self::$frame);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
    }

    public static function RedrawAll() {
        if (!isset(self::$frame))
            return;
        $index = 1;
        self::$frame->clearComponents();
        foreach (self::$records as $record)
            self::$frame->addComponent(new recordItem($index++, $record));
        parent::RedrawAll();
    }

    function onShow() {
        $index = 1;
        self::$frame->clearComponents();
        foreach (self::$records as $record)
            self::$frame->addComponent(new recordItem($index++, $record));
    }

    function destroy() {
        $this->clearComponents();
        parent::destroy();
    }

}

?>
