<?php

namespace ManiaLivePlugins\eXpansion\Core\types;

/**
 * Description of Bill
 *
 * @author De Cramer Oliver
 */
class Bill
{

    private $billId = null;

    private $sourceLogin;
    private $destinationLogin;
    private $amount;
    private $msg;

    private $pluginName = '';
    private $subject = '';

    private $callback = null;
    private $params = array();

    private $errorCallBack;

    public function __construct($source_login, $destination_login, $amount, $msg)
    {
        $this->sourceLogin = $source_login;
        $this->destinationLogin = $destination_login;
        $this->amount = $amount;
        $this->msg = $msg;
    }

    public function getBillId()
    {
        return $this->billId;
    }

    public function setBillId($billId)
    {
        $this->billId = $billId;
    }

    public function getSource_login()
    {
        return $this->sourceLogin;
    }

    public function getDestination_login()
    {
        return $this->destinationLogin;
    }

    public function getAmount()
    {
        return intval($this->amount);
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function setSource_login($sourceLogin)
    {
        $this->sourceLogin = $sourceLogin;
    }

    public function setDestination_login($destinationLogin)
    {
        $this->destinationLogin = $destinationLogin;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setPluginName($pluginName)
    {
        $this->pluginName = $pluginName;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }


    public function setValidationCallback($callback, $params)
    {
        $this->callback = $callback;
        $this->params = $params;
    }

    public function setErrorCallback($errorNum, $callback, $params = array())
    {
        $this->errorCallBack[$errorNum] = array($callback, $params);
    }


    public function validate()
    {
        if ($this->callback != null) {
            $params = $this->params;
            array_unshift($params, $this);
            call_user_func_array($this->callback, $params);
        }
    }

    public function error($erroNum, $stateName)
    {
        if (isset($this->errorCallBack[$erroNum])) {
            call_user_func_array($this->errorCallBack[$erroNum][0], array($this, $erroNum, $stateName));
        }

    }

}
