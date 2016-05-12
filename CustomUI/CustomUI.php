<?php

namespace ManiaLivePlugins\eXpansion\CustomUI;

use ManiaLivePlugins\eXpansion\CustomUI\Gui\Customizer;

class CustomUI extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    function eXpOnLoad()
    {
        // $this->enableDedicatedEvents();
    }

    function eXpOnReady()
    {

        $this->displayWidget(null);
    }

    /**
     * displayWidget(string $login)
     *
     * @param string $login
     */
    function displayWidget($login)
    {
        Customizer::EraseAll();
        $info = Customizer::Create(null);
        $info->update($this->getMetaData()->getAllVariables());
        $info->setSize(60, 15);
        $info->show();
    }

    function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
    {
        if ($var->getConfigInstance() instanceof \ManiaLivePlugins\eXpansion\CustomUI\Config) {
            $this->displayWidget(null);
        }
    }

    function eXpOnUnload()
    {
        Customizer::EraseAll();
    }

}

?>

