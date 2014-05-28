foreach (Player in Players) {
    if (Player.Login == target) {
    
        declare PrevCheckpointCount for Player = -1;
        if (PrevCheckpointCount != Player.CurRace.Checkpoints.count) {
            PrevCheckpointCount = Player.CurRace.Checkpoints.count;
            curCp = Player.CurRace.Checkpoints.count;

            if (curCp > 0 && curCp != totalCp) {	    	    
		declare temp = (curCp % totalCp) - 1;	   
		if(temp == -1) {			
		    continue;
		}
			
                if (Checkpoints.count > (curCp % totalCp) && Checkpoints[temp] != -1) {
                    declare Integer diff = 0;
                    if (lapRace) {
                        declare Integer lastCpIndex = curCp - totalCp - 1;                        
			diff = (Player.CurLap.Checkpoints[temp] - Checkpoints[temp]);
                    }
                    else {
                        diff =(Player.CurRace.Checkpoints[curCp-1] - Checkpoints[temp]);
                    }
		    
                    declare Text color = "$f00$s";
                     if (diff <= 0) {
                        color = "$00f$s";
                    }
                    Label.SetText(color ^ TimeToText(diff));
                }else {                                               
                    Label.SetText("$fff$s" ^ TimeToText(Player.CurRace.Checkpoints[curCp-1]));
                }   
                if (lapRace) {
                    Cp.SetText("Lap:" ^ Player.CurrentNbLaps ^ " Cp:" ^ (curCp % totalCp) ^ "/" ^ totalCp);
                } else {
                    Cp.SetText((curCp % totalCp) ^ "/" ^ totalCp);
                }
            } else {
		if(curCp % totalCp == 0 && curCp > 0){
		    //End of a Lap or Race. 
		    declare temp = totalCp - 1;
		    declare Integer diff = -1;
		    log(Checkpoints);
		    if( Checkpoints[temp] > 0){
			diff =(Player.CurRace.Checkpoints[curCp-1] - Checkpoints[temp]);
		    }
                    
		    if(diff < 0){
			log("New Best TIme");
			//New Best time
			for(i, 0, totalCp-1) {
			    log(i);
			    Checkpoints[i] = Player.CurRace.Checkpoints[(curCp - totalCp) + i];
			}
		    }
		    
		}
                Label.SetText("");
                Cp.SetText("");
            }
        }
    }
}
