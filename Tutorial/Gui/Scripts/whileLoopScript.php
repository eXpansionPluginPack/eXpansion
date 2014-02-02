foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "CloseNotAgain")  {
	    log("not again!");
	    exp_widgetVisible[version][id] = False;
    }		
}
	