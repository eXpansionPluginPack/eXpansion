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
    private $originalMessage;

    /**
     * List of all the messages in different languages
     * @var Array(String => String) 
     */
    private $lmessages = array();

    function __construct($orginalMessage) {
        $this->originalMessage = $orginalMessage;
    }

    public function addLanguageMessage($lang, $message) {
        $this->lmessages[$lang] = $message;
    }

    /**
     * getMultiLangArray()
     * Returns a multilanguage messassage array to be used with Connection->ChatSendServerMessageToLanguage();
     * @return Array(String => String) 
     */
    public function getMultiLangArray($args) {        
        $temp = $this->lmessages;
        $temp['en'] = $this->originalMessage;
        $out = array();

        foreach ($temp as $lang => $msg) {
            $arrgs = $args;
            array_unshift($arrgs, $msg);
            $text = call_user_func_array('sprintf', $arrgs);
            $out[] = array("Lang" => lcfirst($lang), "Text" => \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance()->parseColors($text) );
        }
        return $out;
    }

    public function getMessage($lang = null) {
        echo $lang;
        if ($lang == null) {
            return isset($this->lmessages[self::$defaultLanguage]) ? $this->lmessages[self::$defaultLanguage] : $this->originalMessage;
        } else if (isset($this->lmessages[$lang]))
            return $this->lmessages[$lang];
        else {
            return isset($this->lmessages[self::$defaultLanguage]) ? $this->lmessages[self::$defaultLanguage] : $this->originalMessage;
        }
    }

}

?>
