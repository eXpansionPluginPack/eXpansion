<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising;

/**
 * Description of Widgets_Advertising
 *
 * @author Petri
 */
class Widgets_Advertising extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

	/** @var Config */
	private $config;

	private $settingsChanged = false;

	public function exp_onReady()
	{
		$this->config = Config::GetInstance();
		$this->displayWidget(null);
		$this->enableApplicationEvents();
	}

	public function onSettingsChanged(\ManiaLivePlugins\eXpansion\Core\types\config\Variable $var)
	{
		$name = $var->getName();

		if(isset($this->config->$name)){
			$this->settingsChanged = true;
		}
	}

	function onPreLoop()
	{
		if($this->settingsChanged){
			$this->displayWidget(null);
			$this->settingsChanged = false;
		}
	}


	public function displayWidget($login)
	{
		$widget = Gui\Widgets\WidgetAd::Create($login);

		$widget->setPosition($this->config->x, $this->config->y, -60);
		$widget->setImage($this->config->imageUrl, $this->config->imageFocusUrl, $this->config->url);
		$widget->setImageSize($this->config->imageSizeX, $this->config->imageSizeY, $this->config->size);
		$widget->setPositionX($this->config->x);
		$widget->setPositionY($this->config->y);
		$widget->show();
	}
	
	public function exp_onUnload()
	{
		Gui\Widgets\WidgetAd::EraseAll();
		parent::exp_onUnload();
	}

}
