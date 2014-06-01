<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood;

use ManiaLivePlugins\eXpansion\Core\types\config\types\BasicList;
use ManiaLivePlugins\eXpansion\Core\types\config\types\Int;
use ManiaLivePlugins\eXpansion\Core\types\config\types\String;
use ManiaLivePlugins\eXpansion\Core\types\config\Variable;

/**
 * Description of MetaData
 *
 * @author Petri
 */
class MetaData extends \ManiaLivePlugins\eXpansion\Core\types\config\MetaData
{

    public function onBeginLoad()
    {
        parent::onBeginLoad();
        $this->setName("Server Neighborhood");
        $this->setDescription('Connects to other server to show information');

        $config = Config::getInstance();

        $var = new Int('refresh_interval', "Refresh the Panel every [INT] seconds", $config, Variable::SCOPE_GLOBAL);
        $var->setGroup("Server Neighborhood");
        $this->registerVariable($var);

        $var = new Int('nbElement', "Number of element in Widget", $config, Variable::SCOPE_GLOBAL);
        $var->setGroup("Server Neighborhood");
        $this->registerVariable($var);

        $var = new String('storing_path', "Path to store server information", $config, Variable::SCOPE_SERVER);
        $var->setGroup("Server Neighborhood");
        $this->registerVariable($var);

        $type = new String("","",null);
        $var = new BasicList('servers', "Path to each server information", $config, Variable::SCOPE_SERVER);
        $var->setGroup("Server Neighborhood");
        $var->setType($type);
        $this->registerVariable($var);

    }

}
