<?php
/**
 * Created by PhpStorm.
 * User: Käyttäjä
 * Date: 5.2.2017
 * Time: 13:35
 */

require "../../../../libraries/autoload.php";
$markdown = new \Ciconia\Ciconia(new \ManiaLivePlugins\eXpansion\Helpers\Markdown\ManialinkRenderer());

$markdown->addExtension(new \ManiaLivePlugins\eXpansion\Helpers\Markdown\LineExtension());
$markdown->removeExtension("paragraph");
$markdown->removeExtension("code");

$file = file_get_contents("adminguide.md");

$file = str_replace("<br>", "\n", $file);

echo $markdown->render($file);
