
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields =  <?= $this->nbFields ?>;
declare Integer nbFirstFields =  <?= $this->nbFirstFields ?>;

//Ranks of the players
declare playerTimes	= <?= $this->playerTimes ?>;
declare playerNickNames	= <?= $this->nickNames ?>;

//The latest chackpoint the player past
declare playersOnServer = Text[Text];
declare maxCp = <?= $this->maxCp ?>;

declare Boolean needUpdate = True;
declare lastUpdateTime = 0;
