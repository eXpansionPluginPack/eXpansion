<?php

namespace ManiaLivePlugins\eXpansion\Statistics;

use ManiaLive\Event\Dispatcher;
use ManiaLive\Utilities\Console;

use ManiaLivePlugins\eXpansion\Core\i18n\Message;
use \ManiaLivePlugins\eXpansion\LocalRecords\Config;
use \ManiaLivePlugins\eXpansion\LocalRecords\Events\Event;
use ManiaLivePlugins\eXpansion\LocalRecords\Structures\Record;

use ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

class Statistics extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    
    public function exp_onReady() {
        parent::exp_onReady();
        $this->enableDatabase();
        
        $this->registerChatCommand("topdonators", 'showTopDonators', 0, true);
    }
    
    public function showTopDonators($login){
        
        $this->storage->serverLogin;
        $sql = 'SELECT transaction_fromLogin as login, player_nickname as nickname, SUM(transaction_amount) as totalPlanets'
                . ' FROM exp_planet_transaction, exp_players'
                . ' WHERE transaction_toLogin = '.$this->db->quote( $this->storage->serverLogin).''
                    . ' AND transaction_subject = \'server_donation\''
                    . ' AND transaction_fromLogin = player_login'
                . ' GROUP BY transaction_fromLogin, player_nickname'
                . ' ORDER BY totalPlanets DESC'
                . ' LIMIT 0, 100';         
        
        $datas = $this->getData($sql);
        
        \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmount::Erase($login);
        $window = \ManiaLivePlugins\eXpansion\Statistics\Gui\Windows\ServerDonationAmount::Create($login);
        $window->setTitle(__('Top Server Donators(Amount)', $login));
        $window->centerOnScreen();
        $window->populateList($datas);
        $window->setSize(70, 100);
        $window->show();
    }
    
    public function getData($sql){
        $dbData = $this->db->query($sql);

        if ($dbData->recordCount() == 0) {
            return array();
        }

        
        $i = 0;
        $datas = array();
        while ($data = $dbData->fetchArray()) {
            $datas[$i] = $data;
            array_unshift($datas[$i], $i+1);
            $i++;
        }
        return $datas;
    }
   
}

?>
