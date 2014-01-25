    if (moveScroll) {                                                                                                    
            pagerDelta += MouseY - pagerMouseY;

            declare max = (-itemSizeY * rowsPerPage) + 13 ;

            if (pagerDelta >= 0.0) {
                            pagerDelta = 0.0;
                            pagerMouseY = MouseY;
            }
            if (pagerDelta < max) {
                            pagerDelta = max;
                            pagerMouseY = MouseY;        
            }

            ScrollBar.RelativePosition.Y = pagerDelta;            
            
            declare percent = 1 - (MathLib::Abs(max) -  MathLib::Abs(pagerDelta)) / MathLib::Abs(max);      
            declare test = MathLib::NearestInteger(percent * (desc.count - 1 - columnNumber));
            declare index = (test / itemsPerRow) * itemsPerRow;
            log(index);
            nb = 0;                    
            for(r, 0, itemsPerRow) {                 
                for(i, 0, rowsPerPage) {                   
                    declare CMlLabel item <=> (Page.GetFirstChild("column_"^nb) as CMlLabel);
                    
                    if (item != Null) {     
                           if (desc.count >  index+nb) {
                              item.SetText(desc[index+nb]);  
                              
                              if (!tempData.existskey(nb)) {
                                tempData.add(data[index+nb]);
                              }
                              else  {
                              tempData[nb] = data[index+nb];
                              }
                           }
                    }
                    nb += 1;
              }  
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
    
       foreach (Event in PendingEvents) {
            if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {                    
                    if (Event.Control.HasClass("label")) {
                    //declare CMlLabel item <=> (Event.Control as CMlLabel);                    
                    //entry.Value = data[item.Value];
                    declare id = TextLib::Split("_", Event.ControlId);
                    declare Integer index = TextLib::ToInteger(id[1]);   
                    if (tempData.existskey(index)) {
                    log(" " ^ tempData[index]);
                    entry.Value = " " ^ tempData[index];
                    }
                 }
                 else {
                    entry.Value = "";
                 }
            }
        } 