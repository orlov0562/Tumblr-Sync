<?php

	spl_autoload_register(function ($class) {
		 $classPath = dirname(__FILE__).'/'.$class.'.php';
		 if (file_exists($classPath)) require_once($classPath);
	});	

	require_once(dirname(dirname(__FILE__)).'/app/loader.php');
	
	class TumblrSyncSuite extends PHPUnit_Framework_TestSuite {
	
		public static function suite()
		{
		    $suite = new self();
		    $suite->addTestSuite('ConfigTest');		    
		    $suite->addTestSuite('TumblrSyncTest');
		    $suite->addTestSuite('TumblrImageSyncTest');
		    $suite->addTestSuite('TumblrVideoSyncTest');
		    $suite->addTestSuite('TumblrLikeSyncTest');		    		    
		    return $suite;
		}

		protected function setUp()
		{
		}

		protected function tearDown()
		{
		}
	}
