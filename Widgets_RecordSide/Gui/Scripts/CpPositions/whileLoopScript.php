
foreach (Player in Players) {

    declare <?= $this->varName ?> for Player = -1;

    if (<?= $this->varName ?> != Player.CurRace.Checkpoints.count) {	
	
	//Update the current checkpoint of this user
	declare curCp = Player.CurRace.Checkpoints.count -1 ;	
	<?= $this->varName ?> = curCp+1;
	
	
	//Check if valid checkpoint
	if(curCp >= 0){
	    needUpdate = True;
	    //Check if max Checkpoint
	    if(maxCp <= curCp){
		maxCp = curCp+1;
	    }

	    declare <?= $this->varName ?>_cpPosition for Player = -1;
	    declare newCpPosition = 0;
	    
	    
	    
	    
	    //Register Checkpoint time
	    if(!playerTimes.existskey(curCp)){
		//Is it first player throught this checkpoint?
		nbPlayersCp[curCp] = 0;
		playerTimes[curCp] = Integer[Text];
		playerNickNames[curCp] = Text[Text];
	    }
	    playerTimes[curCp][Player.Login] = Player.CurRace.Checkpoints[curCp];
	    playerNickNames[curCp][Player.Login] = Player.Name;
	    
	    nbPlayersCp[curCp] += 1;
	    
	    //Remove from older checkpoint
	    if(curCp > 0){
		playerTimes[curCp-1].removekey(Player.Login);
		playerNickNames[curCp-1].removekey(Player.Login);
		nbPlayersCp[curCp-1] -= 1;
	    }
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
    
    declare cpIndex = maxCp -1;
    while(cpIndex >= 0){
	declare Players2 = playerTimes[cpIndex];
	foreach(p => Score in Players2){
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
	cpIndex -= 1;
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
    cpIndex = maxCp -1;
    while(cpIndex >= 0){
	declare bestCp = 0;
	declare Players2 = playerTimes[cpIndex];
	foreach(p => Score in Players2){
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
		
		if((maxCp - cpIndex - 1) > 0){
		    nickLabel.SetText((maxCp - cpIndex - 1)^"Cp "^playerNickNames[cpIndex][p]);
		}else{
		     nickLabel.SetText(playerNickNames[cpIndex][p]);
		}
		
		if(nbRec == 1){
		    timeLabel.SetText(TimeToText(Score));
		}else if(firstOfCp){
		    timeLabel.SetText(TimeToText(Score));
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
	cpIndex -= 1;
    }
}