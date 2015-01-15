<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;
use ManiaLivePlugins\eXpansion\Adm\Gui\Controls\MatchSettingsFile;
use ManiaLive\Gui\ActionHandler;

class ConfirmDialog extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    protected $ok;
    protected $cancel;
    
    protected $actionOk;
    protected $actionCancel;
    
    protected $title;
    private $action;

    protected function onConstruct() {
	parent::onConstruct();
	$login = $this->getRecipient();
	$this->actionOk = $this->createAction(array($this, "Ok"));
	$this->actionCancel = $this->createAction(array($this, "Cancel"));

	$this->ok = new OkButton();
	$this->ok->colorize("0d0");
	$this->ok->setPosition(4,0);
	$this->ok->setText(__("Yes", $login));
	$this->ok->setAction($this->actionOk);
	$this->mainFrame->addComponent($this->ok);

	$this->cancel = new OkButton();
	$this->cancel->setPosition(30,0);
	$this->cancel->setText(__("No", $login));
	$this->cancel->colorize("d00");
	$this->cancel->setAction($this->actionCancel);
	$this->mainFrame->addComponent($this->cancel);

	$this->setSize(57, 10);
	$this->setTitle(__("Really do this ?", $login));
	
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
    }

    function setInvokeAction($action) {
	$this->action = $action;
    }

    function Ok($login) {
	$action = ConfirmProxy::Create($login);
	$action->setInvokeAction($this->action);
	$action->setTimeOut(1);	
	$action->show();	
	$this->Erase($login);
    }

    function Cancel($login) {
	$this->erase($login);
    }

    function destroy() {
	$this->ok->destroy();
	$this->cancel->destroy();
	$this->destroyComponents();
	parent::destroy();
    }

}

?>
