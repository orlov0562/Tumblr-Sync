<?php

class TumblrImageSyncTest extends PHPUnit_Framework_TestCase 
{
	private $tumblrImageSync;
	private $client;
	
    protected function setUp()
    {
		$this->tumblrImageSync = new \App\TumblrImageSync(
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
		$this->tumblrImageSync = null;
		$this->client = null;		
    }
    
    protected function invokeMethod($object, $method, array $args=[])
    {
        $method = new ReflectionMethod('App\TumblrImageSync', $method);
        $method->setAccessible(TRUE);
    	return $method->invokeArgs($object, $args);
    }

	// ---------------------------------------------------------------------------------------------------------    
 	
    /**
     * @covers App\TumblrImageSync::getPostType
     */    
    public function testGetPostType()
 	{
 		$actual = $this->invokeMethod($this->tumblrImageSync, 'getPostType');
        $this->assertEquals($actual, 'photo');
 	}	

	// ---------------------------------------------------------------------------------------------------------     	
}
