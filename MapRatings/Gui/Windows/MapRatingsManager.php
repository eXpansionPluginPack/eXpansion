<?php

namespace ManiaLivePlugins\eXpansion\MapRatings\Gui\Windows;

/**
 * Description of MapManager
 *
 * @author Reaby
 */
class MapRatingsManager extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    protected $pager;

    public static $removeId;

    protected $btn_remove;

    protected $btn_close;

    public function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();
        $this->pager = new \ManiaLivePlugins\eXpansion\Gui\Elements\Pager();
        $this->addComponent($this->pager);

        $this->btn_remove = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_remove->setText(__("Remove", $login));
        $this->btn_remove->colorize("d00");
        $this->btn_remove->setAction(self::$removeId);
        $this->addComponent($this->btn_remove);

        $this->btn_close = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btn_close->setText(__("Close", $login));
        $this->btn_close->setAction($this->createAction(array($this, "close")));
        $this->addComponent($this->btn_close);
    }

    /**
     * set ratings for window
     *
     * @param \ManiaLivePlugins\eXpansion\MapRatings\Structures\MapRating[] $ratings ;
     */
    public function setRatings($ratings)
    {
        $this->pager->clearItems();
        $index = 0;
        foreach ($ratings as $rating) {
            $this->pager->addItem(new \ManiaLivePlugins\eXpansion\MapRatings\Gui\Controls\RatingsItem($index, $rating));
            $index++;
        }
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 22);
        $this->btn_remove->setPosition($this->sizeX - 40, -$this->sizeY + 6);
        $this->btn_close->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    public function close($login)
    {
        $this->Erase($login);
    }

    public function destroy()
    {
        $this->pager->destroy();
        $this->btn_remove->destroy();
        $this->btn_close->destroy();

        parent::destroy();
    }
}
