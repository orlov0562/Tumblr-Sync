<?php
	namespace App;
	
	abstract class TumblrSyncAbstract 
	{
		protected $client;
		protected $options = null;
		
		public function __construct(\Tumblr\API\Client $client, array $options=[]) {
			$this->client = $client;
			$this->setOptions($options);
		}
		
		protected function getOptions() {
			return $this->options;
		}
		
		public function setOptions(array $options, $startFromDefOptions=true, $autoSetBlogName=true) {
			if (is_null($this->options) OR $startFromDefOptions) {
				$this->options = array_merge($this->getDefaultOptions(), $options);
			}

			if ($autoSetBlogName AND empty($this->options['blogName'])) {
				$this->options['blogName'] = $this->getFirstBlogName();		
			}
		}

		protected function getDefaultOptions() {
			return [
				'syncFolder' => './files/', // directory where lements will be stored
				'blogName' => null, // blog ID (external allowed), if is null the first user blog will use
				'syncUntilFindFirstExistsItem'=> true, //  stop scanning, after first saved element found
				'skipExistsItems'=> true, //  skip exists elements
			];
		}

		// https://api.tumblr.com/console/calls/user/info
		protected function getFirstBlogName() {
			$req = $this->client->getUserInfo();
			return $req->user->blogs[0]->name;
		}		
		
		public function doSync($limit=0) {
			$ret=0;
			$syncItems = $this->getItemsListToSync();
			foreach($syncItems as $k=>$item) {
				if ($limit && $k >= $limit) break;
				if ($this->saveToFile($item['url'], $item['file_path'])) {
					$ret++;
				}
			}
			return $ret;
		}	
		
		protected function getItemsListToSync(){
			$ret = [];
			$pagesCount = $this->getPagesCount();
			for ($page=0; $page<$pagesCount; $page++) {
				$posts = $this->getBlogPostsFromPage($page);
				foreach($posts as $post) {
					$items = $this->getNotSyncedItems($post);
					
					if (!$items AND !empty($this->options['syncUntilFindFirstExistsItem'])) {
						// If we do not found any new elements we decide that all elemets already saved
						// so no sense to continue scanning
						break(2);
					}
					
					foreach($items as $item) {
						$ret[] = $item;
					}
				}
			}
			
			return array_reverse ($ret);
		}
		
		protected function getPagesCount() {
			return  ceil($this->getPostsCount() / $this->getPostsPerRequest());
		}
		
		protected function getPostsCount() {
			$req = $this->getBlogPosts([
				'limit' => 1,
				'offset' => 0,
				'type' => $this->getPostType(),
			]);
			return $req->total_posts;
		}		

		protected function getBlogPostsFromPage($page) {
			$req = $this->getBlogPosts([
					'limit' => $this->getPostsPerRequest(),
					'offset' => $page * $this->getPostsPerRequest(),
					'type' => $this->getPostType(),
			]);
			return $req->posts;
		}		

		// https://api.tumblr.com/console/calls/blog/posts
		protected function getBlogPosts($options) {
			return $this->client->getBlogPosts($this->options['blogName'] , $options);
		}

		protected function getPostsPerRequest(){
			return 20;
		}
		
		protected function getNotSyncedItems($post) {
			$ret = [];
			$items = $this->getItemsFromPost($post);
			foreach($items as $item) {
				if (file_exists($item['file_path'])) {
					if (!empty($this->options['syncUntilFindFirstExistsItem'])) {
						break;
					} else {
						if (!empty($this->options['skipExistsItems'])) continue;
					}
				}
				$ret[] = $item;
			}
			return $ret;
		}

		protected function saveToFile($url, $filePath) {
			$dirPath = dirname($filePath);
			if (!is_dir($dirPath)) mkdir($dirPath, 0755, true);
			$ret = file_put_contents($filePath.'.part', fopen($url, 'r'));
			if ($ret) rename($filePath.'.part', $filePath);
			return $ret;
		}
		
		protected function getFilePathForItem($url, $pathPrefix=null) {
			$basePath = $this->options['syncFolder'].($pathPrefix ? $pathPrefix : '');

			if (preg_match('~/([^/]+)$~', $url, $regs)) {
				$fileName = $regs[1];
			} else {
				$fileName = md5($url);
			}
			return $basePath.$fileName;
		}
		
		abstract protected function getPostType();
		abstract protected function getItemsFromPost($post);
	}
