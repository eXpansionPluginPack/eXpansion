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


/*for(i, 0, columnNumber) {
        
        if (desc.count > i) {            
            declare test <=> Page.GetFirstChild("column_"^i) ;                                                                        
            if (test.HasClass("label")) {
            declare CMlLabel item <=> (Page.GetFirstChild("column_"^i) as CMlLabel);                                                                        
                item.SetText(desc[temp+i]);  
            }
        }
} */                                   


