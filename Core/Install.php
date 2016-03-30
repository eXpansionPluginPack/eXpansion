<?php

namespace ManiaLivePlugins\eXpansion\Core;

class Install {

    public function postUpdate() {
        $badWords = file_get_contents('https://github.com/shutterstock/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words/archive/master.zip');

        if (!is_dir('tmp')) {
            mkdir('tmp', 0777, true);
        }

        if (!is_dir('config/bad_words')) {
            mkdir('config/bad_words', 0777, true);
        }

        file_put_contents('tmp/tmp.zip', $badWords);

        $zip = new \ZipArchive();
        $zip->open('tmp/tmp.zip');
        $zip->extractTo('config/bad_words');
    }
}

?>