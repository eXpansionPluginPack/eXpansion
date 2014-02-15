
foreach (Player in Players) {

    declare <?= $this->varName ?> for Player = -1;

    if (<?= $this->varName ?> != Player.CurRace.Checkpoints.count) {
	
	//Update the current checkpoint of this user
	<?= $this->varName ?> = Player.CurRace.Checkpoints.count;
	declare curCp = <?= $this->varName ?> -1 ;
	
	//Check if valid checkpoint
	if(curCp >= 0){
	    needUpdate = True;
	    //Check if max Checkpoint
	    if(maxCp <= curCp){
		maxCp = curCp+1;
	    }

	    declare <?= $this->varName ?>_cpPosition for Player = -1;
	    declare newCpPosition = 0;
	    
	    log("New CP Time : CpPosition = "^<?= $this->varName ?>_cpPosition^" CurCP = " ^ curCp);
	    //Register Checkpoint time
	    if(!playerTimes.existskey(curCp)){
		//Is it first player throught this checkpoint?
		newCpPosition = 0;
		playerTimes[curCp] = Integer[Integer];
		playerNickNames[curCp] = Text[Integer];
		playerTimes[curCp][0] = Player.CurRace.Checkpoints[curCp];
		playerNickNames[curCp][0] = Player.Name;
	    }else{
		newCpPosition = playerTimes[curCp].count;
		playerTimes[curCp][0] = Player.CurRace.Checkpoints[curCp];
		playerNickNames[curCp][0] = Player.Name;
	    }
	    
	    //Remove from older checkpoint
	    if(<?= $this->varName ?>_cpPosition >= 0 && curCp > 0){
		log("Remove Old : "^<?= $this->varName ?>_cpPosition);
		playerTimes[curCp-1].removekey(<?= $this->varName ?>_cpPosition);
	    }
	    <?= $this->varName ?>_cpPosition = newCpPosition;
	}
    }
}



foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "setLayer") {
	if (exp_widgetLayers[version][id] == "normal") {
	    exp_needToCheckPersistentVars = True;
	    exp_widgetLayers[version][id] = "scorestable";
	} else {
	    exp_needToCheckPersistentVars = True;
	    exp_widgetLayers[version][id] = "normal";
	}
    }
}


if(!needUpdate){
    lastUpdateTime = Now;
}
if (needUpdate && (((Now - lastUpdateTime) > 500 && exp_widgetVisibleBuffered && exp_widgetLayersBuffered == activeLayer) || exp_widgetVisibilityChanged)) {
    lastUpdateTime = Now;

    needUpdate = False;

    declare i = 1;
    declare nbRec = 1;
    declare showed = False;

    declare myRank = -1;
    declare start = -1;
    declare end = -1;
    declare recCount = -1;

    i = 1;

    foreach (cpIndex => Players in playerTimes) {
	foreach(p => Score in Players){
	    if (LocalUser != Null) {
		if (playerNickNames[cpIndex][p] == LocalUser.Name) {
		    myRank = i;
		    break;
		}
	    }
	    i += 1;
	    if(myRank != -1 && i > myRank + (nbFields - nbFirstFields)){
		break;
	    }
	}
	 if(myRank != -1 && i > myRank + (nbFields - nbFirstFields)){
	    break;
	}
    }
    recCount = i;

    if (myRank != -1) {
	start = myRank - ((nbFields - nbFirstFields) / 2);

	if (start <= nbFirstFields) {
	    start = nbFirstFields;
	    end = start + (nbFields - nbFirstFields);
	} else {
	    end = start + (nbFields - nbFirstFields);
	    if (end > recCount) {
		end = recCount;
		start = end - (nbFields - nbFirstFields);
	    }
	}
    } else {
	start = nbFirstFields;
	end = start + (nbFields - nbFirstFields);
    }

    i = 1;
    nbRec = 1;
    declare firstOfCp = True;
    foreach (cpIndex => Players in playerTimes) {
	declare bestCp = 0;
	foreach(p => Score in Players){
	    if(firstOfCp){
		bestCp = Score;
	    }
	    if ((nbRec <= nbFirstFields || (nbRec > start && nbRec <= end) ) && i <= nbFields) {

		declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
		declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
		declare highliteQuad = (Page.GetFirstChild("RecBg_"^i) as CMlQuad);

		/*if (highliteQuad != Null) {
		    if (playersOnServer.existskey(Login) && i != myRank) {
			highliteQuad.Show();
		    } else {
			highliteQuad.Hide();
		    }
		}*/
		
		if (playerNickNames[cpIndex][p] == LocalUser.Name) {
		    showed = True;
		}

		nickLabel.SetText(playerNickNames[cpIndex][p]);
		
		if(nbRec == 1){
		    timeLabel.SetText(TimeToText(Score));
		}else if(firstOfCp){
		    timeLabel.SetText("+"^(maxCp - cpIndex)^"Cp^");
		}else{
		    timeLabel.SetText("+"^TimeToText(Score - bestCp));
		}
		
		declare rank = (Page.GetFirstChild("RecRank_"^i) as CMlLabel);
		rank.SetText(""^i);

		declare bg = (Page.GetFirstChild("RecBgBlink_"^i) as CMlQuad);

		if(playerNickNames[cpIndex][p] == LocalUser.Name){
			highliteQuad.Hide();    
			bg.Show();
		}else{
			bg.Hide();
		}

		
		i += 1;
	    }
	    nbRec += 1;
	    firstOfCp = False;
	}
	firstOfCp = True;
    }
}