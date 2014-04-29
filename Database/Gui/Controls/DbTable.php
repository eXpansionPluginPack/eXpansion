<?php

namespace ManiaLivePlugins\eXpansion\Database\Gui\Controls;

class DbTable extends \ManiaLive\Gui\Control {

    private $bg;
    private $label;
    private $inputbox;
    private $frame;
    public $checkBox = null;
    public $tableName;
    public $type = null;

    /**
     * 
     * @param int $indexNumber
     * @param string $settingName
     * @param mixed $value
     * @param int $sizeX
     */
    function __construct($indexNumber, $tableName, $sizeX) {
        $sizeY = 6;
        $this->tableName = $tableName;


        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX - 8, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(2,0);
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $this->checkBox = new \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox(4, 4, 40);
        $this->frame->addComponent($this->checkBox);

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $this->label->setText($tableName);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);

        $this->frame->addComponent($spacer);




        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY) {        
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

    // manialive 3.1 override to do nothing.
    function destroy() {
        
    }

    /*
     * custom function to remove contents.
     */

    function erase() {
        $this->checkBox->destroy();
        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}
?>

