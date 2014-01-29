foreach (Player in Players) {
    if (Player.Login == GUIPlayer.Login) {
        // log(Player.RaceState);

        declare PrevCheckpointCount for Player = -1;
        if (PrevCheckpointCount != Player.CurRace.Checkpoints.count) {
			PrevCheckpointCount = Player.CurRace.Checkpoints.count;
			curCp = Player.CurRace.Checkpoints.count;

			if (curCp > 0 && curCp != totalCp) {
				if (Checkpoints.count > curCp && Checkpoints[curCp] != -1) {
					declare Integer diff = (Player.CurRace.Checkpoints[curCp-1] - Checkpoints[curCp-1]);
					//log(diff);
					declare Text color = "$f00$s";
					 if (diff <= 0) {
						color = "$00f$s";
					}
					Label.SetText(color ^ TimeToText(diff));
				}else {                                               
					Label.SetText("$fff$s" ^ TimeToText(Player.CurRace.Checkpoints[curCp-1]));
				}				
				Cp.SetText((curCp % totalCp) ^ "/" ^ totalCp);
			} else {
				Label.SetText("");
				Cp.SetText("");
			}
		}
    }
}
