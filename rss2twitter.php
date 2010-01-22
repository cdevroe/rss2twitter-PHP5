<?php
/* 
	RSS to Twitter using PHP 5.
	By: Colin Devroe
	http://cdevroe.com/
	
	http://github.com/cdevroe
	
	Written on December 6, 2009 while watching
	Star Trek III: The Search for Spock.
	
	Version 0.2 - January 22, 2010

*/

$wpUrl = 'URL'; // e.g. http://cdevroe.com/
$categoryToTwitter = 'CATEGORY'; // e.g. 'Mobile photos'
$cacheDir = "/path/to/cachedir/"; // e.g. /home/.eastwood/domain.com/directory/'
$twitter = array('username' => 'USERNAME', 'password' => 'PASSWORD');

// Retrieve and parse RSS feed
$curl = curl_init($wpUrl.'feed/');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$xml = curl_exec($curl);
curl_close($curl);
$xml = str_replace("&#8217;","'",$xml); $xml = str_replace("&#160;","",$xml); // Replace ' and nbsp
$parsedXml = new SimpleXMLElement($xml);

// Retrieve and load Cache
$cacheFile = $cacheDir . "twitterCache.txt"; $timeout = 12; // In hrs.
if ($cached = @file_get_contents($cacheFile)) { $cached = unserialize($cached); }
if (!$cached || !is_array($cached)) { $cached = array_fill(0, 19, "-"); }

// Loop through RSS feed items and post to Twitter
foreach ($parsedXml->channel->item as $post) {
	
	$url = split('=',$post->guid); // [0] Base URL [1] WP postID
	$shortUrl = $wpUrl.'p/'.$url[1]; unset($url);
	
	if ($post->pubDate != NULL) { $date = strtotime($post->pubDate); } else { $date = $post->pubDate; }
	
	/* Post to Twitter if:
		a. The link has never been tweeted.
		b. The post is less than (timeout) hours old.
		c. The category is (categoryToTwitter) */
	if (in_array($shortUrl, $cached) === false && ($date == NULL || $date > time() - (60 * 60 * $timeout)) && $post->category[0] == $categoryToTwitter) {
		
		$twitterMessage = rtrim($post->category[0], "s").' "'.$post->title.'": '.$shortUrl;
		
		$curl = curl_init("http://twitter.com/statuses/update.xml?status=". urlencode($twitterMessage));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_USERPWD, $twitter['username'].":". $twitter['password']);
			$tweet = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		if ($status == 200) { 
			$cached[] = $shortUrl;
			if (count($cached) > 50) { array_shift($cached); }
		} else {
			echo '<p>Tried to Twitter: '.$twitterMessage.' - Error status: '.$status;
		}

	} // end if (post to twitter)
} // end foreach

// Write cache to cachefile
$f = fopen($cacheFile,"w+");
fwrite($f,serialize($cached));
fclose($f);

?>