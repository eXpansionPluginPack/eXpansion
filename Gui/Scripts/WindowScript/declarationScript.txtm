<?php

$startPosX = (-1 * intval($win->getSizeX() / 2)) . ".0";
$startPosY = intval($win->getSizeY() / 2) . ".0";

?>


<script><!--
#Include "MathLib" as MathLib
#Include "TextLib" as TextLib

//Function definitions
<?php echo $this->scriptLib ?>

//Main Function
main () {
    declare Real closeCounter = 1.0;
    declare Real openCounter = 1.0;
    declare Window <=> Page.GetFirstChild("<?php echo $win->getId() ?>");   
    Window.RelativeScale = openCounter;
    
    declare windowFrame <=> Page.GetFirstChild("windowFrame");
    declare CMlLabel TitlebarText <=> (Page.GetFirstChild("TitlebarText") as CMlLabel);    

    declare MoveWindow = False;
    declare Scroll = False;
    declare closeWindow = False;
    declare Text closeAction = "<?php echo $this->closeAction; ?>";
    declare openWindow = False;
    declare isMinimized = False;
   
    declare CenterWindow = False;

    declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
    declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
    declare Real lastMouseX = 0.0;
    declare Real lastMouseY = 0.0;
    declare active = False;
    declare Text id = "<?php echo $this->name ?>";
    declare Boolean forceReset = <?php echo $this->forceReset; ?>;
    declare Text version = "<?php echo $this->version; ?>";
    declare Boolean disableAnimations = <?php echo $this->disableAnimations; ?>;
    
    if (disableAnimations) {
	openWindow = False;
	Window.RelativeScale = 1.;
    }
    declare persistent Vec3[Text][Text] exp_windowLastPos;
    declare persistent Vec3[Text][Text] exp_windowLastPosRel;
    declare persistent Text[Text] exp_windowActive;

    //Declarations by containers included in this window
    <?php echo $this->dDeclares ?>
    if (!exp_windowActive.existskey(version)) {
        exp_windowActive[version] = Text;    
    }
    
    if (!exp_windowLastPos.existskey(version)) {
        exp_windowLastPos[version] = Vec3[Text];
    }
    if (!exp_windowLastPos[version].existskey(id) || forceReset) {
        exp_windowLastPos[version][id] = < <?php echo $startPosX ?>, <?php echo  $startPosY ?>, 0.0>;
    }
    if (!exp_windowLastPosRel.existskey(version)) {
         exp_windowLastPosRel[version] = Vec3[Text];
    }
    if ( !exp_windowLastPosRel[version].existskey(id) || forceReset) {
        exp_windowLastPosRel[version][id] = < <?php echo $startPosX ?>, <?php echo  $startPosY ?>, 0.0>;
    }
    Window.RelativePosition.X = exp_windowLastPos[version][id][0];
    Window.RelativePosition.Y = exp_windowLastPos[version][id][1];
    LastDelta = exp_windowLastPosRel[version][id];
    Window.RelativePosition = exp_windowLastPosRel[version][id];
    
    exp_windowActive[version] = id;

    while(True) {
	yield;
        <?php echo $this->wLoop ?>

        if (exp_windowActive[version] == id) {
            declare temp = Window.RelativePosition;
            temp.Z = 20.0;
            Window.RelativePosition = temp;
         }else {
            declare temp = Window.RelativePosition;
            temp.Z = -50.0;
            Window.RelativePosition = temp;				
        }
	
	if (openWindow) {
	    openCounter += 0.2;
	    	    	    
	    if (openCounter > 1)
	    {
		openWindow = False;
		Window.RelativeScale = 1.;
	    } else {
	    	Window.RelativeScale = openCounter;
	    }
	}
	
	if (closeWindow) {
	    closeCounter -= 0.2;
	    if (disableAnimations) {
	    	TriggerPageAction(closeAction);
		continue;
	    }
	    if (closeCounter <= 0)
	    {
		closeWindow = False;	    	    
		Window.RelativeScale = 0.;
		TriggerPageAction(closeAction);
		continue;
	    }
	    else {
	    Window.RelativeScale = closeCounter;
	}
	}
	
        if (MoveWindow) {
            DeltaPos.X = MouseX - lastMouseX;
            DeltaPos.Y = MouseY - lastMouseY;

            if (Window.RelativePosition.X < -140.0) {
                LastDelta.X = -140.0;	
            }
            if (Window.RelativePosition.X > 110.0) {
                LastDelta.X = 110.0;
            }
            if (Window.RelativePosition.Y > 78.0) {
                LastDelta.Y = 78.0;
            }

            if (Window.RelativePosition.Y < -80.0) {
                LastDelta.Y = -80.0;
            }

            LastDelta += DeltaPos;
            if (exp_windowActive[version] == id) {
               LastDelta.Z = 20.0;
            }
            Window.RelativePosition = LastDelta;
            exp_windowLastPos[version][id] = Window.AbsolutePosition;
            exp_windowLastPosRel[version][id] = Window.RelativePosition;

            lastMouseX = MouseX;
            lastMouseY = MouseY;
            yield;
        }




        if (MouseLeftButton == True) {
            foreach (Event in PendingEvents) {
                if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "Titlebar")  {
                    lastMouseX = MouseX;
                    lastMouseY = MouseY;
                    MoveWindow = True;
                    exp_windowActive[version] = id;
                 }


                if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "Close") {
                    closeWindow = True;
                }

                if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "MainWindow") {
                    isMinimized = False;
                    exp_windowActive[version] = id;
                }
            }
         } else {
            MoveWindow = False;
         } 
    }


}  //end of window
--></script>