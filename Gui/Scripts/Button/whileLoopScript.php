foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {
        log("Mouse Over : "^Event.ControlId);
		
        if(Page.GetFirstChild("eXp_ButtonDescBg_"^Event.ControlId) != Null){
		
            log("Validated "^Event.ControlId);
            if(eXp_ButtonCurrentBg != Null){
                eXp_ButtonCurrentBg.Hide();
            }
			if(eXp_ButtonCurrentLb != Null){
                eXp_ButtonCurrentLb.Hide();
            }
			
            eXp_ButtonCurrentBg = (Page.GetFirstChild("eXp_ButtonDescBg_"^Event.ControlId) as CMlQuad);
            eXp_ButtonCurrentLb = (Page.GetFirstChild("eXp_ButtonDescText_"^Event.ControlId) as CMlLabel);
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
}