

Void putRecordTo(Integer i, Integer Rank, Integer Score, Text Login, Text nick, Boolean mine){

	declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
	declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
	declare highliteQuad = (Page.GetFirstChild("RecBg_"^i) as CMlQuad);
	
	if(nickLabel != Null){
		nickLabel.SetText(nick);
		timeLabel.SetText(TimeToText(Score));

		declare rank = (Page.GetFirstChild("RecRank_"^i) as CMlLabel);
		rank.SetText(""^Rank);

		declare bg = (Page.GetFirstChild("RecBgBlink_"^i) as CMlQuad);

		if(mine){
			highliteQuad.Hide();    
			bg.Show();
		}else{
			bg.Hide();
		}
	}
}