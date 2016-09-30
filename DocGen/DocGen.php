<?php

namespace ManiaLivePlugins\eXpansion\DocGen;

class DocGen extends \ManiaLivePlugins\eXpansion\Core\types\ExpPlugin
{

    function eXpOnReady()
    {
        $buffer = "<!doctype html>
<html>
    <head>
    <title>eXpansion documentor</title>
    </head>        
    <style>
    table tr:nth-child(odd) {
     background: #eee;
    }
    
    #morehelp {
        font-size: small;
        font-color: #999;
    }
    </style>
<body>        
";
        $adminGr = \ManiaLivePlugins\eXpansion\AdminGroups\AdminGroups::getInstance();

        $buffer .= "<h1>Admin Commands</h1>\n";
        $buffer .= "<table>
            <tr>
            <th>Command</th><th>Aliases</th><th>Help</th>            
            </tr>
";
        foreach ($adminGr->getAdminCommands() as $cmd) {
            $help = __($cmd->getHelp());
            $help = \ManiaLib\Utils\Formatting::stripStyles($help);
            $helpmore = __($cmd->getHelpMore());
            $helpmore = \ManiaLib\Utils\Formatting::stripStyles($helpmore);
            $buffer .= '<tr><td>/admin ' . $cmd->getCmd() . '</td><td>' . implode("<br/>", $cmd->getAliases())
                . '</td><td>' . $help . '<br/><div id="morehelp">' . $helpmore . '</div></td></tr>';
        }
        unset($cmd);

        $buffer .= "</table>\n";
        $buffer .= "<h1>All Public commands</h1>\n";
        $buffer .= "<table>
            <tr>
            <th>Command</th><th>Help</th>            
            </tr>";
        $chatcommands = \ManiaLive\Features\ChatCommand\Interpreter::getInstance();
        foreach ($chatcommands->getRegisteredCommands() as $commands) {
            foreach ($commands as $cmd) {
                if ($cmd->isPublic) {
                    $buffer .= '<tr><td>/' . $cmd->name . '</td><td>' . $cmd->help . '</td></tr>';
                }
            }
        }

        $buffer .= "</table>\n";
        $buffer .= "</body>
</html>            
";
        file_put_contents(__DIR__ . "/Docs/index.htm", $buffer);
    }
}
