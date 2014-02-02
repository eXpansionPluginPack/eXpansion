declare persistent Boolean[Text][Text] exp_widgetVisible;

if (!exp_widgetVisible.existskey(version) ) {
	exp_widgetVisible[version] = Boolean[Text];
}

if ( !exp_widgetVisible[version].existskey(id) || forceReset) {
	exp_widgetVisible[version][id] = True;
}

if (exp_widgetVisible[version][id] == False) {
    Window.Hide();
}
log("status:");
log(exp_widgetVisible[version][id]);