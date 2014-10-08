<?php

namespace ManiaLivePlugins\eXpansion\Gui;

class Config extends \ManiaLib\Utils\Singleton
{

	public $logo = "http://reaby.kapsi.fi/ml/exp.png";

	public $button = "http://reaby.kapsi.fi/ml/button2.png";

	public $buttonActive = "http://reaby.kapsi.fi/ml/button2_active.png";

	public $uiTextureBase = "http://reaby.kapsi.fi/ml/ui/";

	public $uiTextures_Window = array("top_left.png", "top_center.png", "top_left.png", "left.png", "bg.png", "right.png", "bottom_left.png", "bottom_center.png", "bottom_right.png");

	public $uiTextures_statusButtons = array("1_on.png", "1_off.png", "2_on.png", "2_off.png", "3_on.png", "3_off.png",);

	public $uiTextures_closeButton = array("normal.png", "focus.png");

	public $uiTextures_button = array("normal.png", "focus.png");

	public $uiTextures_checkbox = array("normal_on.png", "normal_off.png", "disabled_on.png", "disabled_off.png");

	public $uiTextures_ratiobutton = array("normal_on.png", "normal_off.png");

	public $uiTextures_inputbox = array("left.png", "center.png", "right.png");

	public $uiTextures_widgets = array("title.png", "header_left.png", "header_right.png", "header_center.png");

	public $uiTextures_scrollbar = array("background.png", "scrollbar_normal.png", "scrollbar_focus.png", "downButton_on.png", "downButton_off.png", "downButton_focus.png", "upButton_on.png", "upButton_off.png", "upButton_focus.png");

	public $uiTextures_menu = array("top_on.png", "top_off.png", "middle_on.png", "middle_off.png", "bottom_on.png", "bottom_off.png");

	public $uiTextures_listitem = array("normal_left.png", "normal_center.png", "normal_right.png", "odd_left.png", "odd_center.png", "odd_right.png", "even_left.png", "even_center.png", "even_right.png");

	public $windowTitleColor = "000d";

	public $buttonTitleColor = "fffd";

	public $style_list_bgColor = array('aaa8', 'eee8');

	public $style_list_bgStyle = array('BgsPlayerCard', 'BgsPlayerCard');

	public $style_list_bgSubStyle = array('BgRacePlayerName', 'BgRacePlayerName');

	public $style_list_posXOffset = -1;

	public $style_list_sizeXOffset = 0;

	public $style_list_posYOffset = 0;

	public $style_list_sizeYOffset = -0.25;

	public $style_title_bgColor = 'ddd4';

	public $style_title_bgStyle = 'Bgs1';

	public $style_title_bgSubStyle = 'BgCard';

	public $style_title_posXOffset = -1;

	public $style_title_sizeXOffset = 2;

	public $style_title_posYOffset = 0;

	public $style_title_sizeYOffset = 0;

	public $style_widget_bgColor = '';

	public $style_widget_bgStyle = 'BgsPlayerCard';

	public $style_widget_bgSubStyle = 'BgRacePlayerName'; // BgList

	public $style_widget_bgColorize = '000'; // BgList

	public $style_widget_bgOpacity = 0.9;

	public $style_widget_bgXOffset = 0;

	public $style_widget_bgYOffset = 0;

	public $style_widget_title_bgColorize = '3af'; // BgList

	public $style_widget_title_bgOpacity = 0.6;

	public $style_widget_title_bgXOffset = 0;

	public $style_widget_title_bgYOffset = 0.75;

	public $style_widget_title_lbStyle = 'TextCardScores2';

	public $style_widget_title_lbSize = 1;

	public $style_widget_title_lbColor = 'fff';

	public $disableAnimations = false;

	public $disablePersonalHud = false;

	public function getImage($folder, $image)
	{
		return trim($this->uiTextureBase, "/") . '/' . $folder . '/' . $image;
	}

}

?>
