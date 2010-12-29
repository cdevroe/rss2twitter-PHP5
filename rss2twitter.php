<?php
require_once('twitter.php');
/* 
	RSS to Twitter using PHP 5.
	By: Colin Devroe
	http://cdevroe.com/
	http://github.com/cdevroe
	https://github.com/cdevroe/rss2twitter-PHP5
	
	Originally written on December 6, 2009 while watching
	Star Trek III: The Search for Spock.
	
	Version 2.0 - December 29, 2010
  See readme.textile for installation instructions, licensing, version history, etc.

*/

/* Setup */

/* Initiate Twitter class 
consumerKey, consumerSecret */
$rss2twitter = new Twitter('','');

// Setup OAuthToken,OAuthTokenSecret
$rss2twitter->setOAuthToken('');
$rss2twitter->setOAuthTokenSecret('');

// Feed URL and Cache directory
$feedUrl = 'http://cdevroe.com/feed/'; // e.g. http://cdevroe.com/feed/
$cacheDir = "cache/"; // e.g. /home/.eastwood/domain.com/directory/' Leave blank to turn off caching

// Error reporting level
error_reporting(E_NOTICE); // You may change this for debugging purposes if you'd like.

/* End Setup */




// Retrieve and parse RSS feed
$curl = curl_init($feedUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$xml = curl_exec($curl);
curl_close($curl);

// Clean up and parse XML feed
$xml = str_replace("&#8217;","'",$xml); $xml = str_replace("&#160;","",$xml); // Replace ' and nbsp
$parsedXml = new SimpleXMLElement($xml);

// Retrieve and load Cache
if ($cacheDir != '') {
  $cacheFile = $cacheDir . "twitterCache.txt"; $timeout = 12; // In hrs.
  if ($cached = @file_get_contents($cacheFile)) { $cached = unserialize($cached); }
  if (!$cached || !is_array($cached)) { $cached = array_fill(0, 19, "-"); }
}

// Loop through RSS feed items and post to Twitter
foreach ($parsedXml->channel->item as $post) {
	
	// Format pubDate to Unix timestamp
	if ($post->pubDate != NULL) { $date = strtotime($post->pubDate); } else { $date = $post->pubDate; }
	
	/* Post to Twitter if:
		a. The link has never been tweeted.
		b. The post is less than (timeout) hours old.
		c. The category is (categoryToTwitter) (optional) */
	if (in_array($post->link, $cached) === false && ($date == NULL || $date > time() - (60 * 60 * $timeout))) {
			
		// Construct message
		$tweet = $post->category[0].' "'.$post->title.'": '.$post->link;
		
		// Send tweet to Twitter
		$sendTweet = $rss2twitter->statusesUpdate($tweet);
		
		print_r($sendTweet);
		
		// If success write to cache, else fail
		if (isset($sendTweet['id'])) { 
			$cached[] = $post->link;
			if (count($cached) > 50) { array_shift($cached); }
		} else {
			echo '<p>There was an error.</p>';
		} // End if status */
	} // end if (post to twitter)
} // end foreach

// Open, Write, Close, cache.
if ($cacheDir != '') {
  $f = fopen($cacheFile,"w+"); fwrite($f,serialize($cached)); fclose($f);
}
?>