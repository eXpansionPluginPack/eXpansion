declare Integer nbShow = 10;
declare Integer curCp = 0;
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields = 30;

//Ranks of the players
declare playerTimes = <?= $this->playerTimes ?>;
//The latest chackpoint the player past
declare playerCheckPoint = Integer[Text];

declare Boolean needUpdate = True;

//Putting Checkpoint count to zero
foreach (Player in Players) {
	playerCheckPoint[Player.Login] = -1;
}
