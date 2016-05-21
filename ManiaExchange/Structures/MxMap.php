<?php

/* [19] => Array
  (
  [TrackID] => 63857
  [UserID] => 5237
  [Username] => cricri56
  [UploadedAt] => 2014-05-06T17:53:10.38
  [UpdatedAt] => 2014-05-06T17:53:10.38
  [Name] => Cliffs of Sanity (mix valley)
  [TypeName] => Race
  [MapType] => Race
  [TitlePack] => Trackmania_2
  [StyleName] => Race
  [Mood] => Day
  [DisplayCost] => 5682
  [ModName] =>
  [Lightmap] => 6
  [ExeVersion] => 3.3.0
  [ExeBuild] => 2014-05-02_17_3
  [EnvironmentName] => Canyon
  [VehicleName] => ValleyCar
  [UnlimiterRequired] =>
  [RouteName] => Single
  [LengthName] => 1 min
  [Laps] => 1
  [DifficultyName] => Intermediate
  [ReplayTypeName] =>
  [ReplayWRID] =>
  [ReplayCount] => 0
  [TrackValue] => 0
  [Comments] =>
  [AwardCount] => 0
  [CommentCount] => 0
  [ReplayWRTime] =>
  [ReplayWRUserID] =>
  [ReplayWRUsername] =>
  [Unreleased] =>
  [GbxMapName] => $ccc$oCliffs of Sanity (mix valley)
  [RatingVoteCount] => 0
  [RatingVoteAverage] => 0
  [HasScreenshot] =>
  [HasThumbnail] => 1
  )
 */

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Structures;

class MxMap extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure
{

    public $trackID;

    public $userID;

    public $username;

    public $uploadedAt;

    public $updatedAt;

    public $name;

    /** @var string contains mapname with color codes */
    public $gbxMapName;

    public $typeName;

    public $mapType;

    public $titlePack;

    public $styleName;

    public $mood;

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

}
