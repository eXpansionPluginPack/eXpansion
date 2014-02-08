<?php

namespace ManiaLivePlugins\eXpansion\Menu\Gui\Widgets;

class Submenu extends \ManiaLivePlugins\eXpansion\Gui\Windows\PlainWidget {

    private $menu, $debug, $bg;
    public $myscript;
    private $item = array();
    private $submenu = array();

    public function addItem(&$menu, $text, $action = null, $submenuNb = false) {
        $nb = count($this->item);
        $this->item[$nb] = new \ManiaLib\Gui\Elements\Label();

        $this->item[$nb]->setStyle("TextChallengeNameMedium");
        $this->item[$nb]->setAlign("left", "center");
        $this->item[$nb]->setSize(25, 4.5);
        $this->item[$nb]->setFocusAreaColor1("5af9");
        $this->item[$nb]->setFocusAreaColor2("5af9");

        if (!empty($action)) {
            $this->item[$nb]->setFocusAreaColor2("fffb");
            $this->item[$nb]->setAction($action);
        }

        $this->item[$nb]->setText("  " . $text);
        $this->item[$nb]->setTextColor('ffff');
        $this->item[$nb]->setTextSize(1.75);
        $this->item[$nb]->setPosZ(40);

        if ($submenuNb !== false) {
            $this->item[$nb]->setId("sub_" . $submenuNb);
            $this->item[$nb]->setFocusAreaColor2("fffb");
        } else {

            $snb = false;
            foreach ($this->submenu as $subNb => $sub) {
                if ($sub === $menu) {
                    $snb = $subNb;
                    break;
                }
            }
            if ($snb) {
                $this->item[$nb]->setId("sub_" . $snb . "_item_" . $nb);
                $this->item[$nb]->setFocusAreaColor2("fffb");
                $this->item[$nb]->setAction($action);
            } else {
                $this->item[$nb]->setId("item_" . $nb);
            }
        }
        $this->item[$nb]->setScriptEvents();
        $menu->addComponent($this->item[$nb]);
    }

    public function addSubMenu(&$menu, $text) {
        $mb = count($this->submenu) + 1;
        $this->submenu[$mb] = new \ManiaLive\Gui\Controls\Frame(25, 4.5);
        $this->submenu[$mb]->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->submenu[$mb]->setId("submenu_" . $mb);
        $this->submenu[$mb]->setScriptEvents();
        // add item to menu
        $this->addItem($menu, $text . " » ", null, $mb);
        // add component to menu
        $menu->addComponent($this->submenu[$mb]);

        return $this->submenu[$mb];
    }

    public function getMenu() {
        return $this->menu;
    }

    protected function onConstruct() {
        parent::onConstruct();
        $this->menu = new \ManiaLive\Gui\Controls\Frame();
        $this->menu->setLayout(new \ManiaLib\Gui\Layouts\Column());
        $this->menu->setId("Submenu");
        $this->menu->setScriptEvents();
        $this->addComponent($this->menu);

        $inputbox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox("widgetStatus");
        $inputbox->setPosition(900, 900);
        $inputbox->setScriptEvents();
        $this->addComponent($inputbox);

        $this->myscript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Menu\Gui\Scripts");
        $this->registerScript($this->myscript);
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    protected function onDraw() {
        $count = count($this->submenu);
        $version = \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION;
        $this->myscript->setParam("version", $version);
        $this->myscript->setParam("count", $count);
        parent::onDraw();
    }

    protected function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);
        $this->bg->setSize(30, ($this->itemNb * 4.5) + 1);
        $this->bg->setPosition(-2, 2);
    }

}

?>
