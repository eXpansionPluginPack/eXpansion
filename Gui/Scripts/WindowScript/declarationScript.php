<?php

$startPosX = (-1 * intval($win->getSizeX() / 2)) . ".0";
$startPosY = intval($win->getSizeY() / 2) . ".0";

?>


<script><!--
#Include "MathLib" as MathLib

//Function definitions
<?= $this->scriptLib ?>

//Main Function
main () {
    declare Window <=> Page.GetFirstChild("<?= $win->getId() ?>");
    declare CMlLabel TitlebarText <=> (Page.GetFirstChild("TitlebarText") as CMlLabel);
    declare showCoords = <?= $this->showCoords ?>;

    declare MoveWindow = False;
    declare Scroll = False;
    declare CloseWindow = False;
    declare isMinimized = False;
    declare Real CloseCounter = 1.0;
    declare Real OpenCounter = 0.0;
    declare CenterWindow = False;

    declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
    declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
    declare Real lastMouseX = 0.0;
    declare Real lastMouseY =0.0;
    declare active = False;
    declare Text id = "<?= $this->name ?>";
    declare persistent Vec3[Text] windowLastPos;
    declare persistent Vec3[Text] windowLastPosRel;
    declare persistent Text windowActive = "";

    //Declarations by containers included in this window
    <?= $this->dDeclares ?>

    if (!windowLastPos.existskey(id)) {
        windowLastPos[id] = < <?= $startPosX ?>, <?=  $startPosY ?>, 0.0>;
    }
    if (!windowLastPosRel.existskey(id)) {
        windowLastPosRel[id] = < <?= $startPosX ?>, <?=  $startPosY ?>, 0.0>;
    }
    Window.PosnX = windowLastPos[id][0];
    Window.PosnY = windowLastPos[id][1];
    LastDelta = windowLastPosRel[id];
    Window.RelativePosition = windowLastPosRel[id];
    windowActive = id;

    while(True) {
         <?= $this->wLoop ?>

        if (windowActive == id) {
            declare temp = Window.RelativePosition;
            temp.Z = 20.0;
            Window.RelativePosition = temp;
         }else {
            declare temp = Window.RelativePosition;
            temp.Z = -50.0;
            Window.RelativePosition = temp;				
        }

        if (showCoords) {
            declare coords = "$fffX:" ^ (MouseX - Window.PosnX) ^ " Y:" ^ (MouseY - Window.PosnY + 3 );
            TitlebarText.Value = coords;
        }


        if (MoveWindow) {
            DeltaPos.X = MouseX - lastMouseX;
            DeltaPos.Y = MouseY - lastMouseY;

            if (Window.PosnX < -140.0) {
                LastDelta.X = -140.0;	
            }
            if (Window.PosnX > 110.0) {
                LastDelta.X = 110.0;
            }
            if (Window.PosnY > 78.0) {
                LastDelta.Y = 78.0;
            }

            if (Window.PosnY < -80.0) {
                LastDelta.Y = -80.0;
            }

            LastDelta += DeltaPos;
            if (windowActive == id) {
               LastDelta.Z = 20.0;
            }
            Window.RelativePosition = LastDelta;
            windowLastPos[id] = Window.AbsolutePosition;
            windowLastPosRel[id] = Window.RelativePosition;

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
                    windowActive = id;
                 }


                if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "Close") {
                    Window.Hide();
                }

                if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "MainWindow") {
                    isMinimized = False;
                    windowActive = id;
                }
            }
         } else {
            MoveWindow = False;
         }
        yield;
    }


}  //end of window
--></script>