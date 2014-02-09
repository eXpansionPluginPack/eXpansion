<?php       

    $deltaX = "DeltaPos.X = MouseX - lastMouseX;";
    $deltaY = "DeltaPos.Y = MouseY - lastMouseY;";

    if ($this->axisDisabled == "x")
        $deltaX = "";
    if ($this->axisDisabled == "y")
        $deltaY = "";

?>

if(InputPlayer == Null){
    yield;
    continue;
}

if((Now - eXp_lastWidgetCheck) > (500) ){
    
    
    eXp_lastWidgetCheck = Now;
    if (!exp_widgetVisible.existskey(version) ) {
	exp_widgetVisible[version] = Boolean[Text];
     }

     if (!exp_widgetVisible[version].existskey(id)) {
	    exp_widgetVisible[version][id] = True;
    }

    if (!exp_widgetLayers[version].existskey(id)) {
	exp_widgetLayers[version][id] = "normal";
    }

    if (exp_widgetVisible[version][id] == True && exp_widgetLayers[version][id] == activeLayer && exp_widgetCurrentVisible != exp_widgetVisible[version][id]) {
	Window.Show();
	exp_widgetVisibilityChanged = True;
	exp_widgetCurrentVisible = True;
    } else if(exp_widgetCurrentVisible != exp_widgetVisible[version][id] || exp_widgetLayers[version][id] != activeLayer) {
	Window.Hide();
	exp_widgetCurrentVisible = False;
    }


    if (exp_enableHudMove == True) {
	    exp_enableHudMoveBuffered = True;
	    quad.Show();
    }else {
	    exp_enableHudMoveBuffered = False;
	    quad.Hide();
    }
    exp_widgetLayersBuffered = exp_widgetLayers[version][id];
    exp_widgetVisibleBuffered = exp_widgetVisible[version][id];
}else if(PageIsVisible == False){
    yield;
    continue;
}


if (exp_enableHudMoveBuffered == True && MouseLeftButton == True) {
	foreach (Event in PendingEvents) {
		if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "enableMove")  {
			lastMouseX = MouseX;
			lastMouseY = MouseY;
			MoveWindow = True;
		}
		
	}
}else {
	MoveWindow = False;
}
foreach (Event in PendingEvents) {
    if (Event.Type == CMlEvent::Type::MouseOver ) {
	if(Event.Control != Null ){
	   element = Event.Control;
	   element.Scale = 1.3;	   
	}	
    }
    else {
	  if (element != Null){ 	   	    
	    element.Scale = 1.0;	  
	  }
    }
}		  

if (MoveWindow) {
	<?= $deltaX ?>
	<?= $deltaY ?>
	LastDelta += DeltaPos;
	LastDelta.Z = 3.0;
	Window.RelativePosition = LastDelta;
	exp_widgetLastPos[version][id] = Window.AbsolutePosition;
	exp_widgetLastPosRel[version][id] = Window.RelativePosition;

	lastMouseX = MouseX;
	lastMouseY = MouseY;
}