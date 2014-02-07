declare Integer nbShow = <?= $this->nbRecord ?>;
declare Integer curCp = 0;
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields =  <?= $this->nbFields ?>;
declare Integer nbFirstFields =  <?= $this->nbFirstFields ?>;

declare Integer maxServerRank = <?= $this->acceptMaxServerRank ?>;
declare Integer[Text] maxPlayerRank = <?= $this->acceptMaxPlayerRank ?>;

declare useMaxPlayerRank = <?= $this->useMaxPlayerRank ?>;

declare Integer acceptMinCp = <?= $this->acceptMinCp ?>;

//Ranks of the players
declare playerTimes = <?= $this->playerTimes ?>;
declare origPlayerTimes = playerTimes;

//The latest chackpoint the player past
declare playerCheckPoint = Integer[Text];
declare playerNickName = <?= $this->playerNicks ?>;
declare playersOnServer = Text[Text];
declare recordLogin = "";
declare nbCount = 0;

// test
declare Player = Null;
declare Boolean needUpdate = True;
declare Boolean atStart = True;

<?php
/*
//Dump players to test stu
playerNickName["player1"] = "player1"; 
playerTimes["player1"] = 12100; 
playerNickName["player2"] = "player2"; 
playerTimes["player2"] = 12200; 
playerNickName["player3"] = "player3"; 
playerTimes["player3"] = 12300; 
playerNickName["player4"] = "player4"; 
playerTimes["player4"] = 12400; 
playerNickName["player5"] = "player5"; 
playerTimes["player5"] = 12500; 
playerNickName["player6"] = "player6"; 
playerTimes["player6"] = 12600; 
playerNickName["player7"] = "player7"; 
playerTimes["player7"] = 12700; 
playerNickName["player8"] = "player8"; 
playerTimes["player8"] = 12800; 
playerNickName["player9"] = "player9"; 
playerTimes["player9"] = 12900; 

playerNickName["player10"] = "player10"; 
playerTimes["player10"] = 14000; 
playerNickName["player11"] = "player11"; 
playerTimes["player11"] = 13100; 
playerNickName["player12"] = "player12"; 
playerTimes["player12"] = 13200; 
playerNickName["player13"] = "player13"; 
playerTimes["player13"] = 13300; 
playerNickName["player14"] = "player14"; 
playerTimes["player14"] = 13400; 
playerNickName["player15"] = "player15"; 
playerTimes["player15"] = 13500; 
playerNickName["player16"] = "player16"; 
playerTimes["player16"] = 13600; 
playerNickName["player17"] = "player17"; 
playerTimes["player17"] = 13700; 
playerNickName["player18"] = "player18"; 
playerTimes["player18"] = 13800; 
playerNickName["player19"] = "player19"; 
playerTimes["player19"] = 13900; 

playerNickName["player20"] = "player20"; 
playerTimes["player20"] = 14000; 
playerNickName["player21"] = "player21"; 
playerTimes["player21"] = 14100; 
playerNickName["player22"] = "player22"; 
playerTimes["player22"] = 14200; 
playerNickName["player23"] = "player23"; 
playerTimes["player23"] = 14300; 
playerNickName["player24"] = "player24"; 
playerTimes["player24"] = 14400; 
playerNickName["player25"] = "player25"; 
playerTimes["player25"] = 14500; 
playerNickName["player26"] = "player26"; 
playerTimes["player26"] = 14600; 
playerNickName["player27"] = "player27"; 
playerTimes["player27"] = 14700; 
playerNickName["player28"] = "player28"; 
playerTimes["player28"] = 14800; 
playerNickName["player29"] = "player29"; 
playerTimes["player29"] = 14900; 

playerNickName["player30"] = "player30"; 
playerTimes["player30"] = 15000; 
playerNickName["player31"] = "player31"; 
playerTimes["player31"] = 15100; 
playerNickName["player32"] = "player32"; 
playerTimes["player32"] = 15200; 
playerNickName["player33"] = "player33"; 
playerTimes["player33"] = 15300; 
playerNickName["player34"] = "player34"; 
playerTimes["player34"] = 15400; 
playerNickName["player35"] = "player35"; 
playerTimes["player35"] = 15500; 
playerNickName["player36"] = "player36"; 
playerTimes["player36"] = 65600; 
playerNickName["player37"] = "player37"; 
playerTimes["player37"] = 65700; 
playerNickName["player38"] = "player38"; 
playerTimes["player38"] = 65800; 
playerNickName["player39"] = "player39"; 
playerTimes["player39"] = 65900; 

playerNickName["player40"] = "player40"; 
playerTimes["player40"] = 66000; 
playerNickName["player41"] = "player41"; 
playerTimes["player41"] = 66100; 
playerNickName["player42"] = "player42"; 
playerTimes["player42"] = 66200; 
playerNickName["player43"] = "player43"; 
playerTimes["player43"] = 66300; 
playerNickName["player44"] = "player44"; 
playerTimes["player44"] = 66400; 
playerNickName["player45"] = "player45"; 
playerTimes["player45"] = 66500; 
playerNickName["player46"] = "player46"; 
playerTimes["player46"] = 66600; 
playerNickName["player47"] = "player47"; 
playerTimes["player47"] = 66700; 
playerNickName["player48"] = "player48"; 
playerTimes["player48"] = 66800; 
playerNickName["player49"] = "player49"; 
*/
?>
