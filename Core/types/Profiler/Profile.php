<?php

namespace ManiaLivePlugins\eXpansion\Core\types\Profiler;

use ManiaLivePlugins\eXpansion\Core\Analytics;
use ManiaLivePlugins\eXpansion\Helpers\Helper;

/**
 * Class Profile
 *
 * @author    de Cramer Oliver<oldec@smile.fr>
 * @copyright 2017
 * @package ${NAMESPACE}
 */
class Profile
{
    /** @var Analytics|null Analytics to push into */
    protected $analytics;

    /** @var string The name of the task */
    protected $taskName;

    /** @var string The Description */
    protected $desription;

    /** @var int Numer of elemnts */
    protected $numberOfElements;

    /** @var mixed Time at which the prifling */
    protected $startTime;

    protected $endTime;

    /**
     * Create a new profile instance.
     *
     * @param string $taskName Name of the profiling task
     * @param string $desription Description
     * @param int $numberOfElements Numerical value to represent the complexity of the task
     * @param null|Analytics $analytics Numerical value to represent the complexity of the task
     */
    public function __construct($taskName, $desription, $numberOfElements, $analytics = null)
    {
        $this->taskName = $taskName;
        $this->desription = $desription;
        $this->numberOfElements = $numberOfElements;
        $this->analytics = $analytics;

        $this->startTime = microtime(true);

        $tags = array('Profiling') + explode('.', $taskName);
        Helper::logInfo("Starting  @ " . $this->startTime, $tags);
    }

    /**
     * End the profiling.
     *
     * This will log the result of the profiling & will send if activated the
     */
    public function end()
    {
        $this->endTime = microtime(true);

        if (!is_null($this->analytics)) {
            $this->analytics->ping(null, $this);
        }

        $tags = array('Profiling') + explode('.', $this->taskName);
        Helper::logInfo("Ended @ " . $this->startTime . " - Took : " . $this->getDuration() . "ms", $tags);
    }

    /**
     * Get the duration of the profile.
     *
     * @return mixed
     */
    public function getDuration()
    {
        return $this->endTime - $this->startTime;
    }

    /**
     * The name of the profling task.
     *
     * @return string
     */
    public function getTaskName()
    {
        return $this->taskName;
    }

    /**
     * Description of the profiling task.
     *
     * @return string
     */
    public function getDesription()
    {
        return $this->desription;
    }

    /**
     * Number of elements that is being processed
     *
     * @return int
     */
    public function getNumberOfElements()
    {
        return $this->numberOfElements;
    }

    /**
     * @param string $desription
     */
    public function setDesription($desription)
    {
        $this->desription = $desription;
    }

    /**
     * @param int $numberOfElements
     */
    public function setNumberOfElements($numberOfElements)
    {
        $this->numberOfElements = $numberOfElements;
    }

    /**
     * Time at which the profile started.
     *
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Time at which the profile ended.
     *
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * When the object is destroyed, end the profiling.
     */
    public function __destruct()
    {
        $this->end();
    }
}