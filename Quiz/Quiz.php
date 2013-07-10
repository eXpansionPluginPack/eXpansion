<?php

namespace ManiaLivePlugins\eXpansion\Quiz;

use ManiaLive\Features\Admin\AdminGroup;
use ManiaLive\Features\ChatCommand\Command;
use ManiaLive\Utilities\Console;
use ManiaLive\Config\Loader;
use ManiaLive\Event\Dispatcher;

class Quiz extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin {

    /** @var Structures\Question[] */
    private $questionDb = array();

    /** @var Structures\Question */
    private $currentQuestion = null;

    /** @var Structures\QuizPlayer[] */
    private $players = array();

    /** @var int */
    private $questionCounter = 0;
    private $msg_format = "";
    private $msg_rightAnswer = "";
    private $msg_correct = "";
    private $msg_reset = "";
    private $msg_cancelQuestion = "";
    private $msg_question = "";
    private $msg_error = "";
    private $msg_questionPre = "";
    private $msg_points = "";
    private $msg_answerMissing = "";
    private $msg_questionMissing = "";

    /**
     * onInit()
     * Function called on initialisation of ManiaLive.
     *
     * @return void
     */
    public function exp_onInit() {
        //Oliverde8 Menu
        if ($this->isPluginLoaded('oliverde8\HudMenu')) {
            Dispatcher::register(\ManiaLivePlugins\oliverde8\HudMenu\onOliverde8HudMenuReady::getClass(), $this);
        }
    }

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    function exp_onLoad() {
        $this->enableDedicatedEvents();

        /** @var \ManiaLivePlugins\eXpansion\Core\ColorParser */
        $color = \ManiaLivePlugins\eXpansion\Core\ColorParser::getInstance();
        $color->registerCode("quiz", '$z$s$3e3');
        $color->registerCode("question", '$z$s$o$fa0');


        $command = $this->registerChatCommand("q", "chatquiz", -1, true);
        $this->registerChatCommand("kysy", "ask", -1, true);
        $this->registerChatCommand("pisteet", "showPointsWindow", 0, true);
        $this->registerChatCommand("piste", "addPointsWindow", 0, true, \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance()->get());
        $this->registerChatCommand("peruuta", "cancel", 0, true);
        $this->registerChatCommand("vastaus", "showAnswer", 0, true);
        $this->registerChatCommand("kysymys", "showQuestion", 0, true);

        $this->msg_questionPre = exp_getMessage("#quiz#Question number:#variable# %s$1 #quiz#    Asker:#variable# %s$2");
        $this->msg_question = exp_getMessage("#question#%s ?");
        $this->msg_format = exp_getMessage("#error#Question needs to be at the right format!");
        $this->msg_reset = exp_getMessage("#quiz#Quiz has been reset!");
        $this->msg_correct = exp_getMessage("Correct from");
        $this->msg_rightAnswer = exp_getMessage('#quiz#Right answers: #variable#%s');
        $this->msg_answerMissing = exp_getMessage("#error#Aswer is missing from the question!");
        $this->msg_questionMissing = exp_getMessage("#error#Question is missing from the question!");
        $this->msg_cancelQuestion = exp_getMessage('%s #quiz#cancels the question.');
        $this->msg_points = exp_getMessage("#quiz#Current point holders:");
    }

    function exp_onReady() {
        Gui\Windows\QuestionWindow::$mainPlugin = $this;
        Gui\Windows\Playerlist::$mainPlugin = $this;
        Gui\Windows\AddPoint::$mainPlugin = $this;
    }

    public function onOliverde8HudMenuReady($menu) {
        $button["style"] = "Icons64x64_1";
        $button["substyle"] = "ToolRoot";
        $parent2 = $menu->findButton(array('menu', 'Extras'));
        if (!$parent2) {
            $parent2 = $menu->addButton('menu', "Extras", $button);
        }

        unset($button["style"]);
        unset($button["substyle"]);

        $parent = $menu->findButton(array('menu', "Extras", 'Quiz'));
        if (!$parent) {
            $parent = $menu->addButton($parent2, "Quiz", $button);
        }

        $button["chat"] = "q points";
        $menu->addButton($parent, "Points", $button);

        $button["chat"] = "q cancel";
        $menu->addButton($parent, "Cancel", $button);

        $button["chat"] = "q show";
        $menu->addButton($parent, "Show", $button);

        $button["chat"] = "q reset";
        $menu->addButton($parent, "Reset", $button);
    }

    function chatquiz($login, $args) {
        $args = explode(" ", $args);
        $action = array_shift($args);
        $message = implode(" ", $args);

        $player = $this->storage->getPlayerObject($login);
        switch ($action) {
            case 'ask':
                $this->ask($login, $message);
                break;
            case 'points':
                $this->showPoints($login);
                break;
            case 'cancel':
                $this->cancel($login);
                break;
            case 'show':
                $this->showQuestion();
                break;
            case 'addpoint':
                $this->addPoint($login, $message);
                break;
            case 'reset':
                $this->reset($login);
                break;
        }
    }

    /**
     * addQuestion($question)
     * Adds question to queue
     * @param \ManiaLivePlugins\eXpansion\Quiz\Structures\Question $question     
     */
    function addQuestion(Structures\Question $question) {
        if (empty($question->question)) {
            $this->exp_chatSendServerMessage($this->msg_questionMissing, $question->asker->login);
            return;
        }
        if (sizeof($question->answer) == 0) {
            $this->exp_chatSendServerMessage($this->msg_answerMissing, $question->asker->login);
            return;
        }

        $this->questionDb[] = $question;
        $this->chooseNextQuestion();
    }

    function onPlayerChat($playerUid, $login, $text, $isRegistredCmd) {
        if ($playerUid == 0)
            return;

        if (substr($text, 0, 1) == "/")
            return;

        if (!isset($this->currentQuestion->question))
            return;

        //if ($login == $this->currentQuestion->asker->login)
          //  return; // ignore if answer is from asker
        
        switch ($this->currentQuestion->checkAnswer($text)) {
            case Structures\Question::Correct:                
                $player =$this->storage->getPlayerObject($login);
                $nicklen = strlen(\ManiaLib\Utils\Formatting::stripColors($player->nickName));
                $starCount = floor((50 - $nicklen) / 2);
                
                $header = '$o$af0'. str_repeat("*", $starCount-1).' $z$s$fff'. $player->nickName .' $z$o$s$af0'.str_repeat("*", $starCount-1);
                $this->connection->chatSendServerMessage($header);                
                $this->connection->chatSendServerMessage('$o'.$text);
                $this->connection->chatSendServerMessage('$o$af0'.  str_repeat('*', 45));
                $this->addPoint(null, $login);
                $this->currentQuestion = null;
                $this->chooseNextQuestion();
                break;
            case Structures\Question::MoreAnswersNeeded:
                $this->exp_chatSendServerMessage($this->msg_correct, null, array($text));
                $this->addPoint(null, $login);
                break;
            default:
                break;
        }
    }

    public function getPlayers() {
        return $this->players;
    }

    function cancel($login) {
        if ($this->currentQuestion === null)
            return;

        if ($this->currentQuestion->asker->login == $login || $this->mlepp->AdminGroup->hasPermission($fromLogin, 'admin')) {
            $this->exp_chatSendServerMessage($this->msg_cancelQuestion, null, array($this->storage->getPlayerObject($login)->nickName));
            $this->currentQuestion = null;
            $this->chooseNextQuestion();
        }
    }

    function reset($login) {
        if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login))
            return;
        $this->players = array();
        $this->questionDb = array();
        $this->currentQuestion = null;
        $this->questionCounter = 0;
        $this->exp_chatSendServerMessage($this->msg_reset);
    }

    function showAnswer($login) {
        if (!isset($this->currentQuestion->question))
            return;
        if ($login == $this->currentQuestion->asker->login || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
            $answer = "";

            foreach ($this->currentQuestion->answer as $ans) {
                $answer .= " " . $ans->answer . ",";
            }
            $answer = trim($answer, ", ");
            $this->exp_chatSendServerMessage($this->msg_rightAnswer, null, array($answer));

            $this->currentQuestion = null;
            $this->chooseNextQuestion();
        }
    }

    function chooseNextQuestion() {
        if ($this->questionDb == null)
            return;

        if (isset($this->currentQuestion->question))
            return;

        if (count($this->questionDb) <= 1) {
            $this->currentQuestion = array_shift($this->questionDb);
            $this->questionCounter++;
            $this->showQuestion();
        } else {
            $this->currentQuestion = null;
            $this->questionDb = null;
        }
    }

    function addPoint($login, $target) {
        if ($login == null || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin")) {
            if (!isset($this->players[$target])) {
                $this->players[$target] = new Structures\QuizPlayer($target, $this->storage->getPlayerObject($target)->nickName, 1);
                $this->showPoints();
            } else {
                $this->players[$target]->points++;
                $this->showPoints();
            }
        }
    }

    function removePoint($login, $target) {
        if ($login == null || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, "admin")) {
            if (isset($this->players[$target])) {
                $this->players[$target]->points--;
                $this->showPoints();
            }
        }
    }

    function showQuestion() {
        $nickName = $this->currentQuestion->asker->nickName;
        $question = $this->currentQuestion->question;
        $this->exp_chatSendServerMessage($this->msg_questionPre, null, array($this->questionCounter, $nickName));
        $this->exp_chatSendServerMessage($this->msg_question, null, array($question));
    }

    function ask($login, $text = "") {
        $window = Gui\Windows\QuestionWindow::Create($login);
        try {
            if (strlen($text) > 1) {
                $answerPosition = strpos($text, "?");
                if ($answerPosition == false) {
                    $this->exp_chatSendServerMessage($this->msg_format, $login);
                    return;
                }
                $answer = trim(str_replace("?", "", strstr($text, "?")));
                if ($answer == "") {
                    $this->exp_chatSendServerMessage($this->msg_answerMissing, $login);
                    return;
                }

                $question = new Structures\Question($this->storage->getPlayerObject($login), trim(substr($text, 0, $answerPosition)));
                $answers = explode(",", $answer);

                foreach ($answers as $ans) {
                    $question->addAnswer(trim($ans));
                }
                $window->setQuestion($question);
            }

            $window->setSize(240, 120);
            $window->centerOnScreen();
            $window->setTitle(__("New question", $login));
            $window->Show();
        } catch (\Exception $e) {
            echo $e->getFile() . ":" . $e->getLine();
        }
    }

    function showPointsWindow($login) {
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->players, "points");
        $window = Gui\Windows\Playerlist::Create($login);
        $window->setData($this->players);
        $window->setSize(90, 60);
        $window->centerOnScreen();
        $window->setTitle("Point Holders");
        $window->Show();
    }

    function addPointsWindow($login) {
        $window = Gui\Windows\AddPoint::Create($login);
        $window->setSize(90, 60);
        $window->centerOnScreen();
        $window->setTitle("Add point to player");
        $window->Show();
    }

    function showPoints($login = null) {
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->players, "points");
        $this->exp_chatSendServerMessage($this->msg_points);
        $output = "";
        foreach ($this->players as $player) {
            $output .= $player->nickName . '$z$s$fff ' . $player->points . ", ";
        }

        $this->connection->chatSendServerMessage(substr($output, 0, (strlen($output) - 2)));
    }

}

?>