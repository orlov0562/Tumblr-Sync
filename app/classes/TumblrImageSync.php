<?php
	namespace App;
	
	class TumblrImageSync extends TumblrSyncAbstract
	{
		// # covered	
		protected function getPostType() {
			return 'photo';
		}
		
		protected function getItemsFromPost($post) {
			$ret = [];
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
			return $ret;;
		}
	}
