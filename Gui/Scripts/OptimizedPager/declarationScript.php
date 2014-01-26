declare Real itemSizeY = 6.0;
declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);

declare CMlEntry entry <=> (Page.GetFirstChild("entry") as CMlEntry);


declare Real pagerMouseY;                    
declare moveScroll = False;

declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
declare Real pagerDelta = 0.0;

declare Text[] desc = <?= $this->items;  ?>;
declare Text[] data = <?= $this->data;  ?>;

declare Text[] tempData;

declare Integer rowsPerPage = 14;
declare Integer columnNumber = <?= $this->columnNumber;  ?>;
declare Integer itemsPerRow = <?= $this->itemsPerRow; ?>;
declare Integer itemNumber;

declare Integer nb = 0;


nb = 0;                    
for(r, 0, itemsPerRow) {                 
    for(i, 0, rowsPerPage) {                   
        declare CMlLabel item <=> (Page.GetFirstChild("column_"^nb) as CMlLabel);

        if (item != Null) {     
               if (desc.count >  nb) {
                  item.SetText(desc[nb]);  

                  if (!tempData.existskey(nb)) {
                    tempData.add(data[nb]);
                  }
                  else  {
                  tempData[nb] = data[nb];
                  }
        }     
      }        
       nb += 1; 
    }
}
  nb = 0;
