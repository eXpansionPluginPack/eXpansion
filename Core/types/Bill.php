<?php

namespace ManiaLivePlugins\eXpansion\Core\types ;

/**
 * Description of Bill
 *
 * @author De Cramer Oliver
 */
class Bill {
    
    private $billId = null;
    
    private $source_login;
    private $destination_login;
    private $amount;
    private $msg;
    
    private $pluginName = '';
    private $subject = '';
    
    private $callback = null;
    private $params = array();
    
    private $errorCallBack;
    
    function __construct($source_login, $destination_login, $amount, $msg) {
        $this->source_login = $source_login;
        $this->destination_login = $destination_login;
        $this->amount = $amount;
        $this->msg = $msg;
    }
    
    public function getBillId() {
        return $this->billId;
    }

    public function setBillId($billId) {
        $this->billId = $billId;
    }
    
    public function getSource_login() {
        return $this->source_login;
    }

    public function getDestination_login() {
        return $this->destination_login;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getMsg() {
        return $this->msg;
    }

    public function setSource_login($source_login) {
        $this->source_login = $source_login;
    }

    public function setDestination_login($destination_login) {
        $this->destination_login = $destination_login;
    }

    public function setAmount($amount) {
        $this->amount = $amount;
    }

    public function setMsg($msg) {
        $this->msg = $msg;
    }
    
    public function getPluginName() {
        return $this->pluginName;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function setPluginName($pluginName) {
        $this->pluginName = $pluginName;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
    }

        
    public function setValidationCallback($callback, $params) {
        $this->callback = $callback;
        $this->params = $params;
    }
    
    public function setErrorCallback($errorNum, $callback, $params=array()){
        $this->errorCallBack[$errorNum] = array($callback, $params);
    }

    
    public function validate(){
        if($this->callback != null){
            $params = $this->params;
            array_unshift($params, $this);
            call_user_func_array($this->callback, $params);
        }
    }
    
    public function error($erroNum, $stateName){
        if(isset($this->errorCallBack[$erroNum]))
            call_user_func_array($this->errorCallBack[$erroNum][0],  array($this, $erroNum, $stateName));
        
    }
    
}

?>
