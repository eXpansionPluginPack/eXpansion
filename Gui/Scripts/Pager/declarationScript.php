declare CMlFrame Pager <=> (Page.GetFirstChild("Pager") as CMlFrame);
declare CMlQuad ScrollBar <=> (Page.GetFirstChild("ScrollBar") as CMlQuad);
declare CMlQuad ScrollBg <=> (Page.GetFirstChild("ScrollBg") as CMlQuad);

declare CMlQuad ScrollUp <=> (Page.GetFirstChild("ScrollUp") as CMlQuad);
declare CMlQuad ScrollDown <=> (Page.GetFirstChild("ScrollDown") as CMlQuad);

declare Real pagerMouseY;                    

declare Real ScrollBarHeight = ScrollBar.Size.Y;
declare Real ScrollBgHeight = ScrollBg.Size.Y;

declare Real pagerStartPos = ScrollBar.RelativePosition.Y;
declare Real pagerStopPosition = pagerStartPos - ScrollBgHeight + ScrollBarHeight;
declare maxDelta = pagerStartPos - pagerStopPosition;

declare Real pager_scrollYOriginPosition;                    
declare Real pager_mouseYPosition;                    
declare Real pager_deltaMouseYPosition;                    
declare Real pager_startMouseYPosition;                    
declare pager_moveScroll = False;
declare pager_firstClick = False;
declare Real itemSizeY = <?php echo $this->getNumber($this->sizeY); ?>;
declare Real pagerSizeY = <?php echo $this->getNumber($this->pagerSizeY); ?>;
declare Real oldPercent = -1.0;
declare Real percent = 0.0;

declare Real disabledOpacity = 0.2;

declare CMlFrame item;
declare Real nb = 1.0;
declare Real itemsPerPage = -1.0;



foreach (item in Pager.Controls) {
    item.RelativePosition.Y = -itemSizeY * nb;                    
	if(item.RelativePosition.Y < -pagerSizeY) { 
	   item.Hide();           
	   if (itemsPerPage == -1) {
		itemsPerPage = nb - 1;
	   }
	}
    nb +=1;
}


if (itemsPerPage == -1) {
    ScrollBar.Hide();
    ScrollUp.Opacity = disabledOpacity;
    ScrollDown.Opacity = disabledOpacity;
    ScrollBg.Opacity = disabledOpacity;
    //ScrollBg.Hide();
}
