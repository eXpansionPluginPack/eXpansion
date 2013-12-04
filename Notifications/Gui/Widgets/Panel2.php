<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Notifications\Gui\Controls\Item;

class Panel2 extends \ManiaLive\Gui\Window {

    private $frame;

    /**
     * @var \ManiaLive\Data\Storage  
     */
    private $storage;

    protected function onConstruct() {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "bottom");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(1));
        $this->addComponent($this->frame);
        $this->storage = \ManiaLive\Data\Storage::getInstance();
    }

    function setMessages(array $messages) {
        $this->frame->clearComponents();
        $login = $this->getRecipient();
        $index = 0;
        $buffer = array();
        foreach ($messages as $message) {

            if ($message->login == null || $message->login == $login) {
                $text = $message->message;
                if ($message->message instanceof \ManiaLivePlugins\eXpansion\Core\i18n\Message) {
                    $lang = $this->storage->getPlayerObject($login)->language;

                    $text = $text->getParsedMessage($lang);
                }


                $buffer[] = $text;
                $index ++;
            }

            if ($index > 6) {

                break;
            }
        }
        
        $buffer = array_reverse($buffer, true);
        $buffer = array_slice($buffer, 0, 6, true);
        $buffer = array_reverse($buffer, true);

        foreach ($buffer as $text) {
            $item = new Item($text);
            $this->frame->addComponent($item);
        }
        //$posY = abs(count($menuItems) * 6);
        //$this->frame->setPosition(6, $posY);        
    }

    function destroy() {
        parent::destroy();
    }

}

?>
