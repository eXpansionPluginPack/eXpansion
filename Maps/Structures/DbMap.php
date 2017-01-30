<?php

namespace ManiaLivePlugins\eXpansion\Maps\Structures;

class DbMap
{
    public $trackID;

    public $trackUID;

    public $userID;

    public $username;

    public $uploadedAt;

    public $updatedAt;

    /** @var string contains mapname with color codes */
    public $gbxMapName;

    public $typeName;

    public $mapType;

    public $titlePack;

    public $styleName;

    public $displayCost;

    public $modName;

    /** @var Lightmap version, currently 6 */
    public $lightmap;

    public $exeVersion;

    public $exeBuild;

    /** Canyon / Valley / Stadium */
    public $environmentName;

    /** CanyonCar / ValleyCar / StadiumCar */
    public $vehicleName;

    public $unlimiterRequired;

    public $routeName;

    public $lengthName;

    public $laps;

    public $difficultyName;

    public $replayTypeName;

    public $replayWRID;

    public $replayCount;

    public $trackValue;

    public $comments;

    public $commentCount;

    public $awardCount;

    public $replayWRTime;

    public $replayWRUserID;

    public $replayWRUsername;

    public $ratingVoteCount;

    public $ratingVoteAverage;

    public $hasScreenshot;

    public $hasThumbnail;

    public $id;

    public $uId;

    public $file;

    public $name;

    public $nameStripped;

    public $author;

    public $environment;

    public $mood;

    public $bronzeTime;

    public $silverTime;

    public $goldTime;

    public $authorTime;

    public $copperPrice;

    public $lapRace;

    public $nbLaps;

    public $nbCheckpoints;

    public $addedby;

    public $addtime;

    static public function fromArray($array)
    {
        if (!is_array($array))
            return $array;

        $object = new static;
        foreach ($array as $key => $value) {
            $parts = explode("_", $key);
            $key = $parts[1];
            if ($key == "uid")
                $key = "uId";

            if ($key == "file")
                $key = "fileName";

            $object->{lcfirst($key)} = $value;
        }

        return $object;
    }

    static public function fromArrayOfArray($array)
    {
        if (!is_array($array))
            return $array;

        $result = array();
        foreach ($array as $key => $value)
            $result[$key] = static::fromArray($value);
        return $result;
    }

}