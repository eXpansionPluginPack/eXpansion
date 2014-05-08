

if((Now - lastCheckTime) > 50){

    lastCheckTime = Now;
    
    foreach(Score in Scores){
	declare <?php echo $this->varName ?> for Score.User = -1; 
	
	if((<?php echo $this->varName ?> == -1 || <?php echo $this->varName ?> > Score.BestLap.Time) && Score.BestLap.Time > 0){
	
	  //  log("Interesting Player: "^Score.User.Login);
	    
	    
	    //This player start's to be interesting. 
	    <?php echo $this->varName ?> = Score.BestLap.Time;

	    if (!playerTimes.existskey(Score.User.Login)) {
		origPlayerTimes.clear();
		origPlayerTimes = playerTimes;
		playerTimes[Score.User.Login] = -1;
		playerNickName[Score.User.Login] = Score.User.Name;
	    }
	    declare playerTime = playerTimes[Score.User.Login];
	    // log("Var Time: "^<?php echo $this->varName ?>^"New Time : "^Score.BestLap.Time^" Old Time : "^playerTime);
	    
	    if (playerTime == -1 || playerTime > Score.BestLap.Time) {
		// log("Better Time: "^Score.User.Login);
		if (playerTime != -1) {
		    origPlayerTimes.clear();
		    origPlayerTimes = playerTimes;
		}

		playerTimes[Score.User.Login] = Score.BestLap.Time;

		recordLogin = Score.User.Login;
		needUpdate = True;
	    }else{
		<?php echo $this->varName ?> = playerTime;
	    }
	    // log("");
	}
    }
}

/*
foreach (Player in Players) {

    declare <?php echo $this->varName ?> for Player = -1;

    if (<?php echo $this->varName ?> != Player.CurRace.Checkpoints.count) {
	//Update the current checkpoint of this user
	<?php echo $this->varName ?> = Player.CurRace.Checkpoints.count;
	curCp = Player.CurRace.Checkpoints.count;

	//If finish
	if (curCp > 0 && curCp % (totalCp) == 0 && totalCp > acceptMinCp) {
	    
	    declare Integer cpIndex = totalCp - 1;
	    declare Integer lastCpIndex = curCp - totalCp - 1;
	    declare time = 0;

	    if (lastCpIndex > 0) {
		time = Player.CurRace.Checkpoints[curCp - 1] - Player.CurRace.Checkpoints[lastCpIndex];
	    } else {
		time = Player.CurRace.Checkpoints[curCp - 1];
	    }
	    
	    //This player start's to be interesting. 
	    declare playerTime = -1;
	
	    if (!playerTimes.existskey(Player.Login)) {
		origPlayerTimes.clear();
		origPlayerTimes = playerTimes;
		playerTimes[Player.Login] = -1;
		playerNickName[Player.Login] = Player.Name;
	    }
	    playerTime = playerTimes[Player.Login];
	    

	    if (playerTime == -1 || playerTime > time) {
		log("Better Time: "^Player.Login);
		if (playerTime != -1) {
		    origPlayerTimes.clear();
		    origPlayerTimes = playerTimes;
		}

		playerTimes[Player.Login] = time;
		
		recordLogin = Player.Login;
		needUpdate = True;
	    }
	}
	//Work around for 0 CP tracks
	if(<?php echo $this->varName ?>  == 1){
	    <?php echo $this->varName ?> = -1;
	}
    }
}*/

if(!needUpdate){
    lastUpdateTime = Now;
}

if (needUpdate && (((Now - lastUpdateTime) > 500 && exp_widgetVisibleBuffered && exp_widgetLayersBuffered == activeLayer) || exp_widgetVisibilityChanged)) {
    lastUpdateTime = Now;

    needUpdate = False;
    declare Integer inRank = 1;
    declare Boolean isNewRecord = False;

    playerTimes = playerTimes.sort();

    foreach (Login => Score in playerTimes) {
	if (Login == recordLogin) {
	    if (inRank < maxServerRank) {
		if (useMaxPlayerRank) {
		    if (maxPlayerRank.existskey(Login)) {
			if (inRank < maxPlayerRank[Login]) {
			    isNewRecord = True;
			}
		    }
		} else {
		    isNewRecord = True;
		}
	    }
	}
	inRank += 1;
    }

    inRank = 1;

    if (!isNewRecord) {
	playerTimes.clear();
	playerTimes = origPlayerTimes.sort();
    }



    declare i = 1;
    declare nbRec = 1;
    declare showed = False;

    declare myRank = -1;
    declare start = -1;
    declare end = -1;
    declare recCount = -1;

    if (playerTimes.count > nbShow) {
	recCount = nbShow;
    } else {
	recCount = playerTimes.count;
    }

    i = 1;

    foreach (Login => Score in playerTimes) {
	if (LocalUser != Null) {
	    if (Login == LocalUser.Login) {
		myRank = i;
		break;
	    }
	}
	i += 1;
    }

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
	start = recCount  - (nbFields - nbFirstFields);
	end = start + (nbFields - nbFirstFields);
    }

    i = 1;
    nbRec = 1;
    foreach (Login => Score in playerTimes) {

	if ((nbRec <= nbFirstFields || (nbRec > start && nbRec <= end) ) && nbRec <= nbShow && i <= nbFields) {

	    declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
	    declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
	    declare highliteQuad = (Page.GetFirstChild("RecBg_"^i) as CMlQuad);

	    if (highliteQuad != Null) {
		if (playersOnServer.existskey(Login) && i != myRank) {
		    highliteQuad.Show();
		} else {
		    highliteQuad.Hide();
		}
	    }

	    if (nickLabel != Null) {
		putRecordTo(i, nbRec, Score, Login, playerNickName[Login], Login == LocalUser.Login);
		if (Login == LocalUser.Login) {
		    showed = True;
		}
	    }
	    i += 1;
	}
	nbRec += 1;

	if (nbRec > nbShow) {
	    
	}
    }
    recordLogin = "";
}

foreach (Event in PendingEvents) {
    /*if (Event.Type == CXmlRpcEvent::Type::LibXmlRpc_OnWayPoint) {
	
    }*/
    
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "setLayer") {
	if (eXp_widgetLayers[version][id][gameMode] == "normal") {	    
	    eXp_widgetLayers[version][id][gameMode] = "scorestable";	    
	    exp_needToCheckPersistentVars = True;
	} else {	   
	    eXp_widgetLayers[version][id][gameMode] = "normal";
	    exp_needToCheckPersistentVars = True;
	    
	}
    }
}