<?php
    if(isset($this->specilaCase))
	    $smaller = '>';
    else
	    $smaller = '>=';
?>

if (isMinimized)
{
    if (mainWindow.RelativePosition.X <?php echo $smaller ?> positionMin) {
	mainWindow.RelativePosition.X -= 4;
    }
}else if (!isMinimized){

    if (!disableAutoClose && Now-lastAction > autoCloseTimeout) {
	isMinimized = True;
    }

    if ( mainWindow.RelativePosition.X <= positionMax) {
	mainWindow.RelativePosition.X += 4;
    }                                                                                                                                             
    
}

foreach (Event in PendingEvents) {                                                
    if (Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
		if(lastAction != Now)
           isMinimized = !isMinimized;    
        lastAction = Now;                                           
    }                                       
}