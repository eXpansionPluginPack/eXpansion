
declare Integer nbShow = <?= $this->nbRecord ?>;
declare Integer curCp = 0;
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields =  <?= $this->nbFields ?>;
declare Integer nbFirstFields =  <?= $this->nbFirstFields ?>;
declare Integer worseTime = -1;
declare Boolean getCurrentTimes = <?= $this->getCurrentTimes ?>;

declare Integer maxServerRank = <?= $this->acceptMaxServerRank ?>;
declare Integer[Text] maxPlayerRank = <?= $this->acceptMaxPlayerRank ?>;
declare useMaxPlayerRank = <?= $this->useMaxPlayerRank ?>;
declare Integer acceptMinCp = <?= $this->acceptMinCp ?>;


//Ranks of the players
declare playerTimes = <?= $this->playerTimes ?>;


//The latest chackpoint the player past
declare playerNickName = <?= $this->playerNicks ?>;
declare playersOnServer = Text[Text];
declare recordLogin = "";
declare nbCount = 0;

// test
declare Player = Null;
declare Boolean needUpdate = True;
declare lastUpdateTime = 0;

if(getCurrentTimes){
    //sleep(1000);
}

//Updating currently connected players
foreach (Player in Players) {
    declare <?= $this->varName ?> for Player = -1;
    <?= $this->varName ?> = -1;
    
    if(getCurrentTimes && Player.CurRace.Time != -1){
	//log("Getting Current Times for "^Player.Login^" Time : "^Player.CurRace.Time);
	//playerTimes[Player.Login] = Player.CurRace.Time;
    }
    
    playersOnServer[Player.Login] = Player.Name;
    if(!playerNickName.existskey(Player.Login)){
        playerNickName[Player.Login] = Player.Name;
    }
}

declare origPlayerTimes = playerTimes;

<?php

//Dump players to test stu


?>
