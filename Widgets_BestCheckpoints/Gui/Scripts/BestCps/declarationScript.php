
declare Integer totalCp = <?= $this->totalCp ?>;
declare cpTimes = <?= $this->cpTimes ?>;
declare playerCheckPoint = Integer[Text];
declare Boolean needUpdate = True;

//Putting Checkpoint count to zero
foreach (Player in Players) {
	playerCheckPoint[Player.Login] = -1;
}
