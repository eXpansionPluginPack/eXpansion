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
    
    private $callback = null;
    private $params = array();
    
    private $errorCallBack;
    
    function __construct($source_login, $destination_login, int $amount, $msg) {
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
    
    public function setValidationCallback($callback, $params) {
        $this->callback = $callback;
        $this->params = $params;
    }
    
    public function setErrorCallback($errorNum, $callback, $params){
        $this->errorCallBack[$errorNum] = array($callback, $params);
    }

    
    public function validate(){
        if($this->callback != null)
            call_user_func($this->callback, $this->params);
    }
    
    public function error($erroNum){
        if(isset($this->errorCallBack[$erroNum]))
            call_user_func ($this->errorCallBack[$erroNum][0], $this->errorCallBack[$erroNum][1]);
    }
    
}

?>
