/**
 * @author      oliverde8 (oliverde8 at tm-teams.com)
 * @author      reaby (petri.jarvisalo at gmail.com)
 *
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007

 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */

declare Window <=> Page.GetFirstChild("<?= $win->getId() ?>");
declare MoveWindow = False;
declare CMlQuad  quad <=> (Page.GetFirstChild("enableMove") as CMlQuad);
declare Vec3 LastDelta = <Window.RelativePosition.X, Window.RelativePosition.Y, 0.0>;
declare Vec3 DeltaPos = <0.0, 0.0, 0.0>;
declare Real lastMouseX = 0.0;
declare Real lastMouseY = 0.0;
declare CMlControl element;

declare Boolean exp_needToCheckPersistentVars for UI = False;
declare Integer exp_multipleCheckCount = 0;

declare Boolean exp_enableHudMove for UI = False;
exp_enableHudMove = False;

declare persistent Vec3[Text][Text][Text] eXp_widgetLastPos = Vec3[Text][Text][Text];
declare persistent Vec3[Text][Text][Text] eXp_widgetLastPosRel =  Vec3[Text][Text][Text];	

declare persistent Boolean[Text][Text][Text] eXp_widgetVisible = Boolean[Text][Text][Text];
declare Boolean exp_widgetVisibleBuffered;

declare persistent Text[Text][Text][Text] eXp_widgetLayers = Text[Text][Text][Text];  // layer can be "normal" or "scorestable" or some other for future usage
declare Text exp_widgetLayersBuffered;  

declare Text version = "<?= $this->version ?>";
declare Text id = "<?= $this->name ?>";
declare Text gameMode = "<?= $this->gameMode; ?>";
declare Boolean forceReset = <?= $this->forceReset ?>;
declare Text activeLayer = "<?= $win->getLayer() ?>";
declare Boolean exp_widgetCurrentVisible = False;
declare Boolean exp_widgetVisibilityChanged = False;
declare Integer eXp_lastWidgetCheck = 0;
declare Boolean eXp_firstPersistentCheckDone = False;

if (!eXp_widgetVisible.existskey(version) ) {
	eXp_widgetVisible[version] = Boolean[Text][Text];
}

if ( !eXp_widgetVisible[version].existskey(id) || forceReset) {
	eXp_widgetVisible[version][id] = Boolean[Text];
}

if ( !eXp_widgetVisible[version][id].existskey(gameMode) ) {
	eXp_widgetVisible[version][id][gameMode] = <?= $win->getWidgetVisible(); ?>;
}

if (!eXp_widgetLayers.existskey(version) ) {
	eXp_widgetLayers[version] = Text[Text][Text];
}

if (!eXp_widgetLayers[version].existskey(id) || forceReset) { 
	eXp_widgetLayers[version][id] = Text[Text];
}

if (!eXp_widgetLayers[version][id].existskey(gameMode)) { 
	eXp_widgetLayers[version][id][gameMode] = "normal"; 
}

if (!eXp_widgetLastPos.existskey(version)) {
	eXp_widgetLastPos[version] = Vec3[Text][Text];
}

if (!eXp_widgetLastPos[version].existskey(id) || forceReset) {
	eXp_widgetLastPos[version][id] = Vec3[Text];
}
if (!eXp_widgetLastPos[version][id].existskey(gameMode) ) {
	eXp_widgetLastPos[version][id][gameMode] = < <?= $this->getNumber($win->getPosX()) ?>, <?= $this->getNumber($win->getPosY()) ?>, 0.0>;
}

if (!eXp_widgetLastPosRel.existskey(version)) {
	eXp_widgetLastPosRel[version] = Vec3[Text][Text];
} 

if (!eXp_widgetLastPosRel[version].existskey(id) || forceReset) {
    eXp_widgetLastPosRel[version][id] = Vec3[Text];
}

if (!eXp_widgetLastPosRel[version][id].existskey(gameMode)) {
	eXp_widgetLastPosRel[version][id][gameMode] = < <?= $this->getNumber($win->getPosX()) ?>, <?= $this->getNumber($win->getPosY()) ?>, 0.0>;
}

Window.PosnX = eXp_widgetLastPos[version][id][gameMode][0];
Window.PosnY = eXp_widgetLastPos[version][id][gameMode][1];
LastDelta = eXp_widgetLastPosRel[version][id][gameMode];
Window.RelativePosition = eXp_widgetLastPosRel[version][id][gameMode];

exp_widgetCurrentVisible = eXp_widgetVisible[version][id][gameMode];
exp_widgetVisibleBuffered = eXp_widgetVisible[version][id][gameMode];
exp_widgetLayersBuffered = eXp_widgetLayers[version][id][gameMode];

if (exp_enableHudMove == True) {
	quad.Show();
}else {
	quad.Hide();
}
