<?php
namespace ManiaLivePlugins\eXpansion\Maps\Async;

use ManiaLivePlugins\eXpansion\Helpers\GbxReader\Map;
use ManiaLivePlugins\eXpansion\Helpers\Helper;
use Maniaplanet\DedicatedServer\Structures\Version;
use oliverde8\AsynchronousJobs\Callback;
use oliverde8\AsynchronousJobs\Job;
use oliverde8\AsynchronousJobs\JobData;
use Phine\Exception\Exception;

/**
 * Description of MapReadJob
 *
 * @author Petri Järvisalo <petri.jarvisalo@gmail.com>
 */
class MapReadJob extends Job
{

    use Callback;

    /** $var \ManiaLivePlugins\eXpansion\Helpers\GbxReader\Map[] */
    protected $result = array();

    public function end(JobData $jobData)
    {

    }

    public function run()
    {

        $connection = Helper::getSingletons()->getDediConnection();

        /** @var Version */
        $game = $connection->getVersion();
        $path = Helper::getPaths()->getDownloadMapsPath() . $game->titleId . "/*.Map.Gbx";
        $gbx = new Map();
        $maps = glob($path);
        $x = 0;
        if (count($maps) >= 1) {
            foreach ($maps as $filename) {
                try {
                    $this->result[] = $gbx->read($filename);
                } catch (Exception $e) {
                    Helper::log("Error processing file : " . $e->getMessage(), array('eXpansion', 'Maps', 'MapRead'));
                }
            }
        }
    }

    /**
     *
     * @return Map[]
     */
    public function getResult()
    {
        return $this->result;
    }
}