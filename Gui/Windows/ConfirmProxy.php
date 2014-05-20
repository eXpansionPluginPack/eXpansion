<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class ConfirmProxy extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget {

    private $iscript;

    protected function onConstruct() {
	parent::onConstruct();
	$this->iscript = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("Gui\Scripts\Confirm");
	$this->iscript->setParam("invokeAction", "");
	$this->registerScript($this->iscript);
    }

    protected function onDraw() {
	parent::onDraw();
	echo "invoke window drawn! \n";
    }

    public function setInvokeAction($action) {
	$this->iscript->setParam("invokeAction", $action);
    }

}

?>
