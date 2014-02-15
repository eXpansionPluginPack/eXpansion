
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields =  <?= $this->nbFields ?>;
declare Integer nbFirstFields =  <?= $this->nbFirstFields ?>;

//Ranks of the players
declare nbPlayersCp	= Integer[Integer];
declare playerTimes	= Integer[Integer][Integer];
declare playerNickNames	= Text[Integer][Integer];

//The latest chackpoint the player past
declare playersOnServer = Text[Text];
declare maxCp = -1;

declare Boolean needUpdate = True;
declare lastUpdateTime = 0;
