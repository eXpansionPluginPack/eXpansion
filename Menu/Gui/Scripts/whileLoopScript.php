if(MouseRightButton && !eXp_mouseRightState) {        
	eXp_mouseRightTime = Now;
	eXp_mouseRightState = True;    
} else if(!MouseRightButton && eXp_mouseRightState) {
    eXp_mouseRightState = False;    
    
	if((Now - eXp_mouseRightTime) < 200) {
	    toggleSubmenu = True;
	    Menu.PosnX = MouseX-1;
	    Menu.PosnY = MouseY+.5;  
	    Menu.PosnZ = 40.0;
	}   
    
}
if (IsSpectatorMode) { 	
    toggleSubmenu = False;
}

if (toggleSubmenu) {
	Menu.Show();     
	outText = "";					   
	if (exp_widgetVisible[version].count > 0) {
		foreach (id => status in exp_widgetVisible[version]) {
			declare Text bool = "0";
			if (status == True) {
				bool = "1";
			}
			outText = outText ^ id ^ ":" ^ bool ^ "|";
		}
		widgetStatus.Value = outText;
	}

	foreach (Event in PendingEvents) {
		if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {
			if(Page.GetFirstChild("submenu_"^ TextLib::SubText(Event.ControlId,4,1)) != Null ) {                                                                                            
				if (currentButton != Null && currentButton.ControlId != "submenu_"^ TextLib::SubText(Event.ControlId,4,1)) {        
					log ("ControlId changed");
					currentButton.Hide();
				} 
				//log ("hovering: submenu_"^ TextLib::SubText(Event.ControlId,4,1));
				currentButton = (Page.GetFirstChild("submenu_"^ TextLib::SubText(Event.ControlId,4,1)) as CMlFrame);
				currentButton.Show();                                                                                                                                                              
			} else {                            
				if (currentButton != Null) {                
					//log ("hiding:" ^ currentButton.ControlId);
					currentButton.Hide();        
					currentButton = Null;
				}
			}                                                  
		} 
	}
}
else { 
	Menu.Hide();
}

if (MouseLeftButton) {                           
	toggleSubmenu = False;
}   
