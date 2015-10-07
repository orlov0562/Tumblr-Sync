<?php

    function RunLock($lockFile, $exitText='Script is already running. Exiting..')
    {
        $fp = fopen($lockFile, 'w');
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            return $fp;
        } else {
            die($exitText.PHP_EOL);
        }
    }
