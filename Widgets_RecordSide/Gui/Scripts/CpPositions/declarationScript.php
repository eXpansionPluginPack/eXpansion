
declare Integer totalCp = <?php echo $this->totalCp ?>;
declare Integer nbFields =  <?php echo $this->nbFields ?>;
declare Integer nbFirstFields =  <?php echo $this->nbFirstFields ?>;

//Ranks of the players
declare playerTimes	= <?php echo $this->playerTimes ?>;
declare playerNickNames	= <?php echo $this->nickNames ?>;

//The latest chackpoint the player past
declare playersOnServer = Text[Text];
declare playersTeam = <?= $this->playerTeams ?>;
declare maxCp = <?= $this->maxCp ?>;

declare Boolean needUpdate = True;
declare lastUpdateTime = 0;
declare lastTimeDiff = 0;

declare nbFinish = 0;


//Gui staff
declare Boolean givePoints = <?php echo $this->givePoints ?>;
declare points = <?php echo $this->points ?>;
declare isLaps = <?php echo $this->isLaps ?>;
declare nbLaps = <?php echo $this->nbLaps ?>;
declare isTeam = <?php echo $this->isTeam ?>;
