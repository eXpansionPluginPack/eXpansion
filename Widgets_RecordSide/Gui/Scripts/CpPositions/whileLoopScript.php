
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
		playerTimes[curCp] = Integer[Text];
		playerNickNames[curCp] = Text[Text];
	    }
	    playerTimes[curCp][Player.Login] = Player.CurRace.Checkpoints[curCp];
	    playerNickNames[curCp][Player.Login] = Player.Name;
	    
	    //Remove from older checkpoint
	    if(curCp > 0){
		if(playerTimes.existskey(curCp-1)){
		    playerTimes[curCp-1].removekey(Player.Login);
		    playerNickNames[curCp-1].removekey(Player.Login);
		}
	    }
	}
    }
}



foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "setLayer") {
	if (eXp_widgetLayers[version][id][gameMode] == "normal") {
	    exp_needToCheckPersistentVars = True;
	    eXp_widgetLayers[version][id][gameMode] = "scorestable";
	} else {
	    exp_needToCheckPersistentVars = True;
	    eXp_widgetLayers[version][id][gameMode] = "normal";
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
	if(playerTimes.existskey(cpIndex)){
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
	if(playerTimes.existskey(cpIndex)){
	    declare Players2 = playerTimes[cpIndex];
	    foreach(p => Score in Players2){
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
		    
		    timeLabel.SetText(TimeToText(Score));
		    
		    declare labelInfo1 = (Page.GetFirstChild("RecInfo1_"^i) as CMlLabel);
		    declare labelInfo2 = (Page.GetFirstChild("RecInfo2_"^i) as CMlLabel);
		    
		    if(nbRec == 1){
			labelInfo1.SetText(TimeToText(0));
			labelInfo2.SetText(TimeToText(0));
		    }else if(firstOfCp){
			labelInfo1.SetText("$00F"^TimeToText(0));
			labelInfo2.SetText("$00F"^TimeToText(0));
		    }else{
			declare diff = Score - bestCp;
			if(lastTimeDiff > diff && showed){
			    labelInfo1.SetText("$F00+"^TimeToText(Score - bestCp));
			    labelInfo2.SetText("$F00+"^TimeToText(Score - bestCp));
			}else if(lastTimeDiff < diff && showed){
			    labelInfo1.SetText("$0F0+"^TimeToText(Score - bestCp));
			    labelInfo2.SetText("$0F0+"^TimeToText(Score - bestCp));
			}else{
			    labelInfo1.SetText("+"^TimeToText(Score - bestCp));
			    labelInfo2.SetText("+"^TimeToText(Score - bestCp));
			}
			if(showed){
			    lastTimeDiff = diff;
			}
		    }

		    declare rank = (Page.GetFirstChild("RecRank_"^i) as CMlLabel);
		    rank.SetText(""^i);

		    declare labelCp1 = (Page.GetFirstChild("RecCp1_"^i) as CMlLabel);
		    declare labelCp2 = (Page.GetFirstChild("RecCp2_"^i) as CMlLabel);
		    if(nbRec == 1){
			 declare lap = 0;
			 lap = cpIndex/totalCp;
			 if(lap > 0){
			    labelCp1.SetText("Lap"^lap);
			    labelCp2.SetText("Lap"^lap);
			 }else{
			    labelCp1.SetText("Cp"^(cpIndex+1));
			    labelCp2.SetText("Cp"^(cpIndex+1));
			 }
		    }else{
			declare diff = maxCp - cpIndex - 1;
			if(diff > 0){
			    labelCp1.SetText("+"^diff^"Cp");
			    labelCp2.SetText("+"^diff^"Cp");
			}else{
			    labelCp1.SetText("");
			    labelCp2.SetText("");
			}
		    }	    
		    
		    declare bg = (Page.GetFirstChild("RecBgBlink_"^i) as CMlQuad);

		    if(playerNickNames[cpIndex][p] == LocalUser.Name){
			    highliteQuad.Hide();    
			    bg.Show();
		    }else{
			    bg.Hide();
		    }


		    i += 1;
		}
		bestCp = Score;
		nbRec += 1;
		firstOfCp = False;
	    }
	    firstOfCp = True;
	}
	cpIndex -= 1;
    }
}