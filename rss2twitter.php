<?php
/* 
	RSS to Twitter using PHP 5.
	By: Colin Devroe
	http://cdevroe.com/
	
	http://github.com/cdevroe
	
	Originally written on December 6, 2009 while watching
	Star Trek III: The Search for Spock.
	
	Version 0.4 - January 25, 2010

*/

$feedUrl = 'URL'; // e.g. http://cdevroe.com/feed/
$categoryToTwitter = ''; // e.g. 'Mobile photos' (see Readme for more)
$cacheDir = "/path/to/cachedir/"; // e.g. /home/.eastwood/domain.com/directory/'
$twitter = array('username' => 'USERNAME', 'password' => 'PASSWORD');

// Retrieve and parse RSS feed
$curl = curl_init($feedUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$xml = curl_exec($curl);
curl_close($curl);

// Clean up and parse XML feed
$xml = str_replace("&#8217;","'",$xml); $xml = str_replace("&#160;","",$xml); // Replace ' and nbsp
$parsedXml = new SimpleXMLElement($xml);

// Retrieve and load Cache
$cacheFile = $cacheDir . "twitterCache.txt"; $timeout = 12; // In hrs.
if ($cached = @file_get_contents($cacheFile)) { $cached = unserialize($cached); }
if (!$cached || !is_array($cached)) { $cached = array_fill(0, 19, "-"); }

// Loop through RSS feed items and post to Twitter
foreach ($parsedXml->channel->item as $post) {
	
	// Format pubDate to Unix timestamp
	if ($post->pubDate != NULL) { $date = strtotime($post->pubDate); } else { $date = $post->pubDate; }
	
	/* Post to Twitter if:
		a. The link has never been tweeted.
		b. The post is less than (timeout) hours old.
		c. The category is (categoryToTwitter) (optional) */
	if (in_array($post->link, $cached) === false && ($date == NULL || $date > time() - (60 * 60 * $timeout))) {
	
		// If a category is provided, it will check for it.
		if ($categoryToTwitter == '' || $post->category[0] == $categoryToTwitter) {
			
			// Construct message
			$twitterMessage = $post->category[0].' "'.$post->title.'": '.$post->link;
			
			// Send tweet to Twitter
			$curl = curl_init("http://twitter.com/statuses/update.xml?status=". urlencode($twitterMessage));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_USERPWD, $twitter['username'].":". $twitter['password']);
				$tweet = curl_exec($curl);
				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			
			// If success write to cache, else fail
			if ($status == 200) { 
				$cached[] = $post->link;
				if (count($cached) > 50) { array_shift($cached); }
			} else {
				echo '<p>Tried to Twitter: '.$twitterMessage.' - Error status: '.$status;
			} // End if status
		} // End if categoryToTwitter
	} // end if (post to twitter)
} // end foreach

// Open, Write, Close, cache.
$f = fopen($cacheFile,"w+"); fwrite($f,serialize($cached)); fclose($f);
?>