<?php
namespace ManiaLivePlugins\eXpansion\Core\Gui\Windows;


use ManiaLib\Gui\Elements\Label;
use ManiaLive\Gui\ActionHandler;
use ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSettingListElement;
use ManiaLivePlugins\eXpansion\Core\types\config\types\HashList;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\OptimizedPager;
use ManiaLivePlugins\eXpansion\Gui\Windows\Window;

/**
 * Description of ExpSettings
 *
 * @author De Cramer Oliver
 */
class ExpListSetting extends Window
{

    /** @var OptimizedPager */
    public $pagerFrame;

    /** @var  Inputbox|null */
    public $input_key;

    /** @var  Inputbox */
    public $input_value;

    /** @var Button */
    public $buttonAdd;

    public $actions = array();

    public $items = array();
    /** @var Variable */
    public $var;

    /** @var  Label */
    public $label_help;

    /**
     *
     */
    protected function onConstruct()
    {
        parent::onConstruct();

        $this->pagerFrame = new OptimizedPager();
        $this->pagerFrame->setPosY(-14);
        $this->mainFrame->addComponent($this->pagerFrame);

        $this->label_help = new Label(120, 8);
        $this->label_help->setPosY(5);
        $this->mainFrame->addComponent($this->label_help);

        $this->input_value = new Inputbox("value");
        $this->input_value->setLabel('Value');
        $this->input_value->setPosY(-5);
        $this->mainFrame->addComponent($this->input_value);

        $this->buttonAdd = new Button();
        $this->buttonAdd->setText("Add");
        $this->buttonAdd->setPosY(-5);
        $this->buttonAdd->setAction($this->createAction(array($this, "addValue")));
        $this->mainFrame->addComponent($this->buttonAdd);
    }

    /**
     * @param $oldX
     * @param $oldY
     */
    public function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

        $this->pagerFrame->setPosX(0);
        $this->pagerFrame->setSize($this->getSizeX() - 3, $this->getSizeY() - 16);

        $this->buttonAdd->setPosX($this->getSizeX() - 23);
        if ($this->input_key == null) {
            $this->input_value->setSizeX($this->getSizeX() - 25);
        } else {
            $this->input_value->setSizeX(($this->getSizeX() - 25) / 2);
            $this->input_value->setPosX(($this->getSizeX() - 25) / 2 + 1);
            $this->input_value->setSizeX(($this->getSizeX() - 25) / 2 - 1);
        }
    }

    /**
     * @param Variable $var
     */
    public function populate(Variable $var)
    {
        $this->var = $var;

        $this->label_help->setText($var->getDescription());

        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;

        $this->pagerFrame->clearItems();
        $this->items = array();

        if ($var instanceof HashList) {
            if ($this->input_key == null) {
                $this->input_key = new Inputbox("key", 50);
                $this->input_key->setPosY(-5);
                $this->input_key->setLabel('Key');
                $this->mainFrame->addComponent($this->input_key);
            }

            $this->input_value->setPosX(($this->getSizeX() - 25) / 2 + 1);
            $this->input_value->setSizeX(($this->getSizeX() - 25) / 2 - 1);

            ExpSettingListElement::$large = true;
        } else {
            if ($this->input_key != null) {
                $this->removeComponent($this->input_key);
                $this->input_key->destroy();
                $this->input_key = null;
            }

            $this->input_value->setSizeX($this->getSizeX() - 25);
            ExpSettingListElement::$large = false;
        }

        $values = $var->getRawValue();

        if (!empty($this->actions)) {
            $actionHandler = ActionHandler::getInstance();
            foreach ($this->actions as $action) {
                $actionHandler->deleteAction($action);
            }
        }

        if (!empty($values)) {
            foreach ($values as $key => $value) {
                $action = $this->createAction(array($this, 'removeValue'), $key);
                $this->actions[] = $action;
                $this->pagerFrame->addSimpleItems(array($key => -1,
                    $value . " " => -1,
                    'deleteAction' => $action));
            }
        }

        $this->pagerFrame->setContentLayout('\ManiaLivePlugins\eXpansion\Core\Gui\Controls\ExpSettingListElement');
        $this->pagerFrame->update($this->getRecipient());
    }

    /**
     * @param $login
     * @param $groupName
     */
    public function switchGroup($login, $groupName)
    {
        $this->populate($this->configManager, $groupName);
        $this->redraw();
    }

    /**
     * @param $login
     * @param $entries
     */
    public function addValue($login, $entries)
    {
        if ($this->var instanceof HashList) {
            $key = $entries['key'];
            if ($key != "") {
                $this->var->setValue($key, $entries['value']);
            }
        } else {
            /** @todo oliver can you check this, it points no where ?! */
            $this->var->addValue($entries['value']);
        }
        $this->populate($this->var);
        $this->redraw();
    }

    /**
     * @param $login
     * @param $key
     */
    public function removeValue($login, $key)
    {
        $this->var->removeValue($key);
        $this->populate($this->var);
        $this->redraw();
    }

    /**
     *
     */
    public function destroy()
    {
        foreach ($this->items as $item) {
            $item->destroy();
        }
        $this->items = null;

        if (!empty($this->actions)) {
            $actionHandler = ActionHandler::getInstance();
            foreach ($this->actions as $action) {
                $actionHandler->deleteAction($action);
            }
        }
        parent::destroy();
    }
}
