<?php

namespace ManiaLivePlugins\eXpansion\Database\Gui\Controls;

class SqlFile extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private $bg;
    private $label;
    private $frame;
    public $checkBox = null;
    public $btnRestore;
    public $actionRestore = null;
    public $btnDelete;
    public $actionDelete = null;

    /**
     *
     * @param int $indexNumber
     * @param string $settingName
     * @param mixed $value
     * @param int $sizeX
     */
    function __construct($indexNumber, $controller, $filename, $sizeX)
    {
        $sizeY = 6;
        $this->actionRestore = $this->createAction(array($controller, 'restoreFile'), $filename);
        $this->actionDelete = $this->createAction(array($controller, 'deleteFile'), $filename);


        $this->bg = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround($indexNumber, $sizeX - 8, $sizeY);
        $this->addComponent($this->bg);

        $this->frame = new \ManiaLive\Gui\Controls\Frame(4, 0);
        $this->frame->setSize($sizeX, $sizeY);
        $this->frame->setLayout(new \ManiaLib\Gui\Layouts\Line());

        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        //$this->frame->addComponent($spacer);

        $this->label = new \ManiaLib\Gui\Elements\Label(120, 4);
        $this->label->setAlign('left', 'center');
        $file = explode('/', $filename);
        $text = utf8_encode(end($file));
        $text = str_replace(".txt", "", $text);
        $this->label->setText($text);
        $this->label->setScale(0.8);
        $this->frame->addComponent($this->label);


        $spacer = new \ManiaLib\Gui\Elements\Quad();
        $spacer->setSize(4, 4);
        $spacer->setStyle(\ManiaLib\Gui\Elements\Icons64x64_1::EmptyIcon);
        $this->frame->addComponent($spacer);

        $this->btnRestore = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnRestore->setText("Restore");
        $this->btnRestore->colorize("dd0");
        $this->btnRestore->setScale(0.5);
        $this->btnRestore->setAction($this->actionRestore);
        $this->frame->addComponent($this->btnRestore);

        $this->btnDelete = new \ManiaLivePlugins\eXpansion\Gui\Elements\Button();
        $this->btnDelete->setText('$dd0Delete');
        $this->btnDelete->colorize("222");
        $this->btnDelete->setScale(0.5);
        $this->btnDelete->setAction($this->actionDelete);
        $this->frame->addComponent($this->btnDelete);

        $this->addComponent($this->frame);

        $this->sizeX = $sizeX;
        $this->sizeY = $sizeY;
        $this->setSize($sizeX, $sizeY);
    }

    protected function onResize($oldX, $oldY)
    {
        $this->frame->setSize($this->sizeX, $this->sizeY);
    }

// manialive 3.1 override to do nothing.
    function destroy()
    {

    }

    /*
     * custom function to remove contents.
     */

    function erase()
    {

        $this->btnRestore->destroy();
        $this->btnDelete->destroy();

        $this->frame->clearComponents();
        $this->frame->destroy();
        $this->destroyComponents();
        parent::destroy();
    }

}

?>

