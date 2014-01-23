if (moveScroll) {                                                                                                    
	pagerDelta += MouseY - pagerMouseY;

	declare max = (-itemSizeY * itemsPerPage) + 13 ;

	if (pagerDelta >= 0.0) {
			pagerDelta = 0.0;
			pagerMouseY = MouseY;
	}
	if (pagerDelta < max) {
			pagerDelta = max;
			pagerMouseY = MouseY;        
	}

	ScrollBar.RelativePosition.Y = pagerDelta;            
	declare Real percent = 1 - (MathLib::Abs(max) - MathLib::Abs(pagerDelta)) / MathLib::Abs(max);               
	nb = 1.0;                    
	foreach (item in Pager.Controls) {
		item.RelativePosition.Y = (-itemSizeY * nb) - percent * (-itemSizeY * (Pager.Controls.count - itemsPerPage));
		if(item.RelativePosition.Y > -3.0 || item.RelativePosition.Y < -itemSizeY * itemsPerPage) { 
		  item.Hide();
		}
		else {
		  item.Show();
		} 
		nb +=1;
	}                                                  
		pagerMouseY = MouseY;                         
}





if (MouseLeftButton == True) {                   
	foreach (Event in PendingEvents) {
	   if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "ScrollBar")  {
			  pagerMouseY = MouseY;                                            
			  moveScroll = True;
	  }                                   
	}                                                                                                                                    
} else {
    moveScroll = False;
}
