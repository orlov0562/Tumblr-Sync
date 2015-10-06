<?php
	namespace App;
	
	abstract class TumblrSyncAbstract 
	{
		protected $client;
		protected $options = null;
		protected $blogName;
		
		public function __construct(\Tumblr\API\Client $client, array $options=[]) {
			$this->client = $client;
			$this->setOptions($options);
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
				'syncFolder' => './files/', // директорию куда будут сохраняться элементы
				'blogName' => null, // id блога, если не задано, будет взят первый блог владельца
				'syncUntilFindFirstExistsItem'=> true, //  останавливаться после того как найден первый скачанный элемент
				'skipExistsItems'=> true, //  пропускать существующие элементы
			];
		}

		protected function getOptions() {
			return $this->options;
		}
		
		// https://api.tumblr.com/console/calls/user/info
		protected function getFirstBlogName() {
			$req = $this->client->getUserInfo();
			return $req->user->blogs[0]->name;
		}		
		
		public function doSync($limit=0) {
			$syncItems = $this->getItemsListToSync();
			foreach($syncItems as $k=>$item) {
				if ($limit && $k >= $limit) break;
				$this->saveToFile($item['url'], $item['file_path']);
			}
		}	
		
		protected function getItemsListToSync(){
			$ret = [];
			$pagesCount = $this->getPagesCount();
			for ($page=0; $page<$pagesCount; $page++) {
				$posts = $this->getBlogPostsFromPage($page);
				foreach($posts as $post) {
					$items = $this->getNotSyncedItems($post);
					
					if (!$items AND !empty($this->options['syncUntilFindFirstExistsItem'])) {
						// на последней обработанной страницы не нашли новых элементов
						// считаем, что они все скачанны, поэтому нет смысла искать дальше
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
		
		protected function getFilePathForItem($url, $pathPrefix=null) {
			$basePath = $this->options['syncFolder'].($pathPrefix ? $pathPrefix : '');

			if (preg_match('~/([^/]+)$~', $url, $regs)) {
				$fileName = $regs[1];
			} else {
				$fileName = md5($url);
			}
			return $basePath.$fileName;
		}
		
		protected function saveToFile($url, $filePath) {
			$dirPath = dirname($filePath);
			if (!is_dir($dirPath)) mkdir($dirPath, 0755, true);
			return file_put_contents($filePath, fopen($url, 'r'));
		}
		
		abstract protected function getPostType();
		abstract protected function getItemsFromPost($post);
	}
