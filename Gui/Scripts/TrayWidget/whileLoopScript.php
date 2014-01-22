if (isMinimized)
{
     if (mainWindow.PosnX >= positionMin) {                                          
          mainWindow.PosnX -= 4;                                          
    }
}

if (!isMinimized)
{         
    if (Now-lastAction > autoCloseTimeout) {                                          
        if (mainWindow.PosnX <= positionMin) {                                                 
                mainWindow.PosnX -= 4;                                      
        } 
        if (mainWindow.PosnX >= positionMin)  {
                isMinimized = True;
        }
    }

    else {
        if ( mainWindow.PosnX <= positionMax) {                                                      
                  mainWindow.PosnX += 4;
        }                                                                                                                                             
    }
}

foreach (Event in PendingEvents) {                                                
    if (Event.Type == CMlEvent::Type::MouseClick && ( Event.ControlId == "myWindow" || Event.ControlId == "minimizeButton" )) {
           isMinimized = !isMinimized;    
           lastAction = Now;                                           
    }                                       
}