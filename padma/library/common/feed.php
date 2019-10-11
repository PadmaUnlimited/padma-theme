<?php
class PadmaFeed {


	public static function init() {

		add_filter('feed_link', array(__CLASS__, 'feed_url'));

	}


	public static function feed_url($feed) {

		//Do not change the URL of comment feed URLs.
		if ( strpos($feed, 'comment') !== false )
			return $feed;

		$feed_url = PadmaOption::get('feed-url');

		//If the feed URL option doesn't have http[://] at the beginning, then we're a no go on changing the feed URL.
		if ( !$feed_url || strpos($feed_url, 'http') !== 0 )
			return $feed;

		return $feed_url;

	}


}