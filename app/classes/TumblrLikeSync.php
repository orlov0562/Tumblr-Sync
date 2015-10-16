<?php
	namespace App;
	
	class TumblrLikeSync extends TumblrSyncAbstract
	{
		/**
		 * @codeCoverageIgnore
		 */
		protected function getPostType() {
			// that function just need to redeclare abstract method
			return 'likes';
		}
		
		protected function getItemsFromPost($post) {
			$ret = [];
			
			switch($post->type)
			{
				case 'photo':
					foreach ($post->photos as $photo) {
						$pathPrefix = $this->options['blogName'] .'-'.date('Y', $post->timestamp).'/'
									  .date('ymdHi', $post->timestamp).'-';
						$ret[] = [
							'file_path' => $this->getFilePathForItem(
								$photo->original_size->url, 
								$pathPrefix
							),
							'url' => $photo->original_size->url,
						];			
					}	
				break;
				
				case 'video':
					$pathPrefix = $this->options['blogName'].'-'.date('Y', $post->timestamp).'/'
								  .date('ymdHi', $post->timestamp).'-';
					return [[
						'file_path' => $this->getFilePathForItem(
							$post->video_url, 
							$pathPrefix
						),
						'url' => $post->video_url,
					]];				
				break;
				
			}

			return $ret;
		}
		
		protected function getPostsCount() {
			$req = $this->getLikedPosts([
				'limit' => 1,
				'offset' => 0,
			]);
			return $req->liked_count;
		}		
		
		// https://api.tumblr.com/console/calls/user/likes
		protected function getLikedPosts($options) {
			return $this->client->getLikedPosts($options);
		}
		
		protected function getBlogPostsFromPage($page) {
			$req = $this->getLikedPosts([
					'limit' => $this->getPostsPerRequest(),
					'offset' => $page * $this->getPostsPerRequest(),
			]);
			return $req->liked_posts;
		}
			
	}
