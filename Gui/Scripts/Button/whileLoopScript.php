foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {
        log("Mouse Over : "^Event.ControlId);
        if(Page.GetFirstChild("Desc_"^Event.ControlId) != Null){
            log("Validated "^Event.ControlId);
            if(currentButton != Null){
                currentButton.Hide();
            }
            currentButton = (Page.GetFirstChild("Desc_"^Event.ControlId) as CMlFrame);
            currentButton.Show();
        }else{
             if(currentButton != Null){
                currentButton.Hide();
                currentButton = Null;
            }
        }
    }
}