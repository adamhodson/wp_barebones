<?php
	/**
	 * Runs on Uninstall of WP Fastest Cache
	 *
	 * @package   WP Fastest Cache
	 * @author    Emre Vona
	 * @license   GPL-2.0+
	 * @link      http://wordpress.org/plugins/wp-fastest-cache/
	 */

	// if uninstall.php is not called by WordPress, die
	if(!defined('WP_UNINSTALL_PLUGIN')){
		die;
	}

	include_once("wpFastestCache.php");

	wpfastestcache_deactivate();
	
	delete_option("WpFastestCache");
	delete_option("WpFcDeleteCacheLogs");
	delete_option("WpFastestCacheCDN");
	delete_option("WpFastestCacheExclude");
	delete_option("WpFastestCachePreLoad");
	delete_option("WpFastestCacheCSS");
	delete_option("WpFastestCacheCSSSIZE");
	delete_option("WpFastestCacheJS");
	delete_option("WpFastestCacheJSSIZE");
	delete_option("wpfc_server_location");
	delete_option("wpfc-group");
	delete_option("WpFc_credit");
	delete_option("WpFc_api_key");

	foreach ((array)_get_cron_array() as $cron_key => $cron_value) {
		foreach ( (array) $cron_value as $hook => $events ) {
			if(preg_match("/^wp\_fastest\_cache/", $hook)){
				$args = array();
				
				foreach ( (array) $events as $event_key => $event ) {
					if(isset($event["args"]) && isset($event["args"][0])){
						$args = array(json_encode(json_decode($event["args"][0])));
					}
				}

				wp_clear_scheduled_hook($hook, $args);
			}
		}
	}
?>