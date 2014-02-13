
declare Integer nbShow = <?= $this->nbRecord ?>;
declare Integer curCp = 0;
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields =  <?= $this->nbFields ?>;
declare Integer nbFirstFields =  <?= $this->nbFirstFields ?>;
declare Integer worseTime = -1;

declare Integer maxServerRank = <?= $this->acceptMaxServerRank ?>;
declare Integer[Text] maxPlayerRank = <?= $this->acceptMaxPlayerRank ?>;
declare useMaxPlayerRank = <?= $this->useMaxPlayerRank ?>;
declare Integer acceptMinCp = <?= $this->acceptMinCp ?>;


//Ranks of the players
declare playerTimes = <?= $this->playerTimes ?>;
declare origPlayerTimes = playerTimes;

//The latest chackpoint the player past
declare playerNickName = <?= $this->playerNicks ?>;
declare playersOnServer = Text[Text];
declare recordLogin = "";
declare nbCount = 0;

// test
declare Player = Null;
declare Boolean needUpdate = True;
declare lastUpdateTime = 0;

//Updating currently connected players
foreach (Player in Players) {
    declare <?= $this->varName ?> for Player = -1;
    <?= $this->varName ?> = -1;
    declare firstFinish<?= $this->varName ?> for Player = True;
    firstFinish<?= $this->varName ?>  = True;
    declare playerTime<?= $this->varName ?> for Player = -1;
    playerTime<?= $this->varName ?> = -1;
    
    playersOnServer[Player.Login] = Player.Name;
    if(!playerNickName.existskey(Player.Login)){
        playerNickName[Player.Login] = Player.Name;
    }
}

<?php

//Dump players to test stu


?>
