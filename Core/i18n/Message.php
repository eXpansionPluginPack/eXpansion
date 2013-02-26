<?php

namespace ManiaLivePlugins\eXpansion\Core\i18n;

/**
 * Description of Message
 *
 * @author oliverde8
 */
class Message {
    
    public static $defaultLanguage = "en";
    /**
     * The orginal message as used in ManiaLive
     */
    private $orginalMessage;
    
    /**
     * List of all the massages in different languages
     * @var Array(String => String) 
     */
    private $lmessages = array();
    
    function __construct($orginalMessage) {
        $this->orginalMessage = $orginalMessage;
    }

    public function addLanguageMessage($lang, $message){
        $this->lmessages[$lang] = $message;
    }
    
    public function getMessage($lang = null){
        if($lang == null){
            return isset($this->lmessages[self::$defaultLanguage]) ? $this->lmessages[self::$defaultLanguage] : $this->orginalMessage;
        }else if(isset($this->lmessages[$lang]))
            return $this->lmessages[$lang];
        else {
           return isset($this->lmessages[self::$defaultLanguage]) ? $this->lmessages[self::$defaultLanguage] : $this->orginalMessage;
        }        
    }
    
}

?>
