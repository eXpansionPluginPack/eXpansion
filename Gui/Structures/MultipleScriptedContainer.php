<?php

namespace ManiaLivePlugins\eXpansion\Gui\Structures;

/**
 * Description of ScriptedContainer
 *
 * @author De Cramer Oliver
 */
interface MultipleScriptedContainer
{

    /**
     * @return Script[] All the scripts this component needs
     */
    public function getScripts();

}

?>
