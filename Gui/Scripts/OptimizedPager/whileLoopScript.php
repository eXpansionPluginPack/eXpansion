	if (moveScroll || nbEventsCounted == -1) {
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
		declare test = MathLib::NearestInteger(percent * (totalRows - 1 - rowsPerPage));
		//declare index = (test / itemsPerRow) * itemsPerRow;
		
		if( (nbEventsCounted == -1 || nbEventsCounted == 0) && currentIndex != test){		
			log("update"^pagerDelta);
			currentIndex = test;
			for(i, 0, rowsPerPage) {                   
                 for(r, 0, itemsPerRow-1) { 
					declare CMlLabel item = labels[i][r];

					if (item != Null) {     
						if (textData.count >  nb) {
							item.SetText(textData[i+test][r]);                                                          
						}
					}
				}  
			}                                                                               
		}
		
		pagerMouseY = MouseY;  
		
		if(nbEventsCounted == -1)
			nbEventsCounted == 0;
		else{
			declare Integer i = 3;
			// log(i);
			if(nbEventsCounted >= i)
				nbEventsCounted = 0;
			else
				nbEventsCounted = (nbEventsCounted+1) % i;
		}
	}else if(nbEventsCounted > 0){
		nbEventsCounted += 1;
	}
	
	

   foreach (Event in PendingEvents) {

		if (Event.Type == CMlEvent::Type::MouseClick && Event.ControlId == "ScrollBar")  {
			
			pagerDelta += MouseY - pagerMouseY;
			if(pagerDelta>4 || pagerDelta <-4){
				nbEventsCounted = 1;
			}
		
			pagerMouseY = MouseY;                                            
			moveScroll = True;
	   }                                   

   
		 if (Event.Type == CMlEvent::Type::MouseOver && Event.ControlId != "Unassigned")  {    
			 if (Event.Control.HasClass("eXpOptimizedPagerAction")) {
				declare id = TextLib::Split("_", Event.ControlId);
				declare Integer row = TextLib::ToInteger(id[1]) + currentIndex;   
				declare Integer col = TextLib::ToInteger(id[2]);   
				
				log("Test "^row^" "^col);
				
               if (data.existskey(row) && data[row].existskey(col)){
					log(" " ^ data[row][col]);
					entry.Value = " " ^ data[row][col];
				}
                                
			 }
			 else {
				entry.Value = "";
			 }
		}
	}
	
	if (MouseLeftButton == True) { 
		                                                                                                                               
	} else {
		moveScroll = False;
		
		if(nbEventsCounted > 0)
			nbEventsCounted = -1;
		else
			nbEventsCounted = 0;
	}