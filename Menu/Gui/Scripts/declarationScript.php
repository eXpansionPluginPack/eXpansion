declare CMlFrame Menu <=> (Page.GetFirstChild("Submenu") as CMlFrame);   
declare CMlEntry widgetStatus <=> (Page.GetFirstChild("widgetStatus") as CMlEntry);
declare Text outText = "";
declare Boolean toggleSubmenu = False;
declare CMlFrame currentButton = Null; 
declare CMlFrame previousButton = Null; 
declare persistent Boolean[Text][Text] eXp_widgetVisible;    
declare Text version = "<?php echo $this->version ?>";
declare Boolean eXp_mouseRightState = False;
declare eXp_mouseRightTime = 0;
declare Boolean tabKey = False;

for(i, 1, <?php echo $this->count ?>) {
    Page.GetFirstChild("submenu_"^i).Hide();
}

Menu.RelativePosition.Z = 30.0;     


