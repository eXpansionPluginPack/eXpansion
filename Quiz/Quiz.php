<?php

namespace ManiaLivePlugins\eXpansion\Quiz;

use ManiaLive\Application\ErrorHandling;
use ManiaLivePlugins\eXpansion\AdminGroups\Permission;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Widget\QuizImageWidget;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Windows\AddPoint;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Windows\HiddenQuestionWindow;
use ManiaLivePlugins\eXpansion\Quiz\Gui\Windows\QuestionWindow;
use ManiaLivePlugins\eXpansion\Quiz\Structures\Question;


class Quiz extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

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

    private $msg_correctAnswer = "";

    private $msg_reset = "";

    private $msg_cancelQuestion = "";

    private $msg_question = "";

    private $msg_questionPre = "";

    private $msg_points = "";

    private $msg_answerMissing = "";

    private $msg_questionMissing = "";

    private $msg_pointAdd = "";

    private $msg_pointRemove = "";

    private $msg_errorImageType = "";

    /** @var \ManiaLivePlugins\eXpansion\Core\DataAccess */
    private $dataAccess = null;

    public static $GDsupport = false;

    /**
     * onInit()
     * Function called on initialisation of ManiaLive.
     *
     * @return void
     */
    public function expOnInit()
    {

    }

    /**
     * onLoad()
     * Function called on loading of ManiaLive.
     *
     * @return void
     */
    public function eXpOnLoad()
    {
        if (!extension_loaded("gd")) {
            if (!(bool)ini_get("enable_dl") || (bool)ini_get("safe_mode")) {
                $phpPath = get_cfg_var('cfg_file_path');
                $this->dumpException(
                    "Autoloading extensions is not enabled in php.ini.\n\n`php_gd2` extension needs to be enabled for systems running this plugin.\n\nEdit following file $phpPath and set:\n\nenable_dl = On\n\nor add this line:\n\nextension=php_gd2.dll",
                    new \Maniaplanet\WebServices\Exception("Loading extensions is not permitted.")
                );
                $this->eXpChatSendServerMessage(
                    "#quiz#Quiz started, php GD2-extension was not not loaded, see console log for more details."
                );
            } else {
                dl("php_gd2");
                self::$GDsupport = true;
            }
        } else {
            self::$GDsupport = true;
        }

        $this->enableDedicatedEvents();
        $this->enableDatabase();

        $this->dataAccess = \ManiaLivePlugins\eXpansion\Core\DataAccess::getInstance();

        $this->db->execute("CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text COLLATE utf8_bin NOT NULL,
  `answers` text COLLATE utf8_bin NOT NULL,
  `asker` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MYISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


        $this->db->execute("CREATE TABLE IF NOT EXISTS `quiz_points` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL DEFAULT '1',
  `login` text COLLATE utf8_bin NOT NULL,  
  `nickName` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ");


        $command = $this->registerChatCommand("ask", "ask", -1, true);
        $command->help = '/ask Ask a question';
        $command = $this->registerChatCommand("kysy", "ask", -1, true);
        $command->help = '/kysy Ask a question';
        $command = $this->registerChatCommand("points", "showPointsWindow", 0, true);
        $command->help = '/points Show points window';
        $command = $this->registerChatCommand("pisteet", "showPointsWindow", 0, true);
        $command->help = '/pisteet Show points window';
        $command = $this->registerChatCommand("tilanne", "showPoints", 0, true);
        $command->help = '/tilanne Show current points';
        $command = $this->registerChatCommand("point", "addPointsWindow", 0, true);
        $command->help = '/piste add a point for any player on server';
        $command = $this->registerChatCommand("piste", "addPointsWindow", 0, true);
        $command->help = '/piste add a point for any player on server';
        $command = $this->registerChatCommand("cancel", "cancel", 0, true);
        $command->help = '/cancel Cancels a question';
        $command = $this->registerChatCommand("peruuta", "cancel", 0, true);
        $command->help = '/peruuta Cancels a question';
        $command = $this->registerChatCommand("answer", "showAnswer", 0, true);
        $command->help = '/answer Show the current right answers for a question';
        $command = $this->registerChatCommand("vastaus", "showAnswer", 0, true);
        $command->help = '/vastaus Show the current right answers for a question';
        $command = $this->registerChatCommand("question", "displayQuestion", 0, true);
        $command->help = '/question Shows the current question again';
        $command = $this->registerChatCommand("kysymys", "displayQuestion", 0, true);
        $command->help = '/question Shows the current question again';
        $command = $this->registerChatCommand("reset", "reset", 0, true);
        $command->help = '/reset resets the quiz points';
        $command = $this->registerChatCommand("nollaa", "reset", 0, true);
        $command->help = '/nollaa resets the quiz points';

        $this->msg_questionPre = eXpGetMessage("#quiz#Question number:#variable# %s$1 #quiz#    Asker:#variable# %s$2");
        $this->msg_question = eXpGetMessage("#question#%s?");
        $this->msg_format = eXpGetMessage("#error#Question needs to be at the right format!");
        $this->msg_reset = eXpGetMessage("#quiz#Quiz has been reset!");
        $this->msg_correct = eXpGetMessage("Correct from");
        $this->msg_correctAnswer = eXpGetMessage(
            "#quiz#Correct! #question# %s$1 #quiz# Well Done,#variable# %s$2 #quiz#!"
        );
        $this->msg_rightAnswer = eXpGetMessage('#quiz#Right answers: $o#question#%s');
        $this->msg_answerMissing = eXpGetMessage("#error#Aswer is missing from the question!");
        $this->msg_questionMissing = eXpGetMessage("#error#Question is missing from the question!");
        $this->msg_cancelQuestion = eXpGetMessage('%s #quiz#cancels the question.');
        $this->msg_points = eXpGetMessage("#quiz#Current point holders:");
        $this->msg_pointAdd = eXpGetMessage('#quiz#$oPoint added for #variable#%s');
        $this->msg_pointRemove = eXpGetMessage('#quiz#$oPoint removed from #variable#%s');
        $this->msg_errorImageType = eXpGetMessage(
            '#quiz#$Displaying image not possible, due unsupported media type was detected.'
        );
    }

    public function eXpOnReady()
    {
        if (!extension_loaded("mbstring")) {
            $this->dumpException(
                "Plugin init failed!\nQuiz plugin needs 'mbstring' extension to be loaded!\n Please add the extension to to php for loading this plugin!",
                new \Exception("php_mbstring extension not loaded")
            );
            $adm = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();
            $adm->announceToPermission(
                Permission::EXPANSION_PLUGIN_START_STOP,
                "\$d00Quiz plugin needs 'mbstring' extension to be added in php extensions! Plugin not loaded."
            );
            $this->eXpUnload();
        }

        Gui\Windows\QuestionWindow::$mainPlugin = $this;
        Gui\Windows\Playerlist::$mainPlugin = $this;
        Gui\Windows\AddPoint::$mainPlugin = $this;
        Gui\Widget\QuizImageWidget::EraseAll();

        $data = $this->db->execute("SELECT * FROM `quiz_points` order by score desc;")->fetchArrayOfObject();
        foreach ($data as $player) {
            $this->players[$player->login] = new Structures\QuizPlayer(
                $player->login,
                $player->nickName,
                (int)$player->score
            );
        }

        $this->setPublicMethod("ask");

    }

    public function chatquiz($login, $args)
    {
        $args = explode(" ", $args);
        $action = array_shift($args);
        $message = implode(" ", $args);

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
     *
     * @param \ManiaLivePlugins\eXpansion\Quiz\Structures\Question $question
     */
    public function addQuestion(Structures\Question $question)
    {
        if (empty($question->question)) {
            $this->eXpChatSendServerMessage($this->msg_questionMissing, $question->asker->login);
            return;
        }

        if (sizeof($question->answer) == 0) {
            $this->eXpChatSendServerMessage($this->msg_answerMissing, $question->asker->login);
            return;
        }
        try {
            $dbanswer = "";
            foreach ($question->answer as $answer) {
                $dbanswer .= $answer->answer . ", ";
            }
            $dbanswer = trim($dbanswer, ", ");
            $this->db->execute(
                "INSERT INTO `quiz_questions` (question, answers, asker) VALUES ("
                . $this->db->quote($question->question)
                . ", " . $this->db->quote($dbanswer) . ", " . $this->db->quote($question->asker->login) . ");"
            );
        } catch (\Exception $e) {
            $this->console($e->getMessage());
            ErrorHandling::displayAndLogError($e);
            ErrorHandling::logError($e);
            // silent exception
        }
        $this->questionDb[] = $question;
        $this->chooseNextQuestion($question->asker->login);
    }

    public function onPlayerChat($playerUid, $login, $text, $isRegistredCmd)
    {
        if ($playerUid == 0) {
            return;
        }

        if (substr($text, 0, 1) == "/") {
            return;
        }

        if (!isset($this->currentQuestion->question)) {
            return;
        }

        if ($login == $this->currentQuestion->asker->login) {
            return;
        } // ignore if answer is from asker

        switch ($this->currentQuestion->checkAnswer($text)) {
            case Structures\Question::Correct:
                $player = $this->storage->getPlayerObject($login);
                $nicklen = strlen(\ManiaLib\Utils\Formatting::stripColors($player->nickName));
                $header = '#quiz#$o' . str_repeat("*", 45);
                $this->connection->chatSendServerMessage($this->colorParser->parseColors($header));
                $answer = "";
                foreach ($this->currentQuestion->answer as $ans) {
                    $answer .= " " . $ans->answer . ",";
                }
                $answer = trim($answer, ", ");
                if ($this->currentQuestion->hasImage()) {
                    $answer = '$l[' . $this->currentQuestion->imageUrl . ']' . $answer . '$l';
                }
                $this->eXpChatSendServerMessage($this->msg_correctAnswer, null, array($answer, $player->nickName));
                $this->connection->chatSendServerMessage($this->colorParser->parseColors($header));

                $this->addPoint(null, $login);
                $this->currentQuestion = null;
                $widget = QuizImageWidget::GetAll();
                foreach ($widget as $window) {
                    $window->revealAnswer();
                    $window->redraw();
                }
                $this->chooseNextQuestion();
                break;
            case Structures\Question::MoreAnswersNeeded:
                $this->eXpChatSendServerMessage($this->msg_correct, null, array($text));
                $this->addPoint(null, $login);
                break;
            default:
                break;
        }
    }

    /**
     *
     * @return QuizPlayer[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    public function cancel($login)
    {
        Gui\Widget\QuizImageWidget::EraseAll();

        if ($this->currentQuestion === null) {
            return;
        }

        if ($this->currentQuestion->asker->login == $login
            || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)
        ) {
            $this->eXpChatSendServerMessage(
                $this->msg_cancelQuestion,
                null,
                array($this->storage->getPlayerObject($login)->nickName)
            );
            $this->currentQuestion = null;
            $this->chooseNextQuestion();
        }
    }

    public function reset($login)
    {
        if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)) {
            return;
        }
        $this->players = array();
        $this->questionDb = array();
        $this->currentQuestion = null;
        $this->questionCounter = 0;
        $this->db->execute('TRUNCATE TABLE`quiz_points`;');
        $this->eXpChatSendServerMessage($this->msg_reset);
        Gui\Widget\QuizImageWidget::EraseAll();
    }

    public function showAnswer($login)
    {
        if (!isset($this->currentQuestion->question)) {
            return;
        }
        if ($login == $this->currentQuestion->asker->login
            || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)
        ) {
            $answer = "";

            foreach ($this->currentQuestion->answer as $ans) {
                $answer .= " " . $ans->answer . ",";
            }
            $answer = trim($answer, ", ");
            if ($this->currentQuestion->hasImage()) {
                $answer = '$l[' . $this->currentQuestion->imageUrl . ']' . $answer . '$l';
            }
            $this->eXpChatSendServerMessage($this->msg_rightAnswer, null, array($answer));

            $this->currentQuestion = null;
            Gui\Widget\QuizImageWidget::EraseAll();
            $this->chooseNextQuestion();
        }
    }

    public function chooseNextQuestion($login = null)
    {

        if ($this->questionDb == null) {
            return;
        }

        if (isset($this->currentQuestion->question)) {
            //Show info to asker that there is already question active and the new question was added to the queue
            if ($login != null) {
                $this->eXpChatSendServerMessage(
                    "Question added to the queue. (There is already an existing question.)",
                    $login
                );
            }
            return;
        }

        if (count($this->questionDb) >= 1) {
            $this->currentQuestion = array_shift($this->questionDb);
            $this->questionCounter++;
            Gui\Widget\QuizImageWidget::EraseAll();

            $this->showQuestion();
        } else {
            $this->currentQuestion = null;
            $this->questionDb = null;
            Gui\Widget\QuizImageWidget::EraseAll();
        }
    }

    public function addPoint($login = null, $target)
    {
        if ($login == null
            || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)
        ) {
            if (!isset($this->players[$target])) {
                $this->players[$target] = new Structures\QuizPlayer(
                    $target,
                    $this->storage->getPlayerObject($target)->nickName,
                    1
                );
                if ($login !== null) {
                    $this->eXpChatSendServerMessage(
                        $this->msg_pointAdd,
                        null,
                        array($this->players[$target]->nickName)
                    );
                }
                $this->showPoints();
            } else {
                $this->players[$target]->points++;
                if ($login !== null) {
                    $this->eXpChatSendServerMessage(
                        $this->msg_pointAdd,
                        null,
                        array($this->players[$target]->nickName)
                    );
                }
                $this->showPoints();
            }
            $count = $this->db->execute(
                "SELECT * FROM `quiz_points` where login = "
                . $this->db->quote($target) . " LIMIT 1;"
            )->recordCount();
            if ($count) {
                $this->db->execute(
                    "UPDATE `quiz_points` SET `score` = " . $this->db->quote($this->players[$target]->points)
                    . " where `login` = " . $this->db->quote($target) . ";"
                );
            } else {
                $this->db->execute(
                    "INSERT INTO `quiz_points` (login,nickName,score) values(" . $this->db->quote($target) . ", "
                    . $this->db->quote($this->storage->getPlayerObject($target)->nickName) . ", "
                    . $this->db->quote($this->players[$target]->points) . ");"
                );
            }
        }
    }

    public function removePoint($login, $target)
    {

        if ($login == null
            || \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)
        ) {
            if (isset($this->players[$target])) {
                if ($login !== null) {
                    $this->eXpChatSendServerMessage(
                        $this->msg_pointRemove,
                        null,
                        array($this->players[$target]->nickName)
                    );
                }
                $this->players[$target]->points--;
                $count = $this->db->execute(
                    "SELECT * FROM `quiz_points` where login = " . $this->db->quote($target)
                    . " LIMIT 1;"
                )->recordCount();
                if ($count) {
                    $this->db->execute(
                        "UPDATE `quiz_points` SET `score` = " . $this->db->quote($this->players[$target]->points)
                        . " where `login` = " . $this->db->quote($target) . ";"
                    );
                } else {
                    $this->db->execute(
                        "INSERT INTO `quiz_points` (login,nickName,score) values(" . $this->db->quote($target) . ", "
                        . $this->db->quote($this->storage->getPlayerObject($target)->nickName) . ", "
                        . $this->db->quote($this->players[$target]->points) . ");"
                    );
                }
                $this->showPoints();
            }
        }
    }

    public function setHiddenQuestionBoxes(Question $question)
    {
        if (self::$GDsupport) {
            $this->dataAccess->httpCurl(
                $question->getImage(),
                array($this, "xGetHiddenImage"),
                array("question" => $question)
            );
        } else {
            $this->eXpChatSendServerMessage(
                "#quiz#Hidden Questions can be only asked with GD Support",
                $question->asker->login
            );
        }

    }


    public function displayQuestion()
    {
        $this->showQuestion(false);
    }

    public function showQuestion($redraw = true)
    {
        if ($this->currentQuestion !== null) {
            $nickName = $this->currentQuestion->asker->nickName;
            $question = $this->currentQuestion->question;
            if ($this->currentQuestion->hasImage() && $redraw) {
                if (self::$GDsupport) {

                    $this->dataAccess->httpCurl($this->currentQuestion->getImage(), array($this, "xGetImage"));
                } else {
                    $widget = Gui\Widget\QuizImageWidget::Create(null);
                    $widget->setImage($this->currentQuestion->getImage());
                    $widget->setHiddenQuestion($this->currentQuestion->isHidden, $this->currentQuestion->boxOrder);
                    $widget->setImageSize(20, 11.25);
                    $widget->show();
                }
            }
            $this->eXpChatSendServerMessage($this->msg_questionPre, null, array($this->questionCounter, $nickName));
            $this->eXpChatSendServerMessage($this->msg_question, null, array($question));
        }

    }

    public function xGetImage($job, $jobData)
    {

        $info = $job->getCurlInfo();
        $httpCode = $info['http_code'];


        if ($httpCode != 200) {
            $this->eXpChatSendServerMessage("#error#Quiz encountered http error: code " . $httpCode);
            return;
        }

        $data = $job->getResponse();

        $maxWidth = 20;
        $maxHeight = 20;


        $meta = array();
        list($width, $height, $type, $attr) = @getimagesizefromstring($data, $meta);

        if (($type == IMAGETYPE_JPEG) || ($type == IMAGETYPE_PNG)) {
            $xRatio = $maxWidth / $width;
            $yRatio = $maxHeight / $height;

            $newHeight = 20.0;
            $newWidth = 20.0;

            if (($xRatio * $height) < $maxHeight) {
                $newHeight = $xRatio * $height;
                $newWidth = $maxWidth;
            } else {
                $newWidth = $yRatio * $width;
                $newHeight = $maxHeight;
            }

            $widget = Gui\Widget\QuizImageWidget::Create(null);

            $widget->setImage($this->currentQuestion->getImage());
            $widget->setHiddenQuestion($this->currentQuestion->isHidden, $this->currentQuestion->boxOrder);
            $widget->setImageSize($newWidth, $newHeight);
            $widget->show();
        } else {

            $this->eXpChatSendServerMessage($this->msg_errorImageType);
        }
    }

    public function xGetHiddenImage($job, $jobData)
    {
        $info = $job->getCurlInfo();
        $httpCode = $info['http_code'];

        $additionalData = $job->__additionalData;
        $question = $additionalData['question'];

        if ($httpCode != 200) {
            $this->eXpChatSendServerMessage("#error#Quiz encountered http error: code " . $httpCode);
            return;
        }

        $data = $job->getResponse();

        $maxWidth = 60;
        $maxHeight = 60;

        $meta = array();
        list($width, $height, $type, $attr) = @getimagesizefromstring($data, $meta);

        if (($type == IMAGETYPE_JPEG) || ($type == IMAGETYPE_PNG)) {
            $xRatio = $maxWidth / $width;
            $yRatio = $maxHeight / $height;

            if (($xRatio * $height) < $maxHeight) {
                $newHeight = $xRatio * $height;
                $newWidth = $maxWidth;
            } else {
                $newWidth = $yRatio * $width;
                $newHeight = $maxHeight;
            }

            $win = HiddenQuestionWindow::Create($question->asker->login);
            $question->setImageSize($newWidth, $newHeight);
            $win->setQuestion($question);
            $win->setMain($this);
            $win->setSize(90, 90);
            $win->show();
        } else {
            $this->eXpChatSendServerMessage($this->msg_errorImageType);
        }
    }


    public function ask($login, $text = "")
    {
        $window = Gui\Windows\QuestionWindow::Create($login);
        try {
            if (strlen($text) > 1) {
                $answerPosition = strpos($text, "?");
                if ($answerPosition == false) {
                    $this->eXpChatSendServerMessage($this->msg_format, $login);

                    return;
                }
                $answer = trim(str_replace("?", "", strstr($text, "?")));
                if ($answer == "") {
                    $this->eXpChatSendServerMessage($this->msg_answerMissing, $login);

                    return;
                }

                $question = new Structures\Question(
                    $this->storage->getPlayerObject($login),
                    trim(substr($text, 0, $answerPosition))
                );
                $answers = explode(",", $answer);

                foreach ($answers as $ans) {
                    $question->addAnswer(trim($ans));
                }
                $window->setQuestion($question);
            }

            $window->setSize(90, 120);
            $window->centerOnScreen();
            $window->setTitle(__("New question", $login));
            $window->Show();
        } catch (\Exception $e) {
            $this->console("Error when asking : " . $e->getMessage());
        }
    }

    public function showPointsWindow($login)
    {
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->players, "points");
        $window = Gui\Windows\Playerlist::Create($login);
        $window->setTitle("Point Holders");
        $window->setSize(90, 60);
        $window->centerOnScreen();
        $window->Show();
    }

    public function addPointsWindow($login)
    {
        if (!\ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::hasPermission($login, Permission::QUIZ_ADMIN)) {
            return;
        }
        $window = Gui\Windows\AddPoint::Create($login);
        $window->setSize(90, 60);
        $window->centerOnScreen();
        $window->setTitle("Add point to player");
        $window->Show();
    }

    public function showPoints($login = null)
    {
        \ManiaLivePlugins\eXpansion\Helpers\ArrayOfObj::asortDesc($this->players, "points");
        $this->eXpChatSendServerMessage($this->msg_points);
        $output = "";
        foreach ($this->players as $player) {
            if ($player->points > 0) {
                $output .= '$z$s$o$ff0' . $player->points . ' $z$s$fff' . $player->nickName . '   $z$s$fff|   ';
            }
        }

        $this->connection->chatSendServerMessage(substr($output, 0, (strlen($output) - 2)));
    }

    public function eXpOnUnload()
    {
        AddPoint::EraseAll();
        Gui\Windows\Playerlist::EraseAll();
        QuestionWindow::EraseAll();
        Gui\Widget\QuizImageWidget::EraseAll();
    }
}
