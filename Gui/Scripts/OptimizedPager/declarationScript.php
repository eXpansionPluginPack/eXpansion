declare Real itemSizeY = 6.0;
declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);

declare CMlEntry entry <=> (Page.GetFirstChild("entry") as CMlEntry);


declare Real pagerMouseY;                    
declare moveScroll = False;

declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
declare Real pagerDelta = 0.0;

declare Text[][Integer] desc = <?= $this->items;  ?>;
declare Text[][Integer] data = <?= $this->data;  ?>;

declare Text[] tempData;

declare Integer rowsPerPage = 14;
declare Integer columnNumber = <?= $this->columnNumber;  ?>;
declare Integer itemsPerRow = <?= $this->itemsPerRow; ?>;
declare Integer totalRows = <?= $this->totalRows; ?>;

declare Integer itemNumber;

declare Integer oldIndex = 0;
declare Integer nbEventsCounted = 0;

declare Integer nb = 0;


nb = 0;                    
for(i, 0, rowsPerPage) {                   
    for(r, 0, itemsPerRow-1) {                 
    declare CMlLabel item <=> (Page.GetFirstChild("column_"^nb) as CMlLabel);    
        if (item != Null) {                    
                  item.SetText(desc[i][r]);  
                   /* 
                   if (!tempData.existskey(nb)) {
                    tempData.add(data[nb]);
                  }
                  else  {
                  tempData[nb] = data[nb];
                  } 
                  */        
      }        
       nb += 1; 
    }
}
  nb = 0;
