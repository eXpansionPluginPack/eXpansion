//Putting Checkpoint count to zero
if (atStart == True) {
    foreach (Player in Players) {
	    playerCheckPoint[Player.Login] = -1;
	    if(!playerNickName.existskey(Player.Login)){
		    playerNickName[Player.Login] = Player.Name;
	    }
    }
atStart = False;
}

if(nbCount % 60 == 0) {
    nbCount = 0;        
    foreach (Player in Players) {			
		//If first checkpoint time or new checkpoint time
		if (!playerCheckPoint.existskey(Player.Login)){
			playerCheckPoint[Player.Login] = -1;
		}
		//First encounter get nicknamed
		if(!playerNickName.existskey(Player.Login)){
			playerNickName[Player.Login] = Player.Name;
		}

		if(playerCheckPoint[Player.Login] != Player.CurRace.Checkpoints.count) {			
			//Update the current checkpoint of this user
			playerCheckPoint[Player.Login] = Player.CurRace.Checkpoints.count;
			curCp = Player.CurRace.Checkpoints.count;

			//If finish
			if (curCp > 0 && curCp%(totalCp+1) == totalCp && totalCp > acceptMinCp) {
				log ("finish");
				declare Integer cpIndex = curCp%(totalCp+1) - 1;

				//If first finish or better time		
				if(!playerTimes.existskey(Player.Login)){
					origPlayerTimes.clear();				
					origPlayerTimes = playerTimes;
					playerTimes[Player.Login] = -1;
				}

				if(playerTimes[Player.Login] == -1 || playerTimes[Player.Login] > Player.CurRace.Checkpoints[curCp-1]) {
					
					if (playerTimes[Player.Login] != -1) {				    
						origPlayerTimes.clear();
						origPlayerTimes = playerTimes;
					}

					playerTimes[Player.Login] = Player.CurRace.Checkpoints[cpIndex];				
					recordLogin = Player.Login;
					needUpdate = True;
					break;
				}	
			} 
			
		} 		
		
	} 
}
	nbCount += 1;


foreach (Event in PendingEvents) {	
	if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "setLayer")  {
	    if (exp_widgetLayers[version][id] == "normal") {
            exp_widgetLayers[version][id] = "scorestable";
	    } else {
            exp_widgetLayers[version][id] = "normal"; 
	    }
	}
}

if(needUpdate) { 	
	needUpdate = False;
	declare Integer inRank = 1;	
	declare Boolean isNewRecord = False;		
	
	playerTimes = playerTimes.sort();	
	
	foreach (Login => Score in playerTimes) {
		if(Login == recordLogin) {
			if (inRank < maxServerRank ) {			    
			    if (useMaxPlayerRank) {
				if  (maxPlayerRank.existskey(Login)) {
					if (inRank < maxPlayerRank[Login] ) {						
						isNewRecord = True;						
					}
				}
			    }
			    else {			    				
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
	
	playersOnServer.clear();	
	foreach (Player in Players) {
	    playersOnServer[Player.Login] = Player.Name;
	}

	if(playerTimes.count > nbShow){
		recCount = nbShow;
	} else {
		recCount = playerTimes.count;
	}
	
	i = 1;

	foreach (Login => Score in playerTimes) {		
	    if (LocalUser != Null) {
			if(Login == LocalUser.Login){
				myRank = i;
				break;
			}
	    }
	    i += 1;
	}
	
	if(myRank != -1){
		start = myRank - ((nbFields - nbFirstFields)/2);
		
		if(start <= nbFirstFields){
			start = nbFirstFields;
			end = start + (nbFields - nbFirstFields);
		}else{
			end = start + (nbFields - nbFirstFields);
			if(end > recCount){
				end = recCount;
				start = end - (nbFields - nbFirstFields);
			}
		}
	}else{
		start = nbFirstFields;
		end = start + (nbFields - nbFirstFields);
	}
	
	i = 1;
	nbRec = 1;
	foreach (Login => Score in playerTimes) {
        
		if((nbRec <= nbFirstFields || (nbRec > start && nbRec <= end) ) && nbRec <= nbShow && i <= nbFields){
	
			declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
			declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
			declare highliteQuad = (Page.GetFirstChild("RecBg_"^i) as CMlQuad);
			
			if(highliteQuad != Null){			    
				if (playersOnServer.existskey(Login) && i != myRank) {
					highliteQuad.Show();				
				} else {
					highliteQuad.Hide();				
				}
			}
			
			if(nickLabel != Null){				    			    
			    putRecordTo(i, nbRec, Score, Login, playerNickName[Login], Login == LocalUser.Login);			
				if(Login == LocalUser.Login){
					showed = True;
				}
			}			
			i += 1;
		}		
		nbRec += 1;		
		
		if(nbRec > nbShow){
		
		}
	}
	recordLogin = "";
}