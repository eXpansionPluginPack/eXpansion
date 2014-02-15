
declare Integer totalCp = <?= $this->totalCp ?>;
declare Integer nbFields =  <?= $this->nbFields ?>;
declare Integer nbFirstFields =  <?= $this->nbFirstFields ?>;

//Ranks of the players
declare nbPlayersCp	= Integer[Integer];
declare playerTimes	= Integer[Text][Integer];
declare playerNickNames	= Text[Text][Integer];

//The latest chackpoint the player past
declare playersOnServer = Text[Text];
declare maxCp = -1;

declare Boolean needUpdate = True;
declare lastUpdateTime = 0;
