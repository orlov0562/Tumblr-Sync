<?php

class TumblrVideoSyncTest extends PHPUnit_Framework_TestCase 
{
	private $tumblrVideoSync;
	private $client;
	
    protected function setUp()
    {
		$this->tumblrVideoSync = new \App\TumblrVideoSync(
			$this->getClient(), 
			$this->getOptions()
		);
    }
    
	private function getClient(){
		return new \Tumblr\API\Client(
			CONSUMER_KEY, CONSUMER_SECRET, 
			OAUTH_TOKEN, OAUTH_SECRET
		);
	}

    private function getOptions() {
    	return [
			'blogName' => 'blogname',
		];
    }    

    protected function tearDown()
    {
		$this->client = null;
		$this->tumblrVideoSync = null;
    }
    
    protected function invokeMethod($object, $method, array $args=[])
    {
        $method = new ReflectionMethod('App\TumblrVideoSync', $method);
        $method->setAccessible(TRUE);
    	return $method->invokeArgs($object, $args);
    }

	// ---------------------------------------------------------------------------------------------------------    
 	
    /**
     * @covers App\TumblrVideoSync::getPostType
     */    
    public function testGetPostType()
 	{
 		$actual = $this->invokeMethod($this->tumblrVideoSync, 'getPostType');
        $this->assertEquals($actual, 'video');
 	}	

 	// ---------------------------------------------------------------------------------------------------------   
}
