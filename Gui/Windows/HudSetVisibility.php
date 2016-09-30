<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class HudSetVisibility extends \ManiaLive\Gui\Window
{
    protected $xml;

    /** @var \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem[] */
    private $data;

    protected function onConstruct()
    {
        $this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function onDraw()
    {
        $this->removeComponent($this->xml);
        $content = '<script><!--
                       main () {
			declare Boolean exp_needToCheckPersistentVars for UI = False;
                        declare persistent Boolean[Text][Text][Text] eXp_widgetVisible;
			';
        foreach ($this->data as $item) {
            $bool = "False";
            if ($item->value) {
                $bool = "True";
            }
            $content .= "eXp_widgetVisible[\"" . \ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION
                . "\"][\"" . $item->id . "\"][\"" . $item->gameMode . "\"] = " . $bool . "; \n";
        }

        $content .= '
	    exp_needToCheckPersistentVars = True;               
	    }
		
                --></script>';
        $this->xml->setContent($content);
        $this->addComponent($this->xml);
        parent::onDraw();
    }

    public function destroy()
    {
        parent::destroy();
    }
}
