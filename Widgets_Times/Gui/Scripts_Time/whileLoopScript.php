foreach (Player in Players) {
    if (Player.Login == GUIPlayer.Login) {
    
        declare PrevCheckpointCount for Player = -1;
        if (PrevCheckpointCount != Player.CurRace.Checkpoints.count) {
            PrevCheckpointCount = Player.CurRace.Checkpoints.count;
            curCp = Player.CurRace.Checkpoints.count;

            if (curCp > 0 && curCp != totalCp) {
                if (Checkpoints.count > curCp && Checkpoints[curCp] != -1) {
                    declare Integer diff = 0;
                    if (lapRace) {
                        declare Integer lastCpIndex = curCp - totalCp - 1;
                        if(lastCpIndex > 0) {
                             diff = (Player.CurRace.Checkpoints[curCp-1] - Player.CurRace.Checkpoints[lastCpIndex]) - Checkpoints[(curCp % (totalCp+1))-1];
                        } else {
                            diff =(Player.CurRace.Checkpoints[curCp-1] - Checkpoints[curCp-1]);
                        }                        
                    }
                    else {
                        diff =(Player.CurRace.Checkpoints[curCp-1] - Checkpoints[curCp-1]);
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
                Label.SetText("");
                Cp.SetText("");
            }
        }
    }
}
