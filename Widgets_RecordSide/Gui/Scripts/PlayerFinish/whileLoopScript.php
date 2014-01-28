foreach (Player in Players) {
	//If first checkpoint time or new checkpoint tile
	if (!playerCheckPoint.existskey(Player.Login)){
		playerCheckPoint[Player.Login] = -1;
	}
	if(playerCheckPoint[Player.Login] != Player.CurRace.Checkpoints.count) {
		
		//Update the current checkpoint of this user
		playerCheckPoint[Player.Login] = Player.CurRace.Checkpoints.count;
		curCp = Player.CurRace.Checkpoints.count;
		
		//If finish
		if (curCp > 0 && curCp == totalCp) {
			
			//If first finish or better time		
			if(!playerTimes.existskey(Player.Login)){
				playerTimes[Player.Login] = -1;
			}
			
			if(playerTimes[Player.Login] == -1 || playerTimes[Player.Login] > Player.CurRace.Checkpoints[curCp-1]){
				playerTimes[Player.Login] = Player.CurRace.Checkpoints[curCp-1];
				needUpdate = True;
			}else{
				log("Worse Time, no update");
			}
		
		} else {
			
		}
	}
}

if(needUpdate){	
	playerTimes = playerTimes.sort();
	log(playerTimes);
	needUpdate = False;
	
	declare i = 1;
	
	foreach (Login => Score in playerTimes) {
        
		declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
		declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
		
		if(nickLabel != Null){
			nickLabel.SetText(Login);
			timeLabel.SetText(TimeToText(Score));
		}		
		i += 1;
		if(i >= nbFields)
			break;
	}
}