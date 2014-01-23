<?php

namespace ManiaLivePlugins\eXpansion\Gui\Windows;

class HudSetVisibility extends \ManiaLive\Gui\Window {

    protected $xml;

    /** @var \ManiaLivePlugins\eXpansion\Gui\Structures\ConfigItem[] */
    private $data;

    protected function onConstruct() {
	$this->xml = new \ManiaLive\Gui\Elements\Xml();
    }

    function onResize($oldX, $oldY) {
	parent::onResize($oldX, $oldY);
    }

    public function setData($data) {
	$this->data = $data;
    }

    public function onDraw() {
	$this->removeComponent($this->xml);
	$content = '<script><!--
                       main () {     
                        declare persistent Boolean[Text][Text] exp_widgetVisible;
			';
	foreach ($this->data as $item) {
	    $bool = "False";
	    if ($item->value)
		$bool = "True";

	    $content .= "exp_widgetVisible[\"".\ManiaLivePlugins\eXpansion\Core\Core::EXP_VERSION."\"][\"" . $item->id . "\"] = " . $bool . "; \n";
	}

	$content .='
                       }
                --></script>';
	$this->xml->setContent($content);
	$this->addComponent($this->xml);
	parent::onDraw();
    }

    function destroy() {
	parent::destroy();
    }

}

?>
