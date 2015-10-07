<?php
	/* ---------------------------------------------------------------------------------------
	 * Script that syncing images and videos with your or any other Tumblr Blog
     *
	 * Author: Vitaliy Orlov | https://github.com/orlov0562
	 * License: Haha, you can do with this code anything that you want, even fap-fap. But
	 *          keep in mind that you do it on your own risk, so at least do not do it in 
	 *          public places :)
	 *
	 * Initial configuration:
	 * - rename /app/config.orig.php -> /app/config.php
	 * - fillin api keys in /app/config.php
	 * - make sure that user from which script running have rights to write to script folder
	 *
	 * Pay attention that at this moment it download new files only, if you	delete some downloaded
	 * items (post with video or image) from your blog, this script DO NOT automaticaly delete it
	 * from your PC
	 *
	 * Examples of cron configuration
	 *	0 * * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php 
	 *	0 * * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php images	 
	 *	0 0 * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php videos	 
	 *	0 0 * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php images fluffy-kittens 	 
	 *
	 * If you need more information you may find this links usefull:
	 * - https://github.com/tumblr/tumblr.php
	 * - http://developers.tumblr.com
	 * - https://www.tumblr.com/docs/en/api/v2
	 * - https://www.tumblr.com/oauth/apps
	 * - https://api.tumblr.com/console/calls/user/info
	 * ---------------------------------------------------------------------------------------
	 */

	require_once(dirname(__FILE__).'/app/loader.php');

	use App\TumblrImageSync;
	use App\TumblrVideoSync;	

	if (php_sapi_name() == "cli" or !empty($_GET['argv'])) {
	
		if (empty($argv[1]) OR !trim($argv[1])) $argv[1] = 'all';
		
		if ($argv[1]=='help') {
			echo '---------------'.PHP_EOL;			
			echo 'Usage: index.php [sync-type] [blog-name]'.PHP_EOL;
			echo '---------------'.PHP_EOL;			
			echo 'Options (all are optional):'.PHP_EOL.PHP_EOL;			
			echo ' [sync-type] Synchronization type'.PHP_EOL;
			echo '             Can be one of: all, images, videos'.PHP_EOL;
			echo '             Default: all'.PHP_EOL.PHP_EOL;
			echo ' [blog-name] Blog ID'.PHP_EOL;
			echo '             Example: fluffy-kittens (for blog: http://fluffy-kittens.tumblr.com)'.PHP_EOL;
			echo '             Default: first of your blogs'.PHP_EOL.PHP_EOL;			
			echo 'For more information see comments in index.php source'.PHP_EOL;						
			echo '---------------'.PHP_EOL;						
			exit;
		}
		
		if (!in_array($argv[1],['all', 'videos', 'images']) ) {
			die('Err: Undefined sync type, allowed types: all, images, videos'.PHP_EOL);
		}

		$lock = RunLock(ROOT_DIR.'run.lock');

		$blogName = null;
		if (!empty($argv[2]) AND trim($argv[2])) $blogName = $argv[2];

		$client = new \Tumblr\API\Client(
			CONSUMER_KEY, CONSUMER_SECRET, 
			OAUTH_TOKEN, OAUTH_SECRET
		);
		
		$dwImages = 0;
		if (in_array($argv[1],['all', 'images'])) {
			$dwImages += (new TumblrImageSync($client, [
						'syncFolder' => IMAGES_FOLDER,
						'blogName' => $blogName,
			]))->doSync();
		}

		$dwVideos = 0;		
		if (in_array($argv[1],['all', 'videos'])) {
			$dwVideos += (new TumblrVideoSync($client, [
						'syncFolder' => VIDEOS_FOLDER,
						'blogName' => $blogName,
			]))->doSync();
		}
		
		die('Sync complete, downloaded: '.$dwImages.' images, '.$dwVideos.' videos'.PHP_EOL);
	}
?>

<html>
	<head>
		<title>Tumblr sync</title>
	</head>	
	<body>
		The script is designed to run in CLI, 
		but if you sure that you want to start it in browser use next form:
		<form target="_blank">
			<input type="hidden" name="argv[0]" value="web_start"><br>
			
			Sync type:<br>
			<select name="argv[1]">
				<option value="All">All</option>
				<option value="images">Images</option>				
				<option value="videos">Videos</option>								
			</select><br><br>

			Blog ID [keep empty if you want to use your first blog]:<br>
			<input type="text" name="argv[2]" value=""><br><br>

			<input type="submit" value="Start Sync"><br><br>			
		</form>
	</body>
</html>
