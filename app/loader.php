<?php

	define('ROOT_DIR', dirname(dirname(__FILE__)).'/');
	define('APP_DIR', ROOT_DIR.'/app/');	

	require_once(APP_DIR.'config.php');
	
	require_once(ROOT_DIR.'vendor/autoload.php');
	
	spl_autoload_register(function ($class) {
		 $class = str_replace('\\', '/', $class);
		 if (substr($class,0,4)=='App/') $class = substr($class,4);
		 $classPath = APP_DIR.'classes/'.$class.'.php';
		 if (file_exists($classPath)) require_once($classPath);
	});		
