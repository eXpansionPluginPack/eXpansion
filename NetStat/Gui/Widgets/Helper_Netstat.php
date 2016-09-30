<?php
namespace ManiaLivePlugins\eXpansion\NetStat\Gui\Widgets;

/**
 * Description of widget_netstat
 *
 * @author Petri
 */
class Helper_Netstat extends \ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget
{

    protected $iLogin;
    protected $iStamp;
    protected $latency;
    protected $script;

    protected function onConstruct()
    {
        parent::onConstruct();
        $this->setName('Netstat Helper');

        $input = new \ManiaLib\Gui\Elements\Entry(20, 7);
        $input->setId("sendStamp");
        $input->setName('stamp');
        $input->setPosY(-7 + 1000);
        $this->iStamp = $input;
        $this->addComponent($this->iStamp);

        $input = new \ManiaLib\Gui\Elements\Entry(20, 7);
        $input->setId("latency");
        $input->setName('latency');
        $input->setPosY(-14 + 1000);
        $this->latency = $input;
        $this->addComponent($this->latency);


        $this->script = new \ManiaLivePlugins\eXpansion\Gui\Structures\Script("NetStat/Gui/Scripts/Netstat");
        $this->script->setParam('updateFreq', 5000);
        $this->registerScript($this->script);
    }

    public function setActionId($value)
    {
        $this->script->setParam('action', $value);
    }
}
