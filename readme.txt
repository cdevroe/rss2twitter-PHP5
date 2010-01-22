RSS to Twitter using PHP.
By: Colin Devroe
http://cdevroe.com/
http://github.com/cdevroe

Description:
Grabs an RSS feed and updates a Twitter account with a link to any new post based on a specific category in Wordpress.

Installation: 
1. Edit $wpUrl
2. Edit $categoryToTwitter
3. Edit $cachedir (must be writable)
4. Edit $twitter with user/pass

5. Copy to server.
6. Set up cron job. Example: /usr/local/php5/bin/php   /path/to/script/rss2twitter.php

That's it!

To do:
(If anyone would like to take these on, please feel free.)

1. Make much generic (not Wordpress specific).
2. Make the category choice optional rather than required.
3. Optimize the code and caching.


Version History:

0.2 - January 22, 2010
- Slight code clean up.
- A few readme changes.

0.1 - Initial codebase.

