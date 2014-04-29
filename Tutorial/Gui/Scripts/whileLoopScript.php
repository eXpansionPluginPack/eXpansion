foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "CloseNotAgain")  {	
	    if (!eXp_widgetVisible.existskey(version) ) {
		eXp_widgetVisible = Boolean[Text][Text][Text];
	    }

	    if ( !eXp_widgetVisible[version].existskey(id) ) {
		eXp_widgetVisible[version][id] = Boolean[Text];
	    }

	    if ( !eXp_widgetVisible[version][id].existskey(gamemode) || forceReset) {
		    eXp_widgetVisible[version][id][gamemode] = True;
	    }
	    eXp_widgetVisible[version][id][gamemode] = False;
	    Window.Hide();
    }		
}
	