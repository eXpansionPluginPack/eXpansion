<?php

namespace ManiaLivePlugins\eXpansion\ServerNeighborhood\Gui\Controls;

use ManiaLivePlugins\eXpansion\Gui\Elements\ListBackGround;

/**
 * Description of PlayerItem
 *
 * @author oliverde8
 */
class PlayerItem extends \ManiaLivePlugins\eXpansion\Gui\Control
{

    private static $bgStyle = 'Bgs1';
    private static $bgStyles = array('BgList', 'BgListLine');
    protected $bg_nick;
    protected $bg_login;
    protected $bg_nation;
    protected $bg_ladder;
    protected $bg_spec;
    protected $label_nick;
    protected $label_login;
    protected $label_nation;
    protected $label_ladder;
    protected $icon_spec;

    //nickname, login, nation, ladder, spectator
    public function __construct($indexNumber, $ctr, $player)
    {
        $sizeY = 4;

        $this->bg_nick = new ListBackGround($indexNumber, 0, $sizeY);
        $this->addComponent($this->bg_nick);

        $this->label_nick = new \ManiaLib\Gui\Elements\Label(0, $sizeY);
        $this->label_nick->setScale(.8);
        $this->label_nick->setPosition(1, -1);
        $this->label_nick->setText('$000' . $player->nickname);
        $this->addComponent($this->label_nick);

        $this->bg_login = new ListBackGround($indexNumber, 0, $sizeY);
        $this->addComponent($this->bg_login);

        $this->label_login = new \ManiaLib\Gui\Elements\Label(0, $sizeY);
        $this->label_login->setScale(.8);
        $this->label_login->setPosition(1, $this->bg_login->getPosY() - 1);
        $this->label_login->setText('$000' . $player->login);
        $this->addComponent($this->label_login);

        $this->bg_nation = new ListBackGround($indexNumber, 0, $sizeY);
        $this->addComponent($this->bg_nation);

        $this->label_nation = new \ManiaLib\Gui\Elements\Label(0, $sizeY);
        $this->label_nation->setScale(.8);
        $this->label_nation->setPosition(1, $this->bg_nation->getPosY() - 1);
        $nation = str_replace("World|", "", $player->nation);
        $this->label_nation->setText('$000' . $nation);
        $this->addComponent($this->label_nation);

        $this->bg_ladder = new ListBackGround($indexNumber, 0, $sizeY);
        $this->addComponent($this->bg_ladder);

        $this->label_ladder = new \ManiaLib\Gui\Elements\Label(0, $sizeY);
        $this->label_ladder->setScale(.8);
        $this->label_ladder->setPosition(1, $this->bg_ladder->getPosY() - 1);
        $this->label_ladder->setText('$000' . $player->ladder);
        $this->addComponent($this->label_ladder);


        if ($player->spectator == 'true') {
            $this->icon_spec = new \ManiaLib\Gui\Elements\Icons64x64_1(6, 4);
            $this->icon_spec->setSubStyle(\ManiaLib\Gui\Elements\Icons64x64_1::CameraLocal);
            $this->icon_spec->setPosY(1);
        } else {
            $this->icon_spec = new \ManiaLib\Gui\Elements\UIConstructionSimple_Buttons(10, 10);
            $this->icon_spec->setSubStyle(\ManiaLib\Gui\Elements\UIConstructionSimple_Buttons::Drive);
            $this->icon_spec->setPosY(3.5);
        }
        $this->addComponent($this->icon_spec);

        foreach ($this->getComponents() as $cmp) {
            if ($cmp instanceof ListBackGround) {

            } else {
                $cmp->setPosY($cmp->getPosY() + 2);
            }
        }

        $this->sizeY = 4;
    }

    public function onResize($oldX, $oldY)
    {
        $this->bg_nick->setSize($this->getSizeX() * .3, $this->getSizeY());
        $this->label_nick->setSize(($this->getSizeX() * .3) / .8, $this->getSizeY());

        $this->bg_login->setSize($this->getSizeX() * .2, $this->getSizeY());
        $this->bg_login->setPosX($this->getSizeX() * .3 + 1);
        $this->label_login->setSizeX(($this->bg_login->getSizeX() - 2) / .8);
        $this->label_login->setPosX($this->bg_login->getPosX() + 1);

        $this->bg_nation->setSize($this->getSizeX() * .3, $this->getSizeY());
        $this->bg_nation->setPosX($this->getSizeX() * .5 + 1);
        $this->label_nation->setPosX($this->bg_nation->getPosX() + 1);

        $this->bg_ladder->setSize($this->getSizeX() * .1, $this->getSizeY());
        $this->bg_ladder->setPosX($this->getSizeX() * .8 + 1);
        $this->label_ladder->setPosX($this->bg_ladder->getPosX() + 1);

        $sizeX = $this->getSizeX() * .08;
        $posX = $this->getSizeX() * .9 + 1;

        $this->icon_spec->setPosX($posX + $sizeX / 2 - $this->icon_spec->getSizeX() / 2);
    }
}
