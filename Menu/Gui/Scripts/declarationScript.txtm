
declare CMlFrame Menu <=> (Page.GetFirstChild("Submenu") as CMlFrame);   

Menu.Hide(); // reduce flicker on refresh

declare CMlEntry widgetStatus <=> (Page.GetFirstChild("widgetStatus") as CMlEntry);
declare Text outText = "";
declare Boolean toggleSubmenu = False;
declare CMlFrame currentButton = Null; 
declare CMlFrame previousButton = Null; 
declare persistent Boolean[Text][Text][Text] eXp_widgetVisible;    
declare Text version = "<?php echo $this->version ?>";
declare Text id = "<?php echo $this->name ?>";
declare Text gameMode = "<?php echo $this->gameMode; ?>";
declare Boolean forceReset = <?php echo $this->forceReset ?>;

declare Boolean eXp_mouseRightState = False;
declare eXp_mouseRightTime = 0;

declare Boolean eXp_mouseMiddleState = False;
declare eXp_mouseMiddleTime = 0;

declare Boolean tabKey = False;

declare Boolean exp_enableHudMove for UI = False;     
declare Boolean exp_needToCheckPersistentVars for UI = False;
		

if (!eXp_widgetVisible.existskey(version) ) {
	eXp_widgetVisible[version] = Boolean[Text][Text];
}

if ( !eXp_widgetVisible[version].existskey(id) || forceReset) {
	eXp_widgetVisible[version][id] = Boolean[Text];
}

if ( !eXp_widgetVisible[version][id].existskey(gameMode) ) {
	eXp_widgetVisible[version][id][gameMode] = True;
}

for(i, 1, <?php echo $this->count ?>) {
    Page.GetFirstChild("submenu_"^i).Hide();
}

Menu.RelativePosition.Z = 30.0;     


