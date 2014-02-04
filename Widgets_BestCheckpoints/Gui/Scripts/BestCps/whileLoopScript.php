foreach (Player in Players) {

    //If first checkpoint time or new checkpoint time
	if (!playerCheckPoint.existskey(Player.Login)){
		playerCheckPoint[Player.Login] = -1;
	}
		
	
	if(playerCheckPoint[Player.Login] != Player.CurRace.Checkpoints.count) {
        
		//Update the current checkpoint of this user
		playerCheckPoint[Player.Login] = Player.CurRace.Checkpoints.count;
		declare curCp = (Player.CurRace.Checkpoints.count%(totalCp+1))-1;
		declare cpIndex = Player.CurRace.Checkpoints.count-1;
        
		//Check if better
        if(curCp >= 0 && (cpTimes[curCp] > Player.CurRace.Checkpoints[cpIndex] || cpTimes[curCp] == 0)){
            needUpdate = True;
            cpTimes[curCp] = Player.CurRace.Checkpoints[cpIndex];
            
            declare nickLabel = (Page.GetFirstChild("CpNick_"^curCp) as CMlLabel);
			declare timeLabel = (Page.GetFirstChild("CpTime"^curCp) as CMlLabel);
            
            
			if(nickLabel != Null){		
			    nickLabel.SetText(Player.Name);
			    timeLabel.SetText("$ff0" ^ (curCp + 1 ) ^ " $fff" ^ TimeToText(cpTimes[curCp]) );
			}
        }
	}
}
