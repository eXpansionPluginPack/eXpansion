<?php

    function getNumber($number) {
        return number_format((float) $number, 2, '.', '');
    }
    
    $deltaX = "DeltaPos.X = MouseX - lastMouseX;";
    $deltaY = "DeltaPos.Y = MouseY - lastMouseY;";

    if ($this->axisDisabled == "x")
        $deltaX = "";
    if ($this->axisDisabled == "y")
        $deltaY = "";

?><script><!--
#Include "MathLib" as MathLib

//Function definitions
<?= $this->scriptLib ?>

main () {
    declare Window <=> Page.GetFirstChild("<?= $win->getId() ?>");
    declare MoveWindow = False;
    declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);
    declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
    declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
    declare Real lastMouseX = 0.0;
    declare Real lastMouseY =0.0;

    declare persistent Boolean exp_enableHudMove = False;
    declare persistent Vec3[Text] windowLastPos;
    declare persistent Vec3[Text] windowLastPosRel;			
    declare persistent Boolean[Text] widgetVisible;

    declare Text id = "<?= $this->name ?>";

     // external declares
     <?= $this->dDeclares ?>
     // external declares ends

    if (!widgetVisible.existskey(id)) {
        widgetVisible[id] =  True;
    }
     if (!windowLastPos.existskey(id)) {
        windowLastPos[id] = < <?= getNumber($win->getPosX()) ?>, <?= getNumber($win->getPosY()) ?>, 0.0>;
    }
    if (!windowLastPosRel.existskey(id)) {
        windowLastPosRel[id] = < <?= getNumber($win->getPosX()) ?>, <?= getNumber($win->getPosY()) ?>, 0.0>;
    }

    Window.PosnX = windowLastPos[id][0];
    Window.PosnY = windowLastPos[id][1];
    LastDelta = windowLastPosRel[id];
    Window.RelativePosition = windowLastPosRel[id];

     while(True) {
        yield;

        // external loop stuff
        <?=  $this->wLoop ?>
        // external loop ends

        if (!widgetVisible.existskey(id)) {
            widgetVisible[id] =  True;
        }
        if (widgetVisible[id] == True) {
            Window.Show();
        }else {
            Window.Hide();
        }

        if (exp_enableHudMove == True) {
            quad.Show();
        }else {
            quad.Hide();
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

        if (MoveWindow) {
            <?= $deltaX ?>
            <?= $deltaY ?>
            LastDelta += DeltaPos;
            LastDelta.Z = 3.0;
            Window.RelativePosition = LastDelta;
            windowLastPos[id] = Window.AbsolutePosition;
            windowLastPosRel[id] = Window.RelativePosition;

            lastMouseX = MouseX;
            lastMouseY = MouseY;
       }
    }
}
--></script>