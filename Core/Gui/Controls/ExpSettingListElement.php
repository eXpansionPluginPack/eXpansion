<?php
namespace ManiaLivePlugins\eXpansion\Core\Gui\Controls;

use ManiaLib\Gui\Elements\Label;
use ManiaLivePlugins\eXpansion\Gui\Control;
use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

class ExpSettingListElement extends Control
{

    /** @var ListBackGround */
    protected $bg;
    /** @var Label */
    protected $label_key;
    /** @var Label */
    protected $label_value;
    /** @var Button */
    protected $button_remove;
    /** @var bool */
    public static $large = false;

    /**
     * ExpSettingListElement constructor.
     * @param $indexNumber
     * @param $login
     * @param $action
     */
    public function __construct($indexNumber, $login, $action)
    {

        $this->bg = new ListBackGround($indexNumber, 100, 4);
        $this->addComponent($this->bg);

        $this->label_key = new Label(40, 5);
        $this->label_key->setPosY(2);
        $this->label_key->setId('column_' . $indexNumber . '_0');
        $this->addComponent($this->label_key);

        $this->label_value = new Label(40, 5);
        $this->label_value->setPosY(2);
        $this->label_value->setId('column_' . $indexNumber . '_1');
        $this->addComponent($this->label_value);

        $this->button_remove = new Button(25, 6);
        $this->button_remove->setText(__('Remove', $login));
        $this->button_remove->setAction($action);
        $this->button_remove->setDescription(__('Removes this value', $login), 40);
        $this->button_remove->setId('column_' . $indexNumber . '_2');
        $this->button_remove->setClass("eXpOptimizedPagerAction");
        $this->addComponent($this->button_remove);


        $this->setSize(160, 7);
        $this->setScale(0.8);
    }

    /**
     * @param $oldX
     * @param $oldY
     */
    protected function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

        if (self::$large) {
            $this->label_key->setSizeX(($this->getSizeX() - 25) / 2 - 1);
            $this->label_value->setSizeX(($this->getSizeX() - 25) / 2);
            $this->label_value->setPositionX(($this->getSizeX() - 25) / 2);
        } else {
            $this->label_key->setSizeX(10);
            $this->label_value->setSizeX($this->getSizeX() - 25);
            $this->label_value->setPositionX(12);
        }

        $this->bg->setSize($this->getSizeX(), $this->getSizeY());

        $this->button_remove->setPosition($this->getSizeX() - 20, 0);

    }

    /**
     * @return int
     */
    public function getNbTextColumns()
    {
        return 2;
    }
}
