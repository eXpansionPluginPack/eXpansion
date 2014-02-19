declare Real itemSizeY = <?php echo $this->getNumber($this->sizeY) ?>;
declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);
declare Real itemCount = Pager.Size.Y / itemSizeY;
declare Integer itemsPerPage = MathLib::NearestInteger(itemCount) - 4;
declare Real pagerMouseY;                    
declare moveScroll = False;
declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
declare Real pagerDelta = 0.0;
declare CMlFrame item;
declare Real nb = 1.0;
foreach (item in Pager.Controls) {                        
item.RelativePosition.Y = -itemSizeY * nb;                    
    if(item.RelativePosition.Y < -itemSizeY * itemsPerPage) { 
       item.Hide();                            
    }
nb +=1;
}
if (Pager.Controls.count < itemsPerPage) {
    ScrollBar.Hide();
    ScrollBg.Hide();
}
