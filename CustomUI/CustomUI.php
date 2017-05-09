<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

use ManiaLivePlugins\eXpansion\Core\types\config\types\Boolean;

class CustomUI extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    public function eXpOnLoad()
    {
        // $this->enableDedicatedEvents();
    }

    public function eXpOnReady()
    {

        $this->updateData();
    }

    /**
     * displayWidget(string $login)
     *
     * @param string $login
     */
    protected function updateData()
    {
        $variables = $this->getMetaData()->getAllVariables();
        $code = "<ui_properties>\n";
        foreach ($variables as $variable) {
            $varName = strtolower($variable->getName());
            if ($variable instanceof Boolean) {
                $code .= "<" . $varName . ' visible="' . (($variable->getRawValue()) ? 'true' : 'false') . '" />' . "\n";
            }
        }
        $code .= "</ui_properties>\n";
        if ($this->expStorage->simpleEnviTitle == "TM") {
            $this->connection->triggerModeScriptEvent("Trackmania.UI.SetProperties", [$code]);
        } else {
            $this->connection->triggerModeScriptEvent("Shootmania.SetUIProperties", [$code]);
        }

    }

    public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getConfigInstance() instanceof \ManiaLivePlugins\eXpansion\CustomUI\Config) {
            $this->updateData();
        }
    }

    public function eXpOnUnload()
    {

    }
}
