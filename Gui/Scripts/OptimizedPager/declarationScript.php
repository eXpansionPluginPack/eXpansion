declare Real itemSizeY = 6.0;
declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);

declare CMlEntry entry <=> (Page.GetFirstChild("entry") as CMlEntry);


declare Real pagerMouseY;                    
declare moveScroll = False;

declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
declare Real pagerDelta = 0.0;

declare Text[][Integer] textData = <?= $this->items;  ?>;
declare Text[][Integer] data = <?= $this->data;  ?>;
declare CMlLabel[Integer][Integer] labels;

declare Integer rowsPerPage = 14;
declare Integer itemsPerRow = <?= $this->itemsPerRow; ?>;
declare Integer totalRows = <?= $this->totalRows; ?>;

declare Integer itemNumber;

declare Integer currentIndex = 0;
declare Integer nbEventsCounted = 0;

declare Integer nb = 0;


nb = 0;                    
for(i, 0, rowsPerPage) {

	labels[i] = CMlLabel[Integer];

    for(r, 0, itemsPerRow-1) {
		declare CMlLabel item <=> (Page.GetFirstChild("column_"^i^"_"^r) as CMlLabel);
		labels[i][r] = item;
		
        if (item != Null && textData.existskey(i)) {                    
			item.SetText(textData[i][r]);    
		}        
    }
}

if (textData.count <= rowsPerPage) {
    ScrollBar.Hide();
    ScrollBg.Hide();    
}