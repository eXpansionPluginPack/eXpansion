
declare Integer totalCp = <?php echo $this->totalCp ?>;
declare cpTimes = <?php echo $this->cpTimes ?>;
declare playerCheckPoint = Integer[Text];
declare Boolean needUpdate = True;
declare Integer maxCpIndex = <?php echo $this->maxCpIndex ?>;

//Putting Checkpoint count to zero
foreach (Player in Players) {
	playerCheckPoint[Player.Login] = -1;
}
