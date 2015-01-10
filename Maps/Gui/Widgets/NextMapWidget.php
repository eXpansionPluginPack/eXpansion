<?php

namespace ManiaLivePlugins\eXpansion\Maps\Gui\Widgets;

class NextMapWidget extends \ManiaLivePlugins\eXpansion\Gui\Widgets\Widget
{

    private $bg;
    private $leftFrame, $rightFrame;
    private $mapName;
    private $mapAuthor;
    private $labelName;
    private $labelAuthor, $environment, $time, $country;

    /** @var \Maniaplanet\DedicatedServer\Structures\Map */
    private $map;

    protected function exp_onBeginConstruct()
    {
	$this->setName("Next Map");
	$this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround(60, 15);
	$this->addComponent($this->bg);

	$column = new \ManiaLib\Gui\Layouts\Column();

	$this->leftFrame = new \ManiaLive\Gui\Controls\Frame(4, -1);
	$this->leftFrame->setAlign("left", "top");
	$this->leftFrame->setLayout(clone $column);
	$this->addComponent($this->leftFrame);

	$this->rightFrame = new \ManiaLive\Gui\Controls\Frame(20, -3);
	$this->rightFrame->setLayout(clone $column);
	$this->addComponent($this->rightFrame);

	$biglabel = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(40, 4);
	$biglabel->setStyle("TextRankingsBig");
	$biglabel->setTextSize(2);
	$biglabel->setAlign("left", "center");

	$label = new \ManiaLivePlugins\eXpansion\Gui\Elements\DicoLabel(40, 4);
	$label->setStyle("TextRaceMessage");
	$label->setAlign("left", "center");
	$label->setTextSize(2);
	$label->setTextEmboss();

	$nowPlaying = clone $biglabel;
	$nowPlaying->setText(exp_getMessage("Next Map"));
	$nowPlaying->setPosition(30, 3);
	$nowPlaying->setAlign("center", "center");
	$this->addComponent($nowPlaying);

	$this->country = new \ManiaLib\Gui\Elements\Quad(14, 9);
	$this->country->setId("authorZone");
	$this->country->setImage("http://reaby.kapsi.fi/ml/flags/Other%20Countries.dds", true);
	$this->country->setAlign("left", "top");
	$this->leftFrame->addComponent($this->country);

	$this->labelAuthor = clone $biglabel;
	$this->labelAuthor->setText($this->mapAuthor);
	$this->labelAuthor->setAlign("left", "top");
	$this->leftFrame->addComponent($this->labelAuthor);

	$this->environment = clone $label;
	$this->environment->setText("unknown");
	$this->rightFrame->addComponent($this->environment);

	$this->labelName = clone $biglabel;
	$this->labelName->setText('$ddd' . $this->mapName);
	$this->rightFrame->addComponent($this->labelName);

	$this->time = clone $label;
	$this->rightFrame->addComponent($this->time);
    }

    protected function exp_onEndConstruct()
    {
	$this->setSize(60, 15);
    }

    function setAction($action)
    {
	$this->bg->setAction($action);
    }

    function setMap(\Maniaplanet\DedicatedServer\Structures\Map $map)
    {
	$this->map = $map;
	$this->labelName->setText($this->map->name);
	$this->labelAuthor->setText($this->map->author);
	// $this->time->setText($this->map->goldTime);
	$this->environment->setText($map->environnement);

	if ($map->author == "Nadeo")
	    $this->country->setImage("http://reaby.kapsi.fi/ml/flags/France.dds", true);
    }

    function destroy()
    {
	$this->destroyComponents();
	parent::destroy();
    }

}

?>