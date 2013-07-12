<?php

namespace ManiaLivePlugins\eXpansion\Dedimania;

use \ManiaLive\Event\Dispatcher;
use ManiaLivePlugins\Reaby\Dedimania\Events\Event as dediEvent;
use ManiaLivePlugins\eXpansion\Dedimania\Config;
use ManiaLive\Utilities\Console;

class Dedimania extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin implements \ManiaLivePlugins\Reaby\Dedimania\Events\Listener {

    private $config;
    private $recs = array();

    public function exp_onInit() {
        parent::exp_onInit();
        $this->addDependency(new \ManiaLive\PluginHandler\Dependency("Reaby\\Dedimania"));
        $this->config = Config::getInstance();
    }

    public function exp_onLoad() {
        parent::exp_onLoad();
        \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->registerCode("dedirecord", $this->config->color_dedirecord);
    }

    public function exp_onReady() {
        parent::exp_onReady();
        if ($this->isPluginLoaded('Reaby\\Dedimania'))
            $this->callPublicMethod('Reaby\\Dedimania', 'disableMessages');
        Dispatcher::register(dediEvent::getClass(), $this);
        $this->registerChatCommand("dedirecs", "showRecs", 0, true);
    }

    public function onDedimaniaGetRecords($data) {
        $this->recs = $data['Records'];
    }

    public function showRecs($login) {
        Gui\Windows\Records::Erase($login);
        try {
            $window = Gui\Windows\Records::Create($login);
            $window->setTitle(__('DediRecords on a Map', $login));
            $window->centerOnScreen();
            $window->populateList($this->recs);
            $window->setSize(120, 100);
            $window->show();
        } catch (\Exception $e) {
            echo $e->getFile() . ":". $e->getLine();
        }
    }

    public function onDedimaniaNewRecord($record) {
        $recepient = $record->login;
        if ($this->config->show_record_msg_to_all)
            $recepient = null;

        $this->exp_chatSendServerMessage($this->config->newRecordMsg, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, \ManiaLive\Utilities\Time::fromTM($record->time)));
    }

    public function onDedimaniaOpenSession() {
        
    }

    public function onDedimaniaPlayerConnect($data) {
        if (!$this->config->show_welcome_msg)
            return;

        if ($data == null)
            return;

        if ($data['Banned'])
            return;

        $upgrade = true;
        $player = $this->storage->getPlayerObject($data['Login']);
        $type = '$fffFree';

        if ($data['MaxRank'] > 15) {
            $type = '$ff0Premium$fff';
            $upgrade = false;
        }


        $msg = $this->config->supportMsg;
        if ($upgrade)
            $msg = $this->config->upgradeMsg;
        $this->exp_chatSendServerMessage($msg, $data['Login'], array($data['MaxRank']));
    }

    public function onDedimaniaPlayerDisconnect() {
        
    }

    public function onDedimaniaRecord($record, $oldRecord) {
        $recepient = $record->login;
        if ($this->config->show_record_msg_to_all)
            $recepient = null;

        $diff = \ManiaLive\Utilities\Time::fromTM($record->time - $oldRecord->time, true);
        $this->exp_chatSendServerMessage($this->config->recordMsg, $recepient, array(\ManiaLib\Utils\Formatting::stripCodes($record->nickname, "wos"), $record->place, \ManiaLive\Utilities\Time::fromTM($record->time), $diff));
    }

    public function onDedimaniaUpdateRecords($data) {
        $this->recs = $data['Records'];
    }

}

?>
