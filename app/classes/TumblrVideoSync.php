<?php
	namespace App;
	
	class TumblrVideoSync extends TumblrSyncAbstract
	{
		protected function getPostType() {
			return 'video';
		}
		
		protected function getItemsFromPost($post) {
			$pathPrefix = $this->options['blogName'].'-'.date('Y', $post->timestamp).'/'
						  .date('ymdHi', $post->timestamp).'-';
			return [[
				'file_path' => $this->getFilePathForItem(
					$post->video_url, 
					$pathPrefix
				),
				'url' => $post->video_url,
			]];
		}
	}
