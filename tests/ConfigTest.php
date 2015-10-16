<?php

class ConfigTest extends PHPUnit_Framework_TestCase 
{
	public function testDefinedSyncVars()
	{
		$this->assertTrue(defined('SYNC_VIDEOS'));
		$this->assertTrue(defined('SYNC_IMAGES'));
		$this->assertTrue(defined('SYNC_LIKES'));		
	}
	
	public function testDefinedFoldersVars()
	{
		$this->assertTrue(defined('VIDEOS_FOLDER'));
		$this->assertTrue(defined('IMAGES_FOLDER'));
		$this->assertTrue(defined('LIKES_FOLDER'));		
	}
	
	public function testDefinedKeysVars()
	{
		$this->assertTrue(defined('CONSUMER_KEY'));
		$this->assertTrue(defined('CONSUMER_SECRET'));
		
		$this->assertTrue(defined('OAUTH_TOKEN'));
		$this->assertTrue(defined('OAUTH_SECRET'));		
	}
}
