<?php

namespace ManiaLivePlugins\eXpansion\Mumble\Gui\Widgets;

class MumbleInfo extends \ManiaLive\Gui\Window
{

    private $frame;
    private $TextChannelName;
    private $IconMulti;
    private $QuadCard;
    private $QuadLine;
    private $channels;
    private $QuadChannel;
    private $LabelChannel;
    private $exitQuad;
    private $subchannel;
    private $subchannels;
    private $icon_userQuad;
    private $icon_deafQuad;
    private $icon_authQuad;
    private $icon_selfmutedQuad;
    private $UserLabel;
    private $icon_mumble = "http://tmrankings.com/manialink/mv/icons/mumble.png";
    private $icon_channel = "http://tmrankings.com/manialink/mv/icons/channel.png";
    private $icon_user_on = "http://tmrankings.com/manialink/mv/icons/user_on.png";
    private $icon_user_off = "http://tmrankings.com/manialink/mv/icons/user_off.png";
    private $icon_authenticated = "http://tmrankings.com/manialink/mv/icons/authenticated.png";
    private $icon_muted_server = "http://tmrankings.com/manialink/mv/icons/muted_server.png";
    private $icon_deaf_self = "http://tmrankings.com/manialink/mv/icons/deafened_self.png";
    private $icon_deaf_server = "http://tmrankings.com/manialink/mv/icons/deafened_server.png";
    private $icon_selfmute = "http://tmrankings.com/manialink/mv/icons/muted_self.png";
    private $icon_active = "http://tmrankings.com/manialink/mv/icons/talking_alt.png";

    private $items = array();

    protected function onConstruct()
    {
        parent::onConstruct();

        $this->frame = new \ManiaLib\Gui\Elements\Frame();
        $this->frame->setPosition(-20, 20, 1);
        $this->addComponent($this->frame);

        $this->QuadCard = new \ManiaLib\Gui\Elements\Quad();
        $this->QuadCard->setSize(200, 100);
        $this->QuadCard->setPosition(0, 0, 0);
        $this->QuadCard->setStyle(\ManiaLib\Gui\Elements\BgsPlayerCard::BgsPlayerCard);
        $this->QuadCard->setSubStyle(\ManiaLib\Gui\Elements\BgsPlayerCard::BgRacePlayerName);
        $this->addComponent($this->QuadCard);

        $this->QuadLine = new \ManiaLib\Gui\Elements\Quad();
        $this->QuadLine->setSize(200, 5);
        $this->QuadLine->setPosition(0, 0, 0.04);
        $this->QuadLine->setStyle(\ManiaLib\Gui\Elements\BgsPlayerCard::BgsPlayerCard);
        $this->QuadLine->setSubStyle(\ManiaLib\Gui\Elements\BgsPlayerCard::ProgressBar);
        $this->addComponent($this->QuadLine);

        $this->IconMulti = new \ManiaLib\Gui\Elements\Icons128x128_1();
        $this->IconMulti->setSize(5, 5);
        $this->IconMulti->setPosition(0.6, 0, 0.05);
        $this->IconMulti->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Multiplayer);
        $this->addComponent($this->IconMulti);

        $this->TextChannelName = new \ManiaLib\Gui\Elements\Label();
        $this->TextChannelName->setSize(50.3, 0);
        $this->TextChannelName->setPosition(7, -0.65, 0.05);
        $this->TextChannelName->setTextColor('fff');
        $this->TextChannelName->setTextSize(2);
        $this->addComponent($this->TextChannelName);
    }

    protected function onDraw()
    {
        parent::onDraw();
        //echo "draw: " . $this->getRecipient() . "\n";
    }

    public function destroy()
    {
        $this->destroyComponents();

        parent::destroy();
    }

    public function setData($data)
    {
        $this->TextChannelName->setText($data['name']);
        $this->channels = $data['root']['channels'];
        //var_dump($this->channels);
        $line = 1;
        $offset = 0;
        $entries = 0;
        foreach ($this->channels as $channel) {
            if ($line > 10) {
                $offset += 70.5;
                $line = 1;
            }

            $pid_offset = 0;
            if ($channel['name'] > 0) {
                $pid_offset = 1;
            }

            $this->QuadChannel = new \ManiaLib\Gui\Elements\Icons128x128_1();
            $this->QuadChannel->setSize(4, 4);
            $this->QuadChannel->setPosition($offset + 40, -6 * $line, 0.05);
            $this->QuadChannel->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Lan);
            $this->addComponent($this->QuadChannel);

            $this->LabelChannel = new \ManiaLib\Gui\Elements\Label();
            $this->LabelChannel->setTextColor('fff');
            $this->LabelChannel->setPosition($offset + $pid_offset + 0.9, -6 * $line, 0.05);
            $this->LabelChannel->setSize(23.4 - $pid_offset, 2);
            $this->LabelChannel->setScale(0.9);
            $this->LabelChannel->setStyle('TextTitle3');
            $this->LabelChannel->setTextSize(1);
            $this->LabelChannel->setText($channel['name']);
            $this->LabelChannel->setUrl($channel['x_connecturl']);
            $this->addComponent($this->LabelChannel);
            $line++;
            $entries++;
            $this->subchannels = $channel['channels'];
            if (sizeof($this->subchannels) > 0) {
                foreach ($this->subchannels as $this->subchannel) {
                    if ($line > 10) {
                        $offset += 70.5;
                        $line = 1;
                    }
                    $this->QuadChannel = new \ManiaLib\Gui\Elements\Icons128x128_1();
                    $this->QuadChannel->setSize(4, 4);
                    $this->QuadChannel->setPosition($offset + 40, -6 * $line, 0.05);
                    $this->QuadChannel->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Lan);
                    $this->addComponent($this->QuadChannel);

                    $this->LabelChannel = new \ManiaLib\Gui\Elements\Label();
                    $this->LabelChannel->setTextColor('fff');
                    $this->LabelChannel->setPosition($offset + $pid_offset + 0.9, -6 * $line, 0.05);
                    $this->LabelChannel->setSize(23.4 - $pid_offset, 2);
                    $this->LabelChannel->setScale(0.9);
                    $this->LabelChannel->setStyle('TextTitle3');
                    $this->LabelChannel->setTextSize(1);
                    $this->LabelChannel->setText($this->subchannel['name']);
                    $this->LabelChannel->setUrl($this->subchannel['x_connecturl']);
                    $this->addComponent($this->LabelChannel);
                    $line++;
                    $entries++;
                    $users = $this->subchannel['users'];
                    foreach ($users as $user) {
                        if ($line > 10) {
                            $offset += 70.5;
                            $line = 1;
                        }
                        $icon_user = ($user['bytespersec'] == 0 ? $this->icon_user_off : $this->icon_user_on);
                        $auth = ($user['userid'] > 0);
                        $selfmute = ($user['selfMute']);
                        $deaf = ($user['selfDeaf']);
                        if ($deaf) {
                            $icon_deaf = $this->icon_deaf_self;
                            $this->icon_deaf = $icon_deaf;
                        } else {
                            $icon_deaf = $this->icon_active;
                            $this->icon_deaf = $icon_deaf;
                        }
                        if ($selfmute) {
                            $icon_selfmuted = $this->icon_selfmute;
                            $this->icon_selfmuted = $icon_selfmuted;
                        } else {
                            $icon_selfmuted = $this->icon_active;
                            $this->icon_selfmuted = $icon_selfmuted;
                        }
                        if ($auth) {
                            $icon_auth = $this->icon_authenticated;
                            $this->icon_auth = $icon_auth;
                        } else {
                            $icon_auth = $this->icon_mumble;
                            $this->icon_auth = $icon_auth;
                        }
                        $this->icon_userQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_userQuad->setSize(3, 3);
                        $this->icon_userQuad->setPosition($offset + 30, -6 * $line, 0.05);
                        $this->icon_userQuad->setImage($this->icon_user);
                        $this->addComponent($this->icon_userQuad);

                        $this->icon_authQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_authQuad->setSize(3, 3);
                        $this->icon_authQuad->setPosition($offset + 35, -6 * $line, 0.05);
                        $this->icon_authQuad->setImage($this->icon_auth);
                        $this->addComponent($this->icon_authQuad);

                        $this->icon_selfmutedQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_selfmutedQuad->setSize(3, 3);
                        $this->icon_selfmutedQuad->setPosition($offset + 40, -6 * $line, 0.05);
                        $this->icon_selfmutedQuad->setImage($this->icon_selfmuted);
                        $this->addComponent($this->icon_selfmutedQuad);

                        $this->icon_deafQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_deafQuad->setSize(3, 3);
                        $this->icon_deafQuad->setPosition($offset + 45, -6 * $line, 0.05);
                        $this->icon_deafQuad->setImage($this->icon_deaf);
                        $this->addComponent($this->icon_deafQuad);

                        $this->UserLabel = new \ManiaLib\Gui\Elements\Label();
                        $this->UserLabel->setSize(40 - $pid_offset);
                        $this->UserLabel->setPosition($offset + 2.9, -6 * $line, 0.05);
                        $this->UserLabel->setTextColor('fff');
                        $this->UserLabel->setScale(0.9);
                        $this->UserLabel->setStyle('TextTitle2Blink');
                        $this->UserLabel->setTextSize(1);
                        $this->UserLabel->setText($user['name']);
                        $this->addComponent($this->UserLabel);
                        $line++;
                        $entries++;
                    }
                }
                foreach ($this->subchannel['channels'] as $sub_subchannel) {
                    if ($line > 10) {
                        $offset += 70.5;
                        $line = 1;
                    }
                    $this->QuadChannel = new \ManiaLib\Gui\Elements\Icons128x128_1();
                    $this->QuadChannel->setSize(4, 4);
                    $this->QuadChannel->setPosition($offset + 40, -6 * $line, 0.05);
                    $this->QuadChannel->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Lan);
                    $this->addComponent($this->QuadChannel);

                    $this->LabelChannel = new \ManiaLib\Gui\Elements\Label();
                    $this->LabelChannel->setTextColor('fff');
                    $this->LabelChannel->setPosition($offset + $pid_offset + 0.9, -6 * $line, 0.05);
                    $this->LabelChannel->setSize(23.4 - $pid_offset, 2);
                    $this->LabelChannel->setScale(0.9);
                    $this->LabelChannel->setStyle('TextTitle3');
                    $this->LabelChannel->setTextSize(1);
                    $this->LabelChannel->setText($sub_subchannel['name']);
                    $this->LabelChannel->setUrl($sub_subchannel['x_connecturl']);
                    $this->addComponent($this->LabelChannel);
                    $line++;
                    $entries++;
                    $users = $sub_subchannel['users'];
                    foreach ($users as $user) {
                        if ($line > 10) {
                            $offset += 70.5;
                            $line = 1;
                        }
                        $icon_user = ($user['bytespersec'] == 0 ? $this->icon_user_off : $this->icon_user_on);
                        $auth = ($user['userid'] > 0);
                        $selfmute = ($user['selfMute']);
                        $deaf = ($user['selfDeaf']);
                        if ($deaf) {
                            $icon_deaf = $this->icon_deaf_self;
                            $this->icon_deaf = $icon_deaf;
                        } else {
                            $icon_deaf = $this->icon_active;
                            $this->icon_deaf = $icon_deaf;
                        }
                        if ($selfmute) {
                            $icon_selfmuted = $this->icon_selfmute;
                            $this->icon_selfmuted = $icon_selfmuted;
                        } else {
                            $icon_selfmuted = $this->icon_active;
                            $this->icon_selfmuted = $icon_selfmuted;
                        }
                        if ($auth) {
                            $icon_auth = $this->icon_authenticated;
                            $this->icon_auth = $icon_auth;
                        } else {
                            $icon_auth = $this->icon_mumble;
                            $this->icon_auth = $icon_auth;
                        }
                        $this->icon_userQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_userQuad->setSize(3, 3);
                        $this->icon_userQuad->setPosition($offset + 30, -6 * $line, 0.05);
                        $this->icon_userQuad->setImage($this->icon_user);
                        $this->addComponent($this->icon_userQuad);

                        $this->icon_authQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_authQuad->setSize(3, 3);
                        $this->icon_authQuad->setPosition($offset + 35, -6 * $line, 0.05);
                        $this->icon_authQuad->setImage($this->icon_auth);
                        $this->addComponent($this->icon_authQuad);

                        $this->icon_selfmutedQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_selfmutedQuad->setSize(3, 3);
                        $this->icon_selfmutedQuad->setPosition($offset + 40, -6 * $line, 0.05);
                        $this->icon_selfmutedQuad->setImage($this->icon_selfmuted);
                        $this->addComponent($this->icon_selfmutedQuad);

                        $this->icon_deafQuad = new \ManiaLib\Gui\Elements\Quad();
                        $this->icon_deafQuad->setSize(3, 3);
                        $this->icon_deafQuad->setPosition($offset + 45, -6 * $line, 0.05);
                        $this->icon_deafQuad->setImage($this->icon_deaf);
                        $this->addComponent($this->icon_deafQuad);

                        $this->UserLabel = new \ManiaLib\Gui\Elements\Label();
                        $this->UserLabel->setSize(40 - $pid_offset);
                        $this->UserLabel->setPosition($offset + 2.9, -6 * $line, 0.05);
                        $this->UserLabel->setTextColor('fff');
                        $this->UserLabel->setScale(0.9);
                        $this->UserLabel->setStyle('TextTitle2Blink');
                        $this->UserLabel->setTextSize(1);
                        $this->UserLabel->setText($user['name']);
                        $this->addComponent($this->UserLabel);
                        $line++;
                        $entries++;
                    }
                }
            }
            $users = $channel['users'];
            foreach ($users as $user) {
                if ($line > 10) {
                    $offset += 70.5;
                    $line = 1;
                }
                $icon_user = ($user['bytespersec'] == 0 ? $this->icon_user_off : $this->icon_user_on);
                $this->icon_user = $icon_user;
                $auth = ($user['userid'] > 0);
                $selfmute = ($user['selfMute']);
                $deaf = ($user['selfDeaf']);
                if ($deaf) {
                    $icon_deaf = $this->icon_deaf_self;
                    $this->icon_deaf = $icon_deaf;
                } else {
                    $icon_deaf = $this->icon_active;
                    $this->icon_deaf = $icon_deaf;
                }
                if ($selfmute) {
                    $icon_selfmuted = $this->icon_selfmute;
                    $this->icon_selfmuted = $icon_selfmuted;
                } else {
                    $icon_selfmuted = $this->icon_active;
                    $this->icon_selfmuted = $icon_selfmuted;
                }
                if ($auth) {
                    $icon_auth = $this->icon_authenticated;
                    $this->icon_auth = $icon_auth;
                } else {
                    $icon_auth = $this->icon_mumble;
                    $this->icon_auth = $icon_auth;
                }
                $this->icon_userQuad = new \ManiaLib\Gui\Elements\Quad();
                $this->icon_userQuad->setSize(3, 3);
                $this->icon_userQuad->setPosition($offset + 30, -6 * $line, 0.05);
                $this->icon_userQuad->setImage($this->icon_user);
                $this->addComponent($this->icon_userQuad);

                $this->icon_authQuad = new \ManiaLib\Gui\Elements\Quad();
                $this->icon_authQuad->setSize(3, 3);
                $this->icon_authQuad->setPosition($offset + 35, -6 * $line, 0.05);
                $this->icon_authQuad->setImage($this->icon_auth);
                $this->addComponent($this->icon_authQuad);

                $this->icon_selfmutedQuad = new \ManiaLib\Gui\Elements\Quad();
                $this->icon_selfmutedQuad->setSize(3, 3);
                $this->icon_selfmutedQuad->setPosition($offset + 40, -6 * $line, 0.05);
                $this->icon_selfmutedQuad->setImage($this->icon_selfmuted);
                $this->addComponent($this->icon_selfmutedQuad);

                $this->icon_deafQuad = new \ManiaLib\Gui\Elements\Quad();
                $this->icon_deafQuad->setSize(3, 3);
                $this->icon_deafQuad->setPosition($offset + 45, -6 * $line, 0.05);
                $this->icon_deafQuad->setImage($this->icon_deaf);
                $this->addComponent($this->icon_deafQuad);

                $this->UserLabel = new \ManiaLib\Gui\Elements\Label();
                $this->UserLabel->setSize(40 - $pid_offset);
                $this->UserLabel->setPosition($offset + 2.9, -6 * $line, 0.05);
                $this->UserLabel->setTextColor('fff');
                $this->UserLabel->setScale(0.9);
                $this->UserLabel->setStyle('TextTitle2Blink');
                $this->UserLabel->setTextSize(1);
                $this->UserLabel->setText($user['name']);
                $this->addComponent($this->UserLabel);
                $line++;
                $entries++;
            }
        }
        $this->exitQuad = new \ManiaLib\Gui\Elements\Quad();
        $this->exitQuad->setSize(8, 8);
        $this->exitQuad->setPosition(190, -90, 0);
        $this->exitQuad->setImage('http://reaby.kapsi.fi/ml/close_off.png');
        $this->exitQuad->setImageFocus('http://reaby.kapsi.fi/ml/close_on.png');
        $this->exitQuad->setAction(0);
        $this->addComponent($this->exitQuad);
    }
}
