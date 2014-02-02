foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "CloseNotAgain")  {	    
	    exp_widgetVisible[version][id] = False;
	    Window.Hide();
    }		
}
	