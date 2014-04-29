declare persistent Boolean[Text][Text][Text] eXp_widgetVisible;
declare Text gamemode = "all";

if (!eXp_widgetVisible.existskey(version) ) {
    eXp_widgetVisible = Boolean[Text][Text][Text];
}

if ( !eXp_widgetVisible[version].existskey(id) ) {
    eXp_widgetVisible[version][id] = Boolean[Text];
}

if ( !eXp_widgetVisible[version][id].existskey(gamemode) || forceReset) {
	eXp_widgetVisible[version][id][gamemode] = True;
}

if (eXp_widgetVisible[version][id][gamemode] == False) {
    Window.Hide();
}

