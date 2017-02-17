<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\HelpItem;
use ManiaLivePlugins\eXpansion\Gui\Elements\Pager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of Help
 *
 * @author oliverde8
 */
class Help extends Window
{
    /**
     * @var AdminGroups
     */
    protected $adminGroups;
    /** @var  Pager */
    protected $pager;
    protected $items = array();

    /** @var  Label */
    protected $label_cmd;
    /** @var  Label */
    protected $label_desc;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->adminGroups = AdminGroups::getInstance();
        $this->pager = new Pager();
        $this->mainFrame->addComponent($this->pager);

        $frame = new Frame();
        $frame->setSize(120, 4);
        $frame->setPosY(0);
        $frame->setLayout(new Line());
        $this->mainFrame->addComponent($frame);

        $this->label_cmd = new Label(50, 4);
        $this->label_cmd->setAlign('left', 'center');
        $this->label_cmd->setScale(0.8);
        $frame->addComponent($this->label_cmd);

        $this->label_desc = new Label(20, 4);
        $this->label_desc->setAlign('left', 'center');
        $this->label_desc->setScale(0.8);
        $frame->addComponent($this->label_desc);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX - 2, $this->sizeY - 13);
        $this->pager->setStretchContentX($this->sizeX - 4);
        $this->pager->setPosition(0, -4);
    }

    public function onShow()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->pager->clearItems();
        $this->items = array();

        $this->label_cmd->setText(__(AdminGroups::$txt_command, $this->getRecipient()));
        $this->label_desc->setText(__(AdminGroups::$txt_description, $this->getRecipient()));

        $this->populateList();
    }

    public function populateList()
    {
        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->adminGroups->getAdminCommands() as $cmd) {
            $this->items[$x] = new HelpItem($x, $cmd, $this, $login);
            $this->pager->addItem($this->items[$x]);
            $x++;
        }
    }

    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->items = null;
        $this->pager->destroy();
        $this->destroyComponents();
        parent::destroy();
    }
}
