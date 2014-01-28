 Text pad(Integer number, Integer pad) {
    declare Text out = "";
    out = "" ^ number;
    if (number < 10 && pad == 3) {
            out = "00" ^ number;
            }
    if (number < 10 && pad == 2) {
            out = "0" ^ number;
            }
    if (number < 100 && pad == 3) {
            out = "0" ^ number;
            }

    return out;
}


Text TimeToText(Integer intime) {
	declare time = MathLib::Abs(intime);
	declare Integer cent = time % 1000;                           
	time = time / 1000;
	declare Integer sec = time % 60;
	declare Integer min = time / 60;
	declare Text sign = "";
	if (intime < 0)  {
		sign = "-";
	}
	return sign ^ pad(min,2) ^ ":" ^ pad(sec,2) ^ "." ^ pad(cent,3);                                                         
}   


Void putRecordTo(Integer i, Integer Rank, Integer Score, Text Login,  Text nick, Boolean mine){

	declare nickLabel = (Page.GetFirstChild("RecNick_"^i) as CMlLabel);
	declare timeLabel = (Page.GetFirstChild("RecTime_"^i) as CMlLabel);
		
	if(nickLabel != Null){
		nickLabel.SetText(nick);
		timeLabel.SetText(TimeToText(Score));

		declare rank = (Page.GetFirstChild("RecRank_"^i) as CMlLabel);
		rank.SetText(""^Rank);

		declare bg = (Page.GetFirstChild("RecBg_"^i) as CMlQuad);

		if(mine){
			bg.Show();
		}else{
			bg.Hide();
		}
	}
}