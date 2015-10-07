# Tumblr-Sync
Script that syncing images and videos from your or any other Tumblr Blog with your PC

Initial configuration:
- do composer install
- rename /app/config.orig.php -> /app/config.php
- fillin api keys in /app/config.php
- make sure that user from which script running have rights to write to script folder

Pay attention that at this moment it downloads new files only, if you delete some downloaded
items (post with video or image) from your blog, this script DO NOT automaticaly delete it
from your PC

Examples of cron configuration
```
0 * * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php 
0 * * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php images	 
0 0 * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php videos	 
0 0 * * * * php PATH-TO-THE-SCRIPT-FOLDER/index.php images fluffy-kittens 	 
```

If you need more information you may find this links usefull:
- https://github.com/tumblr/tumblr.php
- http://developers.tumblr.com
- https://www.tumblr.com/docs/en/api/v2
- https://www.tumblr.com/oauth/apps
- https://api.tumblr.com/console/calls/user/info
