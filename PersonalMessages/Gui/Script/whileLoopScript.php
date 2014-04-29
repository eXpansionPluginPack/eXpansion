foreach (Event in PendingEvents) {
	 if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "sendButton")  {
	    TriggerPageAction(sendAction);
            inputBox.Value = "";
	    inputBox.StartEdition();	    
	}

	if (Event.Type == CMlEvent::Type::EntrySubmit)  {	   
            TriggerPageAction(sendAction);
            inputBox.Value = "";
	    inputBox.StartEdition();
	}	
	if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "minimizeButton")  {
	    pmStatus = isMinimized;
	}
}

