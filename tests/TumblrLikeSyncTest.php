<?php

class TumblrLikeSyncTest extends PHPUnit_Framework_TestCase 
{
	private $tumblrLikeSync;
	private $client;
	
    protected function setUp()
    {
		$this->tumblrLikeSync = new \App\TumblrLikeSync(
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
		$this->tumblLikeSync = null;
		$this->client = null;		
    }
    
    protected function invokeMethod($object, $method, array $args=[])
    {
        $method = new ReflectionMethod('App\TumblrLikeSync', $method);
        $method->setAccessible(TRUE);
    	return $method->invokeArgs($object, $args);
    }

	// ---------------------------------------------------------------------------------------------------------    

    /**
     * @covers App\TumblrLikeSync::getPostsCount
     */    
    public function testGetPostsCount()
 	{
 		$res = $this->invokeMethod($this->tumblrLikeSync, 'getPostsCount');
 		$actual = is_int($res) && $res > 0;
        $this->assertTrue($actual); 	
 	}
 
 	// ---------------------------------------------------------------------------------------------------------   
 
}
