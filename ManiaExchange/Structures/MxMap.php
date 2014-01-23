<?php
/*
 [19] => Array
     (
         [TrackID] => 5082
         [UserID] => 1198
         [Username] => chef
         [UploadedAt] => 2011-09-25T10:56:34.833
         [UpdatedAt] => 2011-09-25T10:56:34.833
         [Name] => Probe(test)
         [TypeName] => Race
         [MapType] => Race
         [TitlePack] =>
         [StyleName] => Race
         [Mood] => Day
         [DisplayCost] => 4450
         [ModName] =>
         [Lightmap] => 5
         [ExeVersion] => 3.0.0
         [ExeBuild] => 2011-09-14_16_1
         [EnvironmentName] => Canyon
         [UnlimiterRequired] =>
         [RouteName] => Single
         [LengthName] => 45 secs
         [Laps] => 0
         [DifficultyName] => Beginner
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
     )
     */

namespace ManiaLivePlugins\eXpansion\ManiaExchange\Structures;

class MxMap extends \Maniaplanet\DedicatedServer\Structures\AbstractStructure {
         public $trackID;
         public $userID;
         public $username;
         public $uploadedAt;
         public $updatedAt;
         public $name;
         public $typeName;
         public $mapType;
         public $titlePack;
         public $styleName;
         public $mood;
         public $displayCost;
         public $modName;
         public $lightmap;
         public $exeVersion;
         public $exeBuild;
         public $environmentName;
         public $unlimiterRequired;
         public $routeName;
         public $lengthName;
         public $laps;
         public $difficultyName;
         public $teplayTypeName;
         public $replayWRID;
         public $replayCount;
         public $trackValue;
         public $comments;
         public $awardCount;
         public $commentCount;
         public $replayWRTime;
         public $replayWRUserID;
         public $replayWRUsername;
}
?>
