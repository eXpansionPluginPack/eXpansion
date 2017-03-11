<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Layouts\Line;
use ManiaLive\Gui\Controls\Frame;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd;
use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;
use ManiaLivePlugins\eXpansion\AdminGroups\Gui\Controls\HelpItem;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
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

    /** @var HelpItem[]  */
    protected $items = array();

    /** @var Label */
    protected $labelCmd;

    /** @var Label */
    protected $labelDesc;

    /** @var Label */
    protected $labelShortCmd;

    /** @var Inputbox */
    protected $inputName;

    /** @var Button */
    protected $buttonSearch;

    protected function onConstruct()
    {
        parent::onConstruct();
        $login = $this->getRecipient();

        $this->adminGroups = AdminGroups::getInstance();
        $this->pager = new Pager();
        $this->mainFrame->addComponent($this->pager);

        $this->inputName = new Inputbox('search');
        $this->inputName->setSizeX(60);
        $this->inputName->setPositionY(0);
        $this->inputName->setLabel(__("Name/Description", $login));
        $this->inputName->setPositionX(0);
        $this->mainFrame->addComponent($this->inputName);

        $this->buttonSearch = new Button(20);
        $this->buttonSearch->setPositionX(62);
        $this->buttonSearch->setPositionY(0);
        $this->buttonSearch->setText(__("Search", $login));
        $this->buttonSearch->colorize('0a0');
        $this->buttonSearch->setAction($this->createAction(array($this, "doSearch")));
        $this->mainFrame->addComponent($this->buttonSearch);

        $frame = new Frame();
        $frame->setSize(80, 4);
        $frame->setPosY(-6);
        $frame->setLayout(new Line());
        $this->mainFrame->addComponent($frame);

        $this->labelCmd = new Label(40, 4);
        $this->labelCmd->setAlign('left', 'center');
        $frame->addComponent($this->labelCmd);

        $this->labelShortCmd = new Label(20, 4);
        $this->labelShortCmd->setAlign('left', 'center');
        $frame->addComponent($this->labelShortCmd);

        $this->labelDesc = new Label(80, 4);
        $this->labelDesc->setAlign('left', 'center');
        $frame->addComponent($this->labelDesc);
    }

    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);
        $this->pager->setSize($this->sizeX, $this->sizeY - 14);
        $this->pager->setStretchContentX($this->sizeX);
        $this->pager->setPosition(0, -7);
    }

    public function onShow()
    {
        $this->labelCmd->setText(__(AdminGroups::$txt_command, $this->getRecipient()));
        $this->labelShortCmd->setText(__('Alias', $this->getRecipient()));
        $this->labelDesc->setText(__(AdminGroups::$txt_description, $this->getRecipient()));

        $this->populateList();
    }

    public function populateList($searchCriteria = "")
    {
        foreach ($this->items as $item) {
            $item->erase();
        }
        $this->pager->clearItems();
        $this->items = array();

        $x = 0;
        $login = $this->getRecipient();
        foreach ($this->adminGroups->getAdminCommands() as $cmd) {
            if ($this->validateCmd($cmd, $searchCriteria)) {
                $this->items[$x] = new HelpItem($x, $cmd, $this, $login);
                $this->pager->addItem($this->items[$x]);
                $x++;
            }
        }
    }

    protected function validateCmd(AdminCmd $cmd, $searchCriteria)
    {
        if (empty($searchCriteria)) {
            return true;
        } else if (strpos($cmd->getCmd(), $searchCriteria) !== false) {
            return true;
        } else if(strpos($cmd->getHelp(), $searchCriteria)) {
            return true;
        } else if(strpos($cmd->getHelpMore(), $searchCriteria)) {
            return true;
        } else {
            foreach ($cmd->getAliases() as $alias) {
                if (strpos($alias, $searchCriteria)) {
                    return true;
                }
            }
        }

        return false;
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

    public function doSearch($login, $params)
    {
        $this->inputName->setText($params['search']);

        $this->populateList($params['search']);
        $this->redraw($login);
    }
}
