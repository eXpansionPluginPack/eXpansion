<?php

namespace ManiaLivePlugins\eXpansion\Notifications\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Notifications\Gui\Controls\Item;

class Panel2 extends \ManiaLive\Gui\Window
{

    protected $frame;

    /**
     * @var \ManiaLive\Data\Storage
     */
    protected $storage;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->frame = new \ManiaLive\Gui\Controls\Frame();
        $this->frame->setAlign("left", "bottom");
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Column(1));
        $this->addComponent($this->frame);
    }

    public function setMessages(array $messages)
    {
        $this->storage = \ManiaLive\Data\Storage::getInstance();
        $this->frame->clearComponents();
        $login = $this->getRecipient();
        $index = 0;
        $buffer = array();
        foreach ($messages as $message) {
            if ($message->login == null || $message->login == $login) {
                $text = $message->message;
                if ($message->message instanceof \ManiaLivePlugins\eXpansion\Core\I18n\Message) {
                    $lang = $this->storage->getPlayerObject($login)->language;

                    $text = $text->getParsedMessage($lang);
                }
                $buffer[] = $text;
                $index++;
            }

            if ($index > 6) {
                break;
            }
        }

        foreach ($buffer as $text) {
            $this->frame->addComponent(new Item($text));
        }
    }
}
