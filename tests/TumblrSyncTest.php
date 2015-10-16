<?php

use App\TumblrImageSync;

class TumblrSyncTest extends PHPUnit_Framework_TestCase 
{
	private $tumblImageSync;
	private $client;
	
    protected function setUp()
    {
		$this->tumblImageSync = new TumblrImageSync(
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
		$this->tumblImageSync = null;
    }
    
    protected function invokeMethod($object, $method, array $args=[])
    {
        $method = new ReflectionMethod('App\TumblrImageSync', $method);
        $method->setAccessible(TRUE);
    	return $method->invokeArgs($object, $args);
    }

	// ---------------------------------------------------------------------------------------------------------    

    /**
     * @covers App\TumblrImageSync::getFirstBlogName
     */
    public function testGetFirstBlogName()
    {
        $actual = $this->invokeMethod($this->tumblImageSync, 'getFirstBlogName');
        $this->assertTrue(is_string($actual));
    }    

	// ---------------------------------------------------------------------------------------------------------    
    
    /**
     * @covers App\TumblrImageSync::getFilePathForItem
     * @dataProvider providerGetFilePathForItem
     */    
    public function testGetFilePathForItem($url, $pathPrefix, $expected) {
 		$actual = $this->invokeMethod($this->tumblImageSync, 'getFilePathForItem', [$url, $pathPrefix]);
        $this->assertEquals($expected, $actual);			
	}
	
	public function providerGetFilePathForItem(){
		return [
			[
				'https://41.media.tumblr.com/8afcac1a6416d496a42b649288506a3c/tumblr_nvza3bP1z11r888xpo1_540.jpg', 
				'blogname-2015/1510161231-',
				'./files/blogname-2015/1510161231-@@@@@nvza3bP1z11r888xpo1_540.jpg',
			],
			[
				'https://40.media.tumblr.com/03be7431522353b8f896ca29314a7c2f/tumblr_nw4cdq2lTc1rl1jado2_1280.jpg', 
				'blogname-2015/1510161231-',				
				'./files/blogname-2015/1510161231-@@@@nw4cdq2lTc1rl1jado2_1280.jpg',			
			],			
		];
	}

 	// ---------------------------------------------------------------------------------------------------------   

    /**
     * @covers App\TumblrImageSync::getPagesCount
     * @dataProvider providerGetPagesCount
     */    
    public function testGetPagesCount($totalCount, $perRequest, $expected) 
    {
    	$mock = $this->getMockBuilder('App\TumblrImageSync')
			    ->setMethods(['getPostsCount','getPostsPerRequest'])
			    ->disableOriginalConstructor()
			    ->getMock()
		;

		$mock->expects($this->once())
    		 ->method('getPostsCount')
    		 ->will($this->returnValue($totalCount))
    	;

		$mock->expects($this->once())
    		 ->method('getPostsPerRequest')
    		 ->will($this->returnValue($perRequest))
    	;

        $actual = $this->invokeMethod($mock, 'getPagesCount');
		
        $this->assertEquals($actual, $expected);    	
    } 	
    
    public function providerGetPagesCount()
    {
    	return [
    		[2, 10, 1],
    		[20, 10, 2],
    		[21, 10, 3],
    		[29, 10, 3],
    	];
    }

 	// ---------------------------------------------------------------------------------------------------------   
 	
    /**
     * @covers App\TumblrImageSync::getPostsPerRequest
     */    
    public function testGetPostsPerRequest()
 	{
 		$res = $this->invokeMethod($this->tumblImageSync, 'getPostsPerRequest');
 		$actual = is_int($res) && $res > 0;
        $this->assertTrue($actual); 	
 	}
 	
 	// ---------------------------------------------------------------------------------------------------------   

    /**
     * @covers App\TumblrImageSync::getDefaultOptions
     */    
	public function testGetDefaultOptions()
	{
 		$res = $this->invokeMethod($this->tumblImageSync, 'getDefaultOptions');

		$this->assertArrayHasKey('syncFolder', $res);
		$this->assertArrayHasKey('blogName', $res);
		$this->assertArrayHasKey('syncUntilFindFirstExistsItem', $res);
		$this->assertArrayHasKey('skipExistsItems', $res);
		$this->assertArrayHasKey('progressBar', $res);
	} 	
 	
 	// ---------------------------------------------------------------------------------------------------------   
	/**
	 * @covers App\TumblrImageSync::getOptions
	 */    
	public function testGetOptions()
	{
 		$res = $this->invokeMethod($this->tumblImageSync, 'getOptions');

		$this->assertArrayHasKey('syncFolder', $res);
		$this->assertArrayHasKey('blogName', $res);
		$this->assertArrayHasKey('syncUntilFindFirstExistsItem', $res);
		$this->assertArrayHasKey('skipExistsItems', $res);
		$this->assertArrayHasKey('progressBar', $res);
	} 	

 	// ---------------------------------------------------------------------------------------------------------   

	/**
	 * @covers App\TumblrImageSync::getItemsListToSync
	 */    
	public function testGetItemsListToSync()
	{
		$mock = $this->getMockBuilder('App\TumblrImageSync')
			    ->setMethods([
			    	'getPagesCount',
			    	'getBlogPostsFromPage',
			    	'getNotSyncedItems',
			    ])
			    ->disableOriginalConstructor()
			    ->getMock()
		;

		$mock->expects($this->any())
    		 ->method('getPagesCount')
    		 ->will($this->returnValue(5))
    	;

		$mock->expects($this->any())
    		 ->method('getBlogPostsFromPage')
    		 ->will($this->returnCallback(function($page){
    		 	$posts = [];
			 	for ($i=0; $i<20; $i++) {
			 		$posts[] = (object) [
			 			'test_id' => ($page+1)*100 + $i, // we use this just in testing purposes
			 			'timestamp' => 1272508903,
			 			'photos' => [
			 				(object) ['original_size'=> (object) ['url'=>'http://stub/url/page-'.$page.'/post-'.$i.'/photo-1.jpg']],
			 				(object) ['original_size'=> (object) ['url'=>'http://stub/url/page-'.$page.'/post-'.$i.'/photo-2.jpg']],
			 			],
			 		];
			 	}
    		 	return $posts;
			 }))
    	;
    	
		$mock->expects($this->any())
    		 ->method('getNotSyncedItems')
    		 ->will($this->returnCallback(function($post) {
    		 	
    		 	// if we found post with photo with test_id = 200 
    		 	// we simulate situation when we found first exists item
    		 	// and return empty array
    		 	
    		 	if ($post->test_id == 200 ) return []; 
    		 	
    		 	$items = [];
		 		foreach($post->photos as $photo) {
		 		    $url = $photo->original_size->url;
		 		    $pathPrefix = '';
			 		$items[] = [
			 			'url' => $url,
			 			'file_path' => '/tmp/stub/path',
			 		];
		 		}
		 		
				return $items;
    		 }))
    	;    	

		$mock->setOptions(['syncUntilFindFirstExistsItem'=>true], true, false);
        $res = $this->invokeMethod($mock, 'getItemsListToSync');

        // 40 = 20 posts per page * 2 photos per post
        $this->assertCount(40, $res); 	
        
		$mock->setOptions(['syncUntilFindFirstExistsItem'=>false], true, false);
        $res = $this->invokeMethod($mock, 'getItemsListToSync');

        // 198 = 5 pages * 20 posts per page * 2 photos per post - 2 photos from post with test_id = 200
        $this->assertCount(198, $res); 	
        
	} 	
	
 	// ---------------------------------------------------------------------------------------------------------   
  	
}
