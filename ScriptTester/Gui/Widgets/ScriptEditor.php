<?php

namespace ManiaLivePlugins\eXpansion\ScriptTester\Gui\Widgets;

use ManiaLivePlugins\eXpansion\Gui\Elements\Button;
use ManiaLivePlugins\eXpansion\Gui\Elements\Inputbox;
use ManiaLivePlugins\eXpansion\Gui\Elements\TextEdit;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetBackGround;
use ManiaLivePlugins\eXpansion\Gui\Elements\WidgetTitle;
use ManiaLivePlugins\eXpansion\Gui\Structures\Script;
use ManiaLivePlugins\eXpansion\Gui\Widgets\PlainWidget;
use ManiaLivePlugins\eXpansion\ScriptTester\Config;

/**
 * Description of ScriptEditor
 *
 * @author Petri
 */
class ScriptEditor extends PlainWidget
{

    protected $scriptEditor, $mlEditor, $lbl_ml, $lbl_script, $background, $btn_apply, $btn_close;
    protected $input_script, $input_ml, $script, $actionId;


    protected function onConstruct()
    {
        parent::onConstruct();
        $config = Config::getInstance();
        $this->setName("Script Editor");
        $this->setScale(0.75);
        $this->setPosition(-150, -10);

        $this->background = new WidgetBackGround(395, 75);
        $this->addComponent($this->background);

        $this->lbl_ml = new WidgetTitle(180, 4);
        $this->lbl_ml->setPosition(12);
        $this->lbl_ml->setText('Maniascript editor');
        $this->addComponent($this->lbl_ml);

        $entry = new TextEdit("editor_maniascript", 180, 60);
        $entry->setId('editor_maniascript');
        $entry->setPosition(12, -5);
        $entry->setAlign("left", "top");
        $entry->setAutoNewline();
        $entry->setShowLineNumbers();
        $entry->setTextFormat("script");
        $entry->setScriptEvents();
        $entry->setText($config->tester_maniascript);
        $this->scriptEditor = $entry;
        $this->addComponent($this->scriptEditor);

        $this->lbl_script = new WidgetTitle(180, 4);
        $this->lbl_script->setPosX(210);
        $this->lbl_script->setText('Manialink editor');
        $this->addComponent($this->lbl_script);

        $entry = new TextEdit("editor_manialink", 180, 60);
        $entry->setId('editor_manialink');
        $entry->setPosition(210, -5);
        $entry->setAlign("left", "top");
        $entry->setAutoNewline();
        $entry->setShowLineNumbers();
        $entry->setTextFormat("script");
        $entry->setScriptEvents();
        $entry->setText($config->tester_manialink);
        $this->mlEditor = $entry;
        $this->addComponent($this->mlEditor);

        $this->btn_apply = new Button(40);
        $this->btn_apply->setId("apply");
        $this->btn_apply->setPosition(190, -70);
        $this->btn_apply->colorize('0d0');
        $this->btn_apply->setText("Save & Test");
        $this->addComponent($this->btn_apply);

        $this->btn_close = new Button();
        $this->btn_close->setPosition(365, -70);
        $this->btn_close->colorize('d00');
        $this->btn_close->setText("Close");
        $this->btn_close->setAction($this->createAction(array($this, 'close')));
        $this->addComponent($this->btn_close);

        $this->input_ml = new Inputbox('manialink', 100);
        $this->input_ml->setId('input_manialink');
        $this->input_ml->setPosition(10, 2000);
        $this->input_ml->setScriptEvents();
        $this->addComponent($this->input_ml);

        $this->input_script = new Inputbox('script', 100);
        $this->input_script->setId('input_maniascript');
        $this->input_script->setPosition(120, 2000);
        $this->input_script->setScriptEvents();
        $this->addComponent($this->input_script);

        $this->script = new Script("ScriptTester/Gui/Scripts/Editor");
        $this->registerScript($this->script);
    }

    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
        $this->script->setParam('actionId', $this->actionId);
    }

    public function close($login)
    {
        $this->closeWindow();
    }

}
