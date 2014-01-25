<?php
	if(isset($this->specilaCase))
		$smaller = '>';
	else
		$smaller = '>=';
?>

if (isMinimized)
{
     if (mainWindow.PosnX <?= $smaller ?> positionMin) {                                          
          mainWindow.PosnX -= 4;                                          
    }
}else if (!isMinimized){

	if (Now-lastAction > autoCloseTimeout) {
		isMinimized = True;
	}

	if ( mainWindow.PosnX <= positionMax) {                                                      
			  mainWindow.PosnX += 4;
	}                                                                                                                                             
    
}

foreach (Event in PendingEvents) {                                                
    if (Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
		if(lastAction != Now)
           isMinimized = !isMinimized;    
        lastAction = Now;                                           
    }                                       
}