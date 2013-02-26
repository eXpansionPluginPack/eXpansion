<?php

namespace ManiaLivePlugins\eXpansion\Core;

/**
 * a simple internationalization class for string texts
 * 
 */
class i18n extends \ManiaLib\Utils\Singleton {

    /**
     * Current locale string
     * @var string $language 
     */
    private $defaultLanguage = null;

    /**
     * Translated messages by language
     * @var array
     */
    private $messages = array();

    /**
     * Supported locales
     * @var array
     */
    private $supportedLocales = array();
    
    
    private $directorties = array();
    
    private $started = false;
    
    public function registerDirectory($dir){
        if($this->started){
            $this->readFiles($dir);
            $this->supportedLocales = array_unique($this->supportedLocales);
        }  else {
            $this->directorties[] = $dir;
        }
    }
    
    public function start(){
        if(!empty($this->directorties)){
            foreach ($this->directorties as $dir){
                $this->readFiles($dir);
            }
            $this->supportedLocales = array_unique($this->supportedLocales);
        }
    }
    
    protected function readFiles($dir) {
        if(is_dir($dir. "/messages/")){
            $langFiles = glob($dir. "/messages/*.txt", GLOB_MARK);
            foreach ($langFiles as $file) {
                $language = explode("/", $file);
                $language = end($language);
                $language = str_replace(".txt", "", $language);
                $this->supportedLocales[] = $language;
                $data = file($file, FILE_IGNORE_NEW_LINES);
                for ($x = 0; $x < count($data) - 1; $x += 3) {
                    $orig = trim($data[$x]);
                    $trans = trim($data[$x + 1]);
                    $this->messages[$language][$orig] = $trans;
                }
            }
        }
    }

    public function setDefaultLanguage($language) {
        $this->defaultLanguage = $language;
    }

    public function getString($string, $fromLanguage = null) {
        if ($fromLanguage == null)
            return $this->translate($string, $this->defaultLanguage);
        return $this->translate($string, $fromLanguage);
    }
    
    public function getSupportedLocales() {
        return $this->supportedLocales;        
    }
    
    private function translate($string, $language) {
        if ($language == null) {
            return $string;
        } else {
            if (array_key_exists($string, $this->messages[$language])) {
                return $this->messages[$language][$string];
            }
            else
                return $string;
        }
        // fail safe to ensure default message is delivered;
        return $string;
    }

}

?>