declare Integer nbShow = <?php echo $this->nbRecord ?>;
declare Integer curCp = 0;
declare Integer totalCp = <?php echo $this->totalCp ?>;
declare Integer nbFields =  <?php echo $this->nbFields ?>;
declare Integer nbFirstFields =  <?php echo $this->nbFirstFields ?>;
declare Integer worseTime = -1;
declare Boolean getCurrentTimes = <?php echo $this->getCurrentTimes ?>;

declare Integer maxServerRank = <?php echo $this->acceptMaxServerRank ?>;
declare Integer[Text] maxPlayerRank = <?php echo $this->acceptMaxPlayerRank ?>;
declare useMaxPlayerRank = <?php echo $this->useMaxPlayerRank ?>;
declare Integer acceptMinCp = <?php echo $this->acceptMinCp ?>;


//Ranks of the players
declare playerTimes = <?php echo $this->playerTimes ?>;


//The latest chackpoint the player past
declare playerNickName = <?php echo $this->playerNicks ?>;
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
declare <?php echo $this->varName ?> for Player = -1;
<?php echo $this->varName ?> = -1;

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

