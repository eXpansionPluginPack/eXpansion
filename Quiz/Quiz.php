<?php

namespace ManiaLivePlugins\eXpansion\Quiz;

use ManiaLive\Features\Admin\AdminGroup;
use ManiaLive\Features\ChatCommand\Command;
use ManiaLive\Utilities\Console;
use ManiaLive\Config\Loader;
use ManiaLive\Event\Dispatcher;

class Quiz extends \ManiaLive\PluginHandler\Plugin {

    /** @var Structures\Question[] */
    private $questionDb = array();

    /** @var Structures\Question */
    private $currentQuestion = null;

    /** @var Structures\QuizPlayer[] */
    private $players = array();
    private $questionCounter = 0;

    /**
     * onInit()
     * Function called on initialisation of ManiaLive.
     *
     * @return void
     */
    public function onInit() {
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
    function onLoad() {
        $this->enableDedicatedEvents();
        $command = $this->registerChatCommand("q", "chatquiz", -1, true);
        $this->registerChatCommand("kysy", "ask", -1, true);
        $this->registerChatCommand("pisteet", "showPointsWindow", 0, true);
        $this->registerChatCommand("piste", "addPointsWindow", 0, true, \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance()->get());
        $this->registerChatCommand("peruuta", "cancel", 0, true);
        $this->registerChatCommand("vastaus", "showAnswer", 0, true);
        $this->registerChatCommand("kysymys", "showQuestion", 0, true);
    }

    function onReady() {
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

    function addQuestion(Structures\Question $question) {
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

        // if ($login == $this->currentQuestion->asker->login)
        //     return; // ignore if answer is from asker
        switch ($this->currentQuestion->checkAnswer($text)) {
            case Structures\Question::Correct:
                $this->connection->chatSendServerMessage("\$0b0 CORRECT! \$fff" . $text);
                $this->addPoint(null, $login);
                $this->currentQuestion = null;
                $this->chooseNextQuestion();
                break;
            case Structures\Question::MoreAnswersNeeded:
                $this->connection->chatSendServerMessage("\$0b0 CORRECT! \$fff" . $text);
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
            $this->connection->chatSendServerMessage($this->storage->getPlayerObject($login)->nickName . '$z$s cancels the question.');
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
        $this->connection->chatSendServerMessage("Quiz has been reset!");
    }

    function showAnswer($login) {
        if (!isset($this->currentQuestion->question))
            return;
        if ($login == $this->currentQuestion->login || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::isInList($login)) {
            $this->connection->chatSendServerMessage("\$3e3\$o No answer for next question:");
            $this->connection->chatSendServerMessage('$z$o$s$fa0' . $this->currentQuestion->question . "?");
            $this->connection->chatSendServerMessage('$0b0$oRight answers: $z$s$fff' . implode(",", $this->currentQuestion->answer));
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
        $this->connection->chatSendServerMessage("\$3e3\$oQuestion!! number: \$fff" . $this->questionCounter . "\$z\$s\$3e3\$o Asker: \$z\$s\$fff" . $nickName);
        $this->connection->chatSendServerMessage("\$z\$o\$s\$fa0" . $question . "?");
    }

    function ask($login, $text = "") {
        $window = Gui\Windows\QuestionWindow::Create($login);
        try {
            if (strlen($text) > 1) {
                echo "kysymys";
                $answerPosition = strpos($text, "?");
                if ($answerPosition == false) {
                    $this->connection->chatSendServerMessage("\$f00Question needs to be at the right format!", $login);
                    return;
                }
                $answer = trim(str_replace("?", "", strstr($text, "?")));
                if ($answer == "") {
                    $this->connection->chatSendServerMessage("\$f00Aswer is missing from the question!", $login);
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
        $this->connection->chatSendServerMessage("Current point holders:");
        $output = "";
        foreach ($this->players as $player) {
            $output .= $player->nickName . '$z$s$fff ' . $player->points . ", ";
        }

        $this->connection->chatSendServerMessage(substr($output, 0, (strlen($output) - 2)));
    }

}

?>