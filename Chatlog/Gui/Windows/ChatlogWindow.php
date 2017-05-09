<?php
namespace ManiaLivePlugins\eXpansion\Chatlog\Gui\Windows;

use ManiaLivePlugins\eXpansion\Chatlog\Gui\Controls\Message;
use ManiaLivePlugins\eXpansion\Chatlog\Structures\ChatMessage;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

class ChatlogWindow extends Window
{

    /** @var  Pager */
    protected $pager;

    private $items = array();
    /** @var  Button */
    protected $btn_close;

    protected $actionClose;
    /** @var  Button */
    protected $ok;

    private $widths = array(2, 5, 25);

    public function onConstruct()
    {
        parent::onConstruct();
        $this->pager = new Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionClose = $this->createAction(array($this, "Close"));

    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setPositionY(6);
        $this->pager->setSize($this->sizeX, $this->sizeY - 2);
        $this->pager->setStretchContentX($this->sizeX);
    }

    /**
     *
     * @param ChatMessage[] $messages
     */
    public function populateList($messages)
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;

        foreach ($messages as $message) {
            $this->items[$x] = new Message(
                $x,
                $message,
                $this->widths,
                $this->sizeX
            );
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function Close($login)
    {
        $this->erase($login);
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }

        $this->items = array();
        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
