<?php

namespace ManiaLivePlugins\eXpansion\Chatlog\Gui\Windows;

class ChatlogWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    /** @var  \ManiaLivePlugins\eXpansion\Gui\Elements\Pager */
    protected $pager;
    private $items = array();
    protected $btn_close;
    protected $actionClose;
    protected $ok;
    private $widths = array(2, 5, 25);

    function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->mainFrame->addComponent($this->pager);
        $this->actionClose = $this->createAction(array($this, "Close"));

        /*$this->ok = new OkButton();
        //  $this->ok->colorize("0d0");
        $this->ok->setText(__("Close", $login));
        $this->ok->setAction($this->actionClose);
        $this->mainFrame->addComponent($this->ok);*/
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setPositionY(6);
        $this->pager->setSize($this->sizeX, $this->sizeY - 2);
        $this->pager->setStretchContentX($this->sizeX);
        //$this->ok->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    /**
     *
     * @param \ManiaLivePlugins\eXpansion\Chatlog\Structures\ChatMessage[] $messages
     */
    function populateList($messages)
    {
        foreach ($this->items as $item)
            $item->erase();
        $this->pager->clearItems();
        $this->items = array();

        $login = $this->getRecipient();
        $x = 0;

        foreach ($messages as $message) {
            $this->items[$x] = new \ManiaLivePlugins\eXpansion\Chatlog\Gui\Controls\Message($x, $message, $this->widths, $this->sizeX);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    function Close($login)
    {
        $this->erase($login);
    }

    function destroy()
    {
        foreach ($this->items as $item)
            $item->erase();

        $this->items = array();
        $this->pager->destroy();
        //$this->ok->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}

?>
