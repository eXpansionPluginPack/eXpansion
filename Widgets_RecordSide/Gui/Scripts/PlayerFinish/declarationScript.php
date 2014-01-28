declare Integer nbShow = 10;
declare Integer curCp = 0;
declare Integer totalCp = <?= $this->totalCp ?>;

//Ranks of the players
declare playerTimes = <?= $this->playerTimes ?>;
//The latest chackpoint the player past
declare playerCheckPoint = Integer[Text];

declare Boolean needUpdate = False;

//Putting Checkpoint count to zero
foreach (Player in Players) {
	playerCheckPoint[Player.Login] = -1;
}
