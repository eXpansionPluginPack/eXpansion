<?php

namespace ManiaLivePlugins\eXpansion\AdminGroups\Gui\Windows;

use \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups;

/**
 * Description of CmdMore
 *
 * @author oliverde8
 */
class CmdMore extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window
{

    private $label_cmd, $label_desc, $label_descm, $label_aliases;
    private $bgt_cmd, $bgt_desc, $bgt_descm, $bgt_aliases;

    private $content_cmd, $content_desc, $content_descm, $content_aliases;
    private $bg_cmd, $bg_desc, $bg_descm, $bg_aliases;

    private $cmd;

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->bgt_cmd = new \ManiaLivePlugins\eXpansion\Gui\Elements\TitleBackGround(30, 4);
        $this->bgt_cmd->setPosition(1, -1);
        $this->mainFrame->addComponent($this->bgt_cmd);

        $this->label_cmd = new \ManiaLib\Gui\Elements\Label(30, 5);
        $this->label_cmd->setAlign('left', 'center');
        $this->label_cmd->setScale(0.8);
        $this->label_cmd->setPosition(1, -1);
        $this->mainFrame->addComponent($this->label_cmd);

        $this->bgt_desc = new \ManiaLivePlugins\eXpansion\Gui\Elements\TitleBackGround(30, 4);
        $this->bgt_desc->setPosY(-1);
        $this->mainFrame->addComponent($this->bgt_desc);

        $this->label_desc = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_desc->setAlign('left', 'center');
        $this->label_desc->setScale(0.8);
        $this->label_desc->setPosY(-1);
        $this->mainFrame->addComponent($this->label_desc);

        $this->bgt_descm = new \ManiaLivePlugins\eXpansion\Gui\Elements\TitleBackGround(30, 4);
        $this->bgt_descm->setPosition(1, -10);
        $this->mainFrame->addComponent($this->bgt_descm);

        $this->label_descm = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_descm->setAlign('left', 'center');
        $this->label_descm->setScale(0.8);
        $this->label_descm->setPosition(1, -10);
        $this->mainFrame->addComponent($this->label_descm);

        $this->bgt_aliases = new \ManiaLivePlugins\eXpansion\Gui\Elements\TitleBackGround(30, 4);
        $this->bgt_aliases->setPosY(-10);
        $this->mainFrame->addComponent($this->bgt_aliases);

        $this->label_aliases = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->label_aliases->setAlign('left', 'center');
        $this->label_aliases->setScale(0.8);
        $this->label_aliases->setPosY(-10);
        $this->mainFrame->addComponent($this->label_aliases);

        $this->bg_cmd = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround(0, 30, 4);
        $this->bg_cmd->setPosition(2, -5);
        $this->mainFrame->addComponent($this->bg_cmd);

        $this->content_cmd = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_cmd->setAlign('left', 'top');
        $this->content_cmd->setScale(0.6);
        $this->content_cmd->setPosition(2, -4);
        $this->mainFrame->addComponent($this->content_cmd);

        $this->bg_desc = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround(1, 30, 4);
        $this->bg_desc->setPositionY(-5);
        $this->mainFrame->addComponent($this->bg_desc);

        $this->content_desc = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_desc->setAlign('left', 'top');
        $this->content_desc->setScale(0.6);
        $this->content_desc->setPosY(-4);
        $this->mainFrame->addComponent($this->content_desc);

        $this->bg_descm = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround(3, 30, 4);
        $this->bg_descm->setPosition(2, -13);
        $this->mainFrame->addComponent($this->bg_descm);

        $this->content_descm = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_descm->setAlign('left', 'top');
        $this->content_descm->setScale(0.6);
        $this->content_descm->setPosition(2, -13);
        $this->content_descm->setMaxline(100);
        $this->mainFrame->addComponent($this->content_descm);

        $this->bg_aliases = new \ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround(2, 30, 4);
        $this->bg_aliases->setPosition(2, -13);
        $this->mainFrame->addComponent($this->bg_aliases);

        $this->content_aliases = new \ManiaLib\Gui\Elements\Label(30, 4);
        $this->content_aliases->setAlign('left', 'top');
        $this->content_aliases->setScale(0.6);
        $this->content_aliases->setPosY(-13);
        $this->content_aliases->setMaxline(100);
        $this->mainFrame->addComponent($this->content_aliases);
    }

    public function setCommand(\ManiaLivePlugins\eXpansion\AdminGroups\AdminCmd $command)
    {
        $this->cmd = $command;
    }

    function onResize($oldX, $oldY)
    {
        parent::onResize($oldX, $oldY);

        $sizeX2 = $this->getSizeX() / .8;
        $sizeX3 = $this->getSizeX() / .6;

        $this->label_cmd->setSizeX($sizeX2 / 2 - 4);
        $this->bgt_cmd->setSizeX($this->getSizeX() / 2 - 4);

        $this->label_desc->setPosX($this->getSizeX() / 2);
        $this->label_desc->setSizeX($sizeX2 / 2 - 4);
        $this->bgt_desc->setPosX($this->getSizeX() / 2);
        $this->bgt_desc->setSizeX($this->getSizeX() / 2 - 4);

        $this->label_descm->setSizeX($sizeX2 / 1.5 - 6);
        $this->bgt_descm->setSizeX($this->getSizeX() / 1.5 - 4);

        $this->label_aliases->setPosX($this->getSizeX() / 1.5);
        $this->label_aliases->setSizeX($sizeX2 / 3 - 4);
        $this->bgt_aliases->setPosX($this->getSizeX() / 1.5);
        $this->bgt_aliases->setSizeX($this->getSizeX() / 3 - 4);

        $this->content_cmd->setSizeX($sizeX3 / 2 - 6);
        $this->bg_cmd->setSizeX($this->getSizeX() / 2 - 6);

        $this->content_desc->setPosX($this->getSizeX() / 2 + 1);
        $this->content_desc->setSizeX($sizeX3 / 2 - 6);
        $this->bg_desc->setPosX($this->getSizeX() / 2 + 1);
        $this->bg_desc->setSize($this->getSizeX() / 2 - 6);

        $this->content_descm->setSizeX($sizeX3 / 1.5 - 6);
        $this->content_descm->setSizeY($this->getSizeY() / .6 - 15);
        $this->bg_descm->setSize($this->getSizeX() / 1.5 - 6, $this->getSizeY() - 15);
        $this->bg_descm->setPosY(-12 - (($this->getSizeY() - 15) / 2));

        $this->content_aliases->setPosX($this->getSizeX() / 1.5 + 1);
        $this->content_aliases->setSizeX($this->getSizeX() / 3 - 6);
        $this->content_aliases->setSizeY($this->getSizeY() - 15);
        $this->bg_aliases->setSize($this->getSizeX() / 3 - 6, $this->getSizeY() - 15);
        $this->bg_aliases->setPosY(-12 - (($this->getSizeY() - 15) / 2));
        $this->bg_aliases->setPosX($this->getSizeX() / 1.5 + 1);
    }

    function onShow()
    {

        $this->label_cmd->setText('$w' . __(AdminGroups::$txt_command, $this->getRecipient()));
        $this->label_desc->setText('$w' . __(AdminGroups::$txt_description, $this->getRecipient()));
        $this->label_descm->setText('$w' . __(AdminGroups::$txt_descMore, $this->getRecipient()));
        $this->label_aliases->setText('$w' . __(AdminGroups::$txt_aliases, $this->getRecipient()));

        $this->content_cmd->setText('/admin ' . $this->cmd->getCmd());
        $this->content_desc->setText(__($this->cmd->getHelp(), $this->getRecipient()));
        $this->content_descm->setText(__($this->cmd->getHelpMore(), $this->getRecipient()));

        $aliases = "";
        $i = 1;
        foreach ($this->cmd->getAliases() as $alias) {
            $aliases .= '$w' . $i . ')$z ' . $alias . "\n";
            $i++;
        }
        $this->content_aliases->setText($aliases);
    }

    function destroy()
    {
        parent::destroy();
        $this->bg_aliases->destroy();
        $this->bg_desc->destroy();
        $this->bg_descm->destroy();
        $this->bg_cmd->destroy();

        $this->bg_aliases = null;
        $this->bg_desc = null;
        $this->bg_descm = null;
        $this->bg_cmd = null;

        $this->bgt_aliases->destroy();
        $this->bgt_desc->destroy();
        $this->bgt_descm->destroy();
        $this->bgt_cmd->destroy();

        $this->bgt_aliases = null;
        $this->bgt_desc = null;
        $this->bgt_descm = null;
        $this->bgt_cmd = null;
    }

}

?>
