<?php

namespace ManiaLivePlugins\eXpansion\Quiz\Gui\Windows;

use \ManiaLivePlugins\eXpansion\Gui\Elements\Button as OkButton;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Checkbox;
use \ManiaLivePlugins\eXpansion\Gui\Elements\Ratiobutton;

class QuestionWindow extends \ManiaLivePlugins\eXpansion\Gui\Windows\Window {

    private $ok;
    private $cancel;
    private $actionOk;
    private $actionCancel;
    private $IBanswers;
    private $IBQuestion;
    private $frame;

    /** @var \ManiaLivePlugins\eXpansion\Quiz\Quiz */
    public static $mainPlugin;

    protected function onConstruct() {
        parent::onConstruct();
        $login = $this->getRecipient();


        $this->frame = new \ManiaLive\Gui\Controls\Frame(0, -6);
        $this->frame->setSize(240, 120);
        $this->frame->setLayout(New \ManiaLib\Gui\Layouts\Column(240, 6));


        $this->IBQuestion = new Inputbox("question", 235);
        $this->IBQuestion->setLabel(__("Question"), $login);
        $this->frame->addComponent($this->IBQuestion);

        for ($x = 0; $x < 10; $x++) {
            $this->IBanswers[$x] = new Inputbox("answer." . $x, 235);
            $this->IBanswers[$x]->setLabel(__("Answer") . ($x + 1), $login);
            $this->frame->addComponent($this->IBanswers[$x]);
        }

        $this->mainFrame->addComponent($this->frame);

        $this->actionOk = $this->createAction(array($this, "Ok"));
        $this->actionCancel = $this->createAction(array($this, "Cancel"));

        $this->ok = new OkButton();
        $this->ok->colorize("0d0");
        $this->ok->setText(__("Apply", $login));
        $this->ok->setAction($this->actionOk);
        $this->mainFrame->addComponent($this->ok);

        $this->cancel = new OkButton();
        $this->cancel->setText(__("Cancel", $login));
        $this->cancel->setAction($this->actionCancel);
        $this->mainFrame->addComponent($this->cancel);
    }

    function onResize($oldX, $oldY) {
        parent::onResize($oldX, $oldY);

        $this->ok->setPosition($this->sizeX - 38, -$this->sizeY + 6);
        $this->cancel->setPosition($this->sizeX - 20, -$this->sizeY + 6);
    }

    function onShow() {
        
    }

    function setQuestion(\ManiaLivePlugins\eXpansion\Quiz\Structures\Question $question) {
        $this->IBQuestion->setText($question->getQuestion());
        for ($x = 0; $x < 10; $x++) {
            if (isset($question->answer[$x])) {
                $this->IBanswers[$x]->setText($question->answer[$x]->answer);
            }
        }
    }

    function Ok($login, $data) {
        $storage = \ManiaLive\Data\Storage::getInstance();
        $q = str_replace("?", "", $data['question']);
        $question = new \ManiaLivePlugins\eXpansion\Quiz\Structures\Question($storage->getPlayerObject($login), trim($q));
        for ($x = 0; $x < 10; $x++) {
            if (trim($data['answer.' . $x]) != "") {
                $question->addAnswer(trim($data['answer.' . $x]));
            }
        }

        self::$mainPlugin->addQuestion($question);
        $this->erase($login);
    }

    function Cancel($login) {
        $this->erase($login);
    }

    function destroy() {

        $this->ok->destroy();
        $this->cancel->destroy();
        $this->clearComponents();
        parent::destroy();
    }

}

?>
