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

declare persistent Boolean exp_enableHudMove = False;
declare persistent Vec3[Text][Text] exp_widgetLastPos;
declare persistent Vec3[Text][Text] exp_widgetLastPosRel;			
declare persistent Boolean[Text][Text] exp_widgetVisible;
declare persistent Text[Text][Text] exp_widgetLayers;  // layer can be "normal" or "scorestable" or some other for future usage

declare Text version = "<?= $this->version ?>";
declare Text id = "<?= $this->name ?>";
declare Boolean forceReset = <?= $this->forceReset ?>;
declare Text activeLayer = "<?= $win->getLayer() ?>";
declare Boolean exp_widgetCurrentVisible = False;
declare Boolean exp_widgetVisibilityChanged = False;

if (!exp_widgetVisible.existskey(version) ) {
	exp_widgetVisible[version] = Boolean[Text];
}

if ( !exp_widgetVisible[version].existskey(id) || forceReset) {
	exp_widgetVisible[version][id] = True;
}

if (!exp_widgetLayers.existskey(version) ) {
	exp_widgetLayers[version] = Text[Text];
}

if (!exp_widgetLayers[version].existskey(id) || forceReset) { 
    exp_widgetLayers[version][id] = "normal";
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

exp_widgetCurrentVisible = exp_widgetVisible[version][id];
