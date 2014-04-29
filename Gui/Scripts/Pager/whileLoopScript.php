if (pager_moveScroll) {                                                                                                    
	if(pager_firstClick){
	    pager_scrollYOriginPosition = ScrollBar.RelativePosition.Y;
	}
	pager_firstClick = False;
        declare ypos = pager_scrollYOriginPosition + pager_deltaMouseYPosition;
    
	if(ypos >= pagerStartPos){
		ScrollBar.RelativePosition.Y = pagerStartPos;
        }else if(ypos < pagerStopPosition){
	    ScrollBar.RelativePosition.Y = pagerStopPosition;
        }else{
	    ScrollBar.RelativePosition.Y = pager_scrollYOriginPosition + pager_deltaMouseYPosition;
	}
	
	percent = 1 - (MathLib::Abs(maxDelta) - MathLib::Abs(ScrollBar.RelativePosition.Y)) / MathLib::Abs(maxDelta);               
}


if (percent != oldPercent) {

	oldPercent = percent;
	nb = 1.0;	
	foreach (item in Pager.Controls) {
		item.RelativePosition.Y = (-itemSizeY * nb) - percent * (-itemSizeY * (Pager.Controls.count - itemsPerPage));
		if(item.RelativePosition.Y > -(0.5*itemSizeY) || item.RelativePosition.Y < -pagerSizeY) { 
		  item.Hide();
		}
		else {
		  item.Show();
		} 
		nb +=1;
	} 
	if (itemsPerPage != -1.0) {
	    ScrollUp.Opacity = 1.0;
	    ScrollDown.Opacity = 1.0;
	
	    if (percent == 0) {
	        ScrollUp.Opacity = disabledOpacity;	
	    } 
	
	    if (percent == 1) {
		ScrollDown.Opacity = disabledOpacity;	
	    } 
	}
}

foreach (Event in PendingEvents) {
    if(Event.Type == CMlEvent::Type::MouseClick){
	if (Event.ControlId == "ScrollBar")  {
	    pager_startMouseYPosition = MouseY;
	    pager_firstClick = True;
	    pager_moveScroll = True;
	}
	else if (Event.ControlId == "ScrollDown")  {
		declare Real totalHeight = (Pager.Controls.count * itemSizeY);
		declare Real stepIncrement = itemSizeY * 5;		
		declare Real step = ((totalHeight/stepIncrement)*itemSizeY)/100;
		declare Real positionDelta = (maxDelta * step);
		declare Real newPercent = percent + step;
		if (itemsPerPage != -1.0) {
		    if (newPercent <= 1) {		
			percent = newPercent;
			ScrollBar.RelativePosition.Y -= positionDelta;
		    } else {
			percent = 1.;
			ScrollBar.RelativePosition.Y = pagerStopPosition;
		    }
		}
		
	} else if (Event.ControlId == "ScrollUp")  {
		declare Real totalHeight = (Pager.Controls.count * itemSizeY);
		declare Real stepIncrement = itemSizeY * 5;						
		declare Real step = ((totalHeight/stepIncrement)*itemSizeY)/100;		
		declare Real positionDelta = (maxDelta * step);		
		declare Real newPercent = percent - step;
		if (itemsPerPage != -1.0) {
		    if (newPercent >= 0 ) {		
		    	percent = newPercent;
			ScrollBar.RelativePosition.Y += positionDelta;
		    } else {
		    	percent = 0.;
			ScrollBar.RelativePosition.Y = pagerStartPos;
		    }
		
		}
	    }
    }
    
}              

if (MouseLeftButton == False) { 
    pager_firstClick = False;
    pager_moveScroll = False;
}else if(pager_moveScroll) {
    pager_deltaMouseYPosition = MouseY - pager_startMouseYPosition;
   // log("Moving : "^pager_deltaMouseYPosition);
}
