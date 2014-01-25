

declare Window <=> Page.GetFirstChild("<?= $win->getId() ?>");
declare MoveWindow = False;
declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);
declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
declare Real lastMouseX = 0.0;
declare Real lastMouseY = 0.0;

declare persistent Boolean exp_enableHudMove = False;
declare persistent Vec3[Text][Text] exp_widgetLastPos;
declare persistent Vec3[Text][Text] exp_widgetLastPosRel;			
declare persistent Boolean[Text][Text] exp_widgetVisible;

declare Text version = "<?= $this->version ?>";
declare Text id = "<?= $this->name ?>";
declare Boolean forceReset = <?= $this->forceReset ?>;

if (!exp_widgetVisible.existskey(version) ) {
	exp_widgetVisible[version] = Boolean[Text];
}

if ( !exp_widgetVisible[version].existskey(id) || forceReset) {
	exp_widgetVisible[version][id] = True;
}
if (!exp_widgetLastPos.existskey(version)) {
	exp_widgetLastPos[version] = Vec3[Text];
}

if (!exp_widgetLastPos[version].existskey(id) || forceReset) {
	exp_widgetLastPos[version][id] = < <?= $this->getNumber($win->getPosX()) ?>, <?= $this->getNumber($win->getPosY()) ?>, 0.0>;
}

if (!exp_widgetLastPosRel.existskey(version)) {
	exp_widgetLastPosRel[version] = Vec3[Text];
}
if (!exp_widgetLastPosRel[version].existskey(id) || forceReset) {
	exp_widgetLastPosRel[version][id] = < <?= $this->getNumber($win->getPosX()) ?>, <?= $this->getNumber($win->getPosY()) ?>, 0.0>;
}

Window.PosnX = exp_widgetLastPos[version][id][0];
Window.PosnY = exp_widgetLastPos[version][id][1];
LastDelta = exp_widgetLastPosRel[version][id];
Window.RelativePosition = exp_widgetLastPosRel[version][id];
