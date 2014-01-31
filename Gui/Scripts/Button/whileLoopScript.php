foreach (Event in PendingEvents) {

    if (Event.Type == CMlEvent::Type::MouseOver)  {
        if(Event.ControlId != "Unassigned"){
            declare CMlQuad eXp_Button <=> (Page.GetFirstChild(Event.ControlId) as CMlQuad);

            if(Page.GetFirstChild("eXp_ButtonDescBg_"^Event.ControlId) != Null){

                if(eXp_ButtonCurrentBg != Null){
                    eXp_ButtonCurrentBg.Hide();
                }
                if(eXp_ButtonCurrentLb != Null){
                    eXp_ButtonCurrentLb.Hide();
                }

                eXp_ButtonCurrentBg = (Page.GetFirstChild("eXp_ButtonDescBg_"^Event.ControlId) as CMlQuad);
                eXp_ButtonCurrentLb = (Page.GetFirstChild("eXp_ButtonDescText_"^Event.ControlId) as CMlLabel);

                //Checking position
                if(eXp_Button.AbsolutePosition.X + eXp_ButtonCurrentBg.Size.X > 160){
                    //Left open
                    eXp_ButtonCurrentBg.PosnX = - eXp_ButtonCurrentBg.Size.X;
                    eXp_ButtonCurrentLb.PosnX = - eXp_ButtonCurrentBg.Size.X + 1;
                }else{
                    //Open right
                    eXp_ButtonCurrentBg.PosnX = 5.0;
                    eXp_ButtonCurrentLb.PosnX = 6.0;
                }

                eXp_ButtonCurrentBg.Show();
                eXp_ButtonCurrentLb.Show();

            }else{
                 if(eXp_ButtonCurrentBg != Null){
                    eXp_ButtonCurrentBg.Hide();
                    eXp_ButtonCurrentBg = Null;
                }
                if(eXp_ButtonCurrentLb != Null){
                    eXp_ButtonCurrentLb.Hide();
                    eXp_ButtonCurrentLb = Null;
                }
            }
        }
    }else{
         if(eXp_ButtonCurrentBg != Null){
                eXp_ButtonCurrentBg.Hide();
                eXp_ButtonCurrentBg = Null;
            }
	if(eXp_ButtonCurrentLb != Null){
                eXp_ButtonCurrentLb.Hide();
                eXp_ButtonCurrentLb = Null;
            }
    }
}