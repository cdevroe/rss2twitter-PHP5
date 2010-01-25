RSS to Twitter using PHP.
By: Colin Devroe
http://cdevroe.com/
http://github.com/cdevroe

Description:
Grabs an RSS feed and updates a Twitter account with a link to any new post based on a specific category in Wordpress.

Installation: 
1. Edit $wpUrl
2. Edit $categoryToTwitter
	'Mobile photos' as an example
	'' for all (which means every single post will be tweeted)
3. Edit $cachedir (must be writable)
4. Edit $twitter with user/pass

5. Copy to server.
6. Set up cron job. Example: /usr/local/php5/bin/php   /path/to/script/rss2twitter.php

That's it!

To do:
(If anyone would like to take these on, please feel free.)

1. Make much generic (not Wordpress specific).
2. Optimize the code and caching.
3. Take out the Short URL stuff (related to #1)
4. Make caching optional
5. Add support for posts attributed to multiple categories


Version History:
0.3 - January 24, 2010
- categoryToTwitter is optional
- Took away the "plural category name" check


0.2 - January 22, 2010
- Slight code clean up.
- A few readme changes.

0.1 - Initial codebase.

