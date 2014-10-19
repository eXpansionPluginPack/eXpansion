<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Voip;

class Config extends \ManiaLib\Utils\Singleton {

	//Mumble
	public $mumbleActive = false;
	public $mumbleHost = "";
	public $mumblePort = "";
	public $mumbleImageUrl = "http://reaby.kapsi.fi/ml/logo/mumble.png";
	public $mumbleImageFocusUrl = "http://reaby.kapsi.fi/ml/logo/mumble_focus.png";

	public $mumbleImageSizeX = 128; // image sizeX in px
	public $mumbleImageSizeY = 128; // image sizeY in px

	public $mumbleSize = 5;  // image width in maniaplanet display units

	public $mumbleX = 110;  // image position x in maniaplanet display units
	public $mumbleY = 65; // image position y in maniaplanet display units

	// TeamSpeak
	public $tsActive = false;
	public $tsHost = "";
	public $tsPort = "";
	public $tsImageUrl = "http://reaby.kapsi.fi/ml/logo/ts.png";
	public $tsImageFocusUrl = "http://reaby.kapsi.fi/ml/logo/ts_focus.png";

	public $tsImageSizeX = 128; // image sizeX in px
	public $tsImageSizeY = 128; // image sizeY in px

	public $tsSize = 5;  // image width in maniaplanet display units

	public $tsX = 100;  // image position x in maniaplanet display units
	public $tsY = 65; // image position y in maniaplanet display units
}

?>
