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
    
	function isCLI()
	{
		return php_sapi_name() == "cli";
	}
	
	function isWebStart()
	{
		return !empty($_GET['argv']) AND !empty($_GET['argv'][0]) AND ($_GET['argv'][0]=='web_start');
	}	
	
	function progressBar($done, $total, $maxWidth=25)
	{
		$perc = ceil(($done / $total) * 100);
		$percDraw = floor($perc/ceil(100/$maxWidth));
		$bar = "[" . ($percDraw > 0 ? str_repeat("=", $percDraw - 1) : "") . ">";
		$spacesFillLength = $maxWidth - $percDraw;
		$bar .= str_repeat(" ", $spacesFillLength) . "] - $perc% - $done/$total";
		//echo "\033[0G$bar"; // Note the \033[0G. Put the cursor at the beginning of the line
		echo "\r$bar";
	}		
