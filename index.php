<?php

	require_once(dirname(__FILE__).'/app/loader.php');

	use App\TumblrImageSync;
	use App\TumblrVideoSync;	

	// ---------------------------------------------------------------------------------------

	// https://github.com/tumblr/tumblr.php	
	
	// ---------------------------------------------------------------------------------------

	$client = new \Tumblr\API\Client(
		CONSUMER_KEY, CONSUMER_SECRET, 
		OAUTH_TOKEN, OAUTH_SECRET
	);

/*	
	$videoSync = new TumblrVideoSync($client, [
				'syncFolder' => VIDEOS_FOLDER,
				'blogName' => null,
				'syncUntilFindFirstExistsItem' => false,
				'skipExistsItems' => true,
	]);
	$videoSync->doSync();


	$imgSync = new TumblrImageSync($client, ['syncFolder' => IMAGES_FOLDER]);
	$imgSync->doSync();
*/

	$imgSync = new TumblrImageSync($client, ['syncFolder' => IMAGES_FOLDER, 'blogName' => 'orlovephone']);
	$imgSync->doSync();

//orlophone
