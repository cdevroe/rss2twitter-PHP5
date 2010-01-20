RSS to Twitter using PHP.
By: Colin Devroe
http://cdevroe.com/
http://github.com/cdevroe

Description: Polls an RSS feed and updates a Twitter account with a link to any new post based on a specific category in Wordpress.

Installation: 
1. Edit $wpUrl
2. Edit $categoryToTwitter
3. Edit $cachedir (must be writable)
4. Edit $twitter with user/pass

5. Copy to server.
6. Set up cron job. Example: /usr/local/php5/bin/php   /path/to/script/rss2twitter.php

That's it!
