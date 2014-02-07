<?php

namespace ManiaLivePlugins\eXpansion\Widgets_Advertising;

/**
 * Description of Widgets_Advertising
 *
 * @author Petri
 */
class Widgets_Advertising extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var Config */
    private $config;

    public function exp_onReady() {
        $this->config = Config::GetInstance();
	$this->displayWidget(null);
    }

    

    public function displayWidget($login) {
        $widget = Gui\Widgets\WidgetAd::Create($login);
        $widget->setPosition($this->config->x, $this->config->y, -60);
        $widget->setImage($this->config->imageUrl, $this->config->imageFocusUrl, $this->config->url);
        $widget->setImageSize($this->config->imageSizeX, $this->config->imageSizeY, $this->config->size);
        $widget->show();
    }

}
