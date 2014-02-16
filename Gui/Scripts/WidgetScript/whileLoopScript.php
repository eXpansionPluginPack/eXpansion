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

//Check if persistent variables needs to be checked, or first loop
if(exp_needToCheckPersistentVars || !eXp_firstPersistentCheckDone){
    exp_multipleCheckCount += 1;
    eXp_firstPersistentCheckDone = True;

    if(exp_multipleCheckCount > 10){
	exp_needToCheckPersistentVars = False;
	exp_multipleCheckCount = 0;
    }

    if (!eXp_widgetVisible.existskey(version) ) {
	eXp_widgetVisible[version] = Boolean[Text][Text];
    }

    if ( !eXp_widgetVisible[version].existskey(id)) {
	eXp_widgetVisible[version][id] = Boolean[Text];
    }

    if ( !eXp_widgetVisible[version][id].existskey(gameMode) ) {
	eXp_widgetVisible[version][id][gameMode] = True;
    }

    if (!eXp_widgetLayers.existskey(version) ) {
	eXp_widgetLayers[version] = Text[Text][Text];
    }

    if (!eXp_widgetLayers[version].existskey(id)) { 
	eXp_widgetLayers[version][id] = Text[Text];
    }

    if (!eXp_widgetLayers[version][id].existskey(gameMode)) { 
	eXp_widgetLayers[version][id][gameMode] = "normal"; 
    }

    if (eXp_widgetVisible[version][id][gameMode] == True && eXp_widgetLayers[version][id][gameMode] == activeLayer && exp_widgetCurrentVisible != eXp_widgetVisible[version][id][gameMode]) {
	Window.Show();
	exp_widgetVisibilityChanged = True;
	exp_widgetCurrentVisible = True;
    } else if(exp_widgetCurrentVisible != eXp_widgetVisible[version][id][gameMode] || eXp_widgetLayers[version][id][gameMode] != activeLayer) {
	Window.Hide();
	exp_widgetCurrentVisible = False;
    }

    if (exp_enableHudMove == True) {
	    quad.Show();
    }else {
	    quad.Hide();
    }
    
    exp_widgetLayersBuffered = eXp_widgetLayers[version][id][gameMode];
    exp_widgetVisibleBuffered = eXp_widgetVisible[version][id][gameMode];
    
}else{
    exp_multipleCheckCount = 0;
}
    
if(PageIsVisible == False){
    yield;
    continue;
}

if (exp_enableHudMove == True && MouseLeftButton == True) {
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
	eXp_widgetLastPos[version][id][gameMode] = Window.AbsolutePosition;
	eXp_widgetLastPosRel[version][id][gameMode] = Window.RelativePosition;

	lastMouseX = MouseX;
	lastMouseY = MouseY;
}
