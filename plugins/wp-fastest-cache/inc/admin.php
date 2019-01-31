<?php
	class WpFastestCacheAdmin extends WpFastestCache{
		private $adminPageUrl = "wp-fastest-cache/admin/index.php";
		private $systemMessage = array();
		private $options = array();
		private $cronJobSettings;
		private $startTime;
		private $blockCache = false;

		public function __construct(){
			$this->options = $this->getOptions();
			
			$this->setCronJobSettings();
			$this->addButtonOnEditor();
			add_action('admin_enqueue_scripts', array($this, 'addJavaScript'));
		}

		public function create_auto_cache_timeout($recurrance, $interval){
			$exist_cronjob = false;
			$wpfc_timeout_number = 0;

			$crons = _get_cron_array();

			foreach ((array)$crons as $cron_key => $cron_value) {
				foreach ( (array) $cron_value as $hook => $events ) {
					if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
						if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
							$wpfc_timeout_number++;

							foreach ( (array) $events as $event_key => $event ) {
								$schedules = wp_get_schedules();

								if(isset($event["args"]) && isset($event["args"][0])){
									if($event["args"][0] == '{"prefix":"all","content":"all"}'){
										if($schedules[$event["schedule"]]["interval"] <= $interval){
											$exist_cronjob = true;
										}
									}
								}
							}
						}
					}
				}
			}

			if(!$exist_cronjob){
				$args = array("prefix" => "all", "content" => "all");
				wp_schedule_event(time(), $recurrance, "wp_fastest_cache_".$wpfc_timeout_number, array(json_encode($args)));
			}
		}

		public function get_premium_version(){
			$wpfc_premium_version = "";
			if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/wpFastestCachePremium.php")){
				if($data = @file_get_contents(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/wpFastestCachePremium.php")){
					preg_match("/Version:\s*(.+)/", $data, $out);
					if(isset($out[1]) && $out[1]){
						$wpfc_premium_version = trim($out[1]);
					}
				}
			}
			return $wpfc_premium_version;
		}

		public function addButtonOnEditor(){
			add_action('admin_print_footer_scripts', array($this, 'addButtonOnQuicktagsEditor'));
			add_action('init', array($this, 'myplugin_buttonhooks'));
		}

		public function checkShortCode($content){
			preg_match("/\[wpfcNOT\]/", $content, $wpfcNOT);
			if(count($wpfcNOT) > 0){
				if(is_single() || is_page()){
					$this->blockCache = true;
				}
				$content = str_replace("[wpfcNOT]", "", $content);
			}
			return $content;
		}

		public function myplugin_buttonhooks() {
		   // Only add hooks when the current user has permissions AND is in Rich Text editor mode
		   if (current_user_can( 'manage_options' )) {
		     add_filter("mce_external_plugins", array($this, "myplugin_register_tinymce_javascript"));
		     add_filter('mce_buttons', array($this, 'myplugin_register_buttons'));
		   }
		}
		// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
		public function myplugin_register_tinymce_javascript($plugin_array) {
		   $plugin_array['wpfc'] = plugins_url('../js/button.js?v='.time(),__file__);
		   return $plugin_array;
		}

		public function myplugin_register_buttons($buttons) {
		   array_push($buttons, 'wpfc');
		   return $buttons;
		}

		public function addButtonOnQuicktagsEditor(){
			if (wp_script_is('quicktags') && current_user_can( 'manage_options' )){ ?>
				<script type="text/javascript">
					if(typeof QTags != "undefined"){
				    	QTags.addButton('wpfc_not', 'wpfcNOT', '<!--[wpfcNOT]-->', '', '', 'Block caching for this page');
					}
			    </script>
		    <?php }
		}

		public function optionsPageRequest(){
			if(!empty($_POST)){
				if(isset($_POST["wpFastestCachePage"])){
					include_once ABSPATH."wp-includes/capabilities.php";
					include_once ABSPATH."wp-includes/pluggable.php";

					if(is_multisite()){
						$this->notify(array("The plugin does not work with Multisite", "error"));
						return 0;
					}

					if(current_user_can('manage_options')){
						if($_POST["wpFastestCachePage"] == "options"){
							$this->exclude_urls();

							$this->saveOption();
						}else if($_POST["wpFastestCachePage"] == "deleteCache"){
							$this->deleteCache();
						}else if($_POST["wpFastestCachePage"] == "deleteCssAndJsCache"){
							$this->deleteCache(true);
						}else if($_POST["wpFastestCachePage"] == "cacheTimeout"){
							$this->addCacheTimeout();
						}
					}else{
						die("Forbidden");
					}
				}
			}
		}

		public function exclude_urls(){
			// to exclude wishlist url of YITH WooCommerce Wishlist
			if($this->isPluginActive('yith-woocommerce-wishlist/init.php')){
				$wishlist_page_id = get_option("yith_wcwl_wishlist_page_id");
				$permalink = urldecode(get_permalink($wishlist_page_id));

				if(preg_match("/https?:\/\/[^\/]+\/(.+)/", $permalink, $out)){
					$url = trim($out[1], "/");
				}
			}


			if(isset($url) && $url){
				$rules_std = array();
				$rules_json = get_option("WpFastestCacheExclude");

				$new_rule = new stdClass;
				$new_rule->prefix = "exact";
				$new_rule->content = $url;
				$new_rule->type = "page";


				if($rules_json === false){
					array_push($rules_std, $new_rule);
					add_option("WpFastestCacheExclude", json_encode($rules_std), null, "yes");
				}else{
					$rules_std = json_decode($rules_json);

					if(!is_array($rules_std)){
						$rules_std = array();
					}

					if(!in_array($new_rule, $rules_std)){
						array_push($rules_std, $new_rule);
						update_option("WpFastestCacheExclude", json_encode($rules_std));
					}
				}
			}
		}

		public function addCacheTimeout(){
			if(isset($_POST["wpFastestCacheTimeOut"])){
				if($_POST["wpFastestCacheTimeOut"]){
					if(isset($_POST["wpFastestCacheTimeOutHour"]) && is_numeric($_POST["wpFastestCacheTimeOutHour"])){
						if(isset($_POST["wpFastestCacheTimeOutMinute"]) && is_numeric($_POST["wpFastestCacheTimeOutMinute"])){
							$selected = mktime($_POST["wpFastestCacheTimeOutHour"], $_POST["wpFastestCacheTimeOutMinute"], 0, date("n"), date("j"), date("Y"));

							if($selected > time()){
								$timestamp = $selected;
							}else{
								if(time() - $selected < 60){
									$timestamp = $selected + 60;
								}else{
									// if selected time is less than now, 24hours is added
									$timestamp = $selected + 24*60*60;
								}
							}

							wp_clear_scheduled_hook($this->slug());
							wp_schedule_event($timestamp, $_POST["wpFastestCacheTimeOut"], $this->slug());
						}else{
							echo "Minute was not set";
							exit;
						}
					}else{
						echo "Hour was not set";
						exit;
					}
				}else{
					wp_clear_scheduled_hook($this->slug());
				}
			}
		}

		public function setCronJobSettings(){
			if(wp_next_scheduled($this->slug())){
				$this->cronJobSettings["period"] = wp_get_schedule($this->slug());
				$this->cronJobSettings["time"] = wp_next_scheduled($this->slug());
			}
		}

		public function addMenuPage(){
			add_action('admin_menu', array($this, 'register_my_custom_menu_page'));
		}

		public function addJavaScript(){
			wp_enqueue_script("jquery-ui-draggable");
			wp_enqueue_script("jquery-ui-position");
			wp_enqueue_script("wpfc-dialog", plugins_url("wp-fastest-cache/js/dialog.js"), array(), time(), false);
			wp_enqueue_script("wpfc-dialog-new", plugins_url("wp-fastest-cache/js/dialog_new.js"), array(), time(), false);


			wp_enqueue_script("wpfc-cdn", plugins_url("wp-fastest-cache/js/cdn/cdn.js"), array(), time(), false);


			wp_enqueue_script("wpfc-language", plugins_url("wp-fastest-cache/js/language.js"), array(), time(), false);
			wp_enqueue_script("wpfc-schedule", plugins_url("wp-fastest-cache/js/schedule.js"), array(), time(), true);
			wp_enqueue_script("wpfc-db", plugins_url("wp-fastest-cache/js/db.js"), array(), time(), true);

			
			if(class_exists("WpFastestCacheImageOptimisation")){
				if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/js/statics.js")){
					wp_enqueue_script("wpfc-statics", plugins_url("wp-fastest-cache-premium/pro/js/statics.js"), array(), time(), false);
				}

				if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/js/premium.js")){
					wp_enqueue_script("wpfc-premium", plugins_url("wp-fastest-cache-premium/pro/js/premium.js"), array(), time(), true);
				}
			}
			
			if(isset($this->options->wpFastestCacheLanguage) && $this->options->wpFastestCacheLanguage != "eng"){
				wp_enqueue_script("wpfc-dictionary", plugins_url("wp-fastest-cache/js/lang/".$this->options->wpFastestCacheLanguage.".js"), array(), time(), false);
			}
		}

		public function saveOption(){
			unset($_POST["wpFastestCachePage"]);
			unset($_POST["option_page"]);
			unset($_POST["action"]);
			unset($_POST["_wpnonce"]);
			unset($_POST["_wp_http_referer"]);
			
			$data = json_encode($_POST);
			//for optionsPage() $_POST is array and json_decode() converts to stdObj
			$this->options = json_decode($data);

			$this->systemMessage = $this->modifyHtaccess($_POST);

			if(isset($this->systemMessage[1]) && $this->systemMessage[1] != "error"){

				if($message = $this->checkCachePathWriteable()){


					if(is_array($message)){
						$this->systemMessage = $message;
					}else{
						if(isset($this->options->wpFastestCachePreload)){
							$this->set_preload();
						}else{
							delete_option("WpFastestCachePreLoad");
							wp_clear_scheduled_hook("wp_fastest_cache_Preload");
						}

						if(get_option("WpFastestCache")){
							update_option("WpFastestCache", $data);
						}else{
							add_option("WpFastestCache", $data, null, "yes");
						}
					}
				}
			}

			$this->notify($this->systemMessage);
		}

		public function checkCachePathWriteable(){
			$message = array();

			if(!is_dir($this->getWpContentDir("/cache/"))){
				if (@mkdir($this->getWpContentDir("/cache/"), 0755, true)){
					//
				}else{
					array_push($message, "- /wp-content/cache/ is needed to be created");
				}
			}else{
				if (@mkdir($this->getWpContentDir("/cache/testWpFc/"), 0755, true)){
					rmdir($this->getWpContentDir("/cache/testWpFc/"));
				}else{
					array_push($message, "- /wp-content/cache/ permission has to be 755");
				}
			}

			if(!is_dir($this->getWpContentDir("/cache/all/"))){
				if (@mkdir($this->getWpContentDir("/cache/all/"), 0755, true)){
					//
				}else{
					array_push($message, "- /wp-content/cache/all/ is needed to be created");
				}
			}else{
				if (@mkdir($this->getWpContentDir("/cache/all/testWpFc/"), 0755, true)){
					rmdir($this->getWpContentDir("/cache/all/testWpFc/"));
				}else{
					array_push($message, "- /wp-content/cache/all/ permission has to be 755");
				}	
			}

			if(count($message) > 0){
				return array(implode("<br>", $message), "error");
			}else{
				return true;
			}
		}

		public function modifyHtaccess($post){
			$path = ABSPATH;
			if($this->is_subdirectory_install()){
				$path = $this->getABSPATH();
			}

			// if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"])){
			// 	return array("The plugin does not work with Microsoft IIS. Only with Apache", "error");
			// }

			// if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
			// 	return array("The plugin does not work with Nginx. Only with Apache", "error");
			// }

			if(!file_exists($path.".htaccess")){
				if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && (preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"]) || preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"]))){
					//
				}else{
					return array("<label>.htaccess was not found</label> <a target='_blank' href='http://www.wpfastestcache.com/warnings/htaccess-was-not-found/'>Read More</a>", "error");
				}
			}

			if($this->isPluginActive('wp-postviews/wp-postviews.php')){
				$wp_postviews_options = get_option("views_options");
				$wp_postviews_options["use_ajax"] = true;
				update_option("views_options", $wp_postviews_options);

				if(!WP_CACHE){
					if($wp_config = @file_get_contents(ABSPATH."wp-config.php")){
						$wp_config = str_replace("\$table_prefix", "define('WP_CACHE', true);\n\$table_prefix", $wp_config);

						if(!@file_put_contents(ABSPATH."wp-config.php", $wp_config)){
							return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
						}
					}else{
						return array("define('WP_CACHE', true); is needed to be added into wp-config.php", "error");
					}
				}
			}

			$htaccess = @file_get_contents($path.".htaccess");

			// if(defined('DONOTCACHEPAGE')){
			// 	return array("DONOTCACHEPAGE <label>constant is defined as TRUE. It must be FALSE</label>", "error");
			// }else 
			

			if(!get_option('permalink_structure')){
				return array("You have to set <strong><u><a href='".admin_url()."options-permalink.php"."'>permalinks</a></u></strong>", "error");
			}else if($res = $this->checkSuperCache($path, $htaccess)){
				return $res;
			}else if($this->isPluginActive('far-future-expiration/far-future-expiration.php')){
				return array("Far Future Expiration Plugin", "error");
			}else if($this->isPluginActive('sg-cachepress/sg-cachepress.php')){
				return array("SG Optimizer needs to be deactived", "error");
			}else if($this->isPluginActive('adrotate/adrotate.php') || $this->isPluginActive('adrotate-pro/adrotate.php')){
				return $this->warningIncompatible("AdRotate");
			}else if($this->isPluginActive('mobilepress/mobilepress.php')){
				return $this->warningIncompatible("MobilePress", array("name" => "WPtouch Mobile", "url" => "https://wordpress.org/plugins/wptouch/"));
			}else if($this->isPluginActive('speed-booster-pack/speed-booster-pack.php')){
				return array("Speed Booster Pack needs to be deactivated<br>", "error");
			}else if($this->isPluginActive('cdn-enabler/cdn-enabler.php')){
				return array("CDN Enabler needs to be deactivated<br>This plugin has aldready CDN feature", "error");
			}else if($this->isPluginActive('wp-performance-score-booster/wp-performance-score-booster.php')){
				return array("WP Performance Score Booster needs to be deactivated<br>This plugin has aldready Gzip, Leverage Browser Caching features", "error");
			}else if($this->isPluginActive('bwp-minify/bwp-minify.php')){
				return array("Better WordPress Minify needs to be deactivated<br>This plugin has aldready Minify feature", "error");
			}else if($this->isPluginActive('check-and-enable-gzip-compression/richards-toolbox.php')){
				return array("Check and Enable GZIP compression needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
			}else if($this->isPluginActive('gzippy/gzippy.php')){
				return array("GZippy needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
			}else if($this->isPluginActive('gzip-ninja-speed-compression/gzip-ninja-speed.php')){
				return array("GZip Ninja Speed Compression needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
			}else if($this->isPluginActive('wordpress-gzip-compression/ezgz.php')){
				return array("WordPress Gzip Compression needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
			}else if($this->isPluginActive('filosofo-gzip-compression/filosofo-gzip-compression.php')){
				return array("GZIP Output needs to be deactivated<br>This plugin has aldready Gzip feature", "error");
			}else if($this->isPluginActive('head-cleaner/head-cleaner.php')){
				return array("Head Cleaner needs to be deactivated", "error");
			}else if($this->isPluginActive('far-future-expiry-header/far-future-expiration.php')){
				return array("Far Future Expiration Plugin needs to be deactivated", "error");
			}else if(is_writable($path.".htaccess")){
				$htaccess = $this->insertWebp($htaccess);
				$htaccess = $this->insertLBCRule($htaccess, $post);
				$htaccess = $this->insertGzipRule($htaccess, $post);
				$htaccess = $this->insertRewriteRule($htaccess, $post);

				$htaccess = $this->to_move_gtranslate_rules($htaccess);

				file_put_contents($path.".htaccess", $htaccess);
			}else{
				return array("Options have been saved", "updated");
				//return array(".htaccess is not writable", "error");
			}
			return array("Options have been saved", "updated");

		}

		public function to_move_gtranslate_rules($htaccess){
			preg_match("/\#\#\#\s+BEGIN\sGTranslate\sconfig\s\#\#\#[^\#]+\#\#\#\s+END\sGTranslate\sconfig\s\#\#\#/i", $htaccess, $gtranslate);

			if(isset($gtranslate[0])){
				$htaccess = preg_replace("/\#\#\#\s+BEGIN\sGTranslate\sconfig\s\#\#\#[^\#]+\#\#\#\s+END\sGTranslate\sconfig\s\#\#\#/i", "", $htaccess);
				$htaccess = $gtranslate[0]."\n".$htaccess;
			}

			return $htaccess;
		}

		public function warningIncompatible($incompatible, $alternative = false){
			if($alternative){
				return array($incompatible." <label>needs to be deactive</label><br><label>We advise</label> <a id='alternative-plugin' target='_blank' href='".$alternative["url"]."'>".$alternative["name"]."</a>", "error");
			}else{
				return array($incompatible." <label>needs to be deactive</label>", "error");
			}
		}

		public function insertWebp($htaccess){
			if(class_exists("WpFastestCachePowerfulHtml")){
				if(defined("WPFC_DISABLE_WEBP") && WPFC_DISABLE_WEBP){
					$webp = false;
				}else{
					$webp = true;
				}
			}else{
				$webp = false;
			}

							
			if($webp){
				$basename = "$1.webp";

				// this part for sub-directory installation
				// site_url() and home_url() must be the same
				if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", site_url(), $siteurl_base_name)){
					if(preg_match("/https?\:\/\/[^\/]+\/(.+)/", home_url(), $homeurl_base_name)){
						$homeurl_base_name[1] = trim($homeurl_base_name[1], "/");
						$siteurl_base_name[1] = trim($siteurl_base_name[1], "/");

						if($homeurl_base_name[1] == $siteurl_base_name[1]){
							if(preg_match("/".preg_quote($homeurl_base_name[1], "/")."$/", trim(ABSPATH, "/"))){
								$basename = $homeurl_base_name[1]."/".$basename;
							}
						}
					}
				}

				if(ABSPATH == "//"){
					$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f"."\n";
				}else{
					// to escape spaces
					$tmp_ABSPATH = str_replace(" ", "\ ", ABSPATH);

					$RewriteCond = "RewriteCond %{DOCUMENT_ROOT}/".$basename." -f [or]"."\n";
					$RewriteCond = $RewriteCond."RewriteCond ".$tmp_ABSPATH."$1.webp -f"."\n";
				}


				$data = "# BEGIN WEBPWpFastestCache"."\n".
						"<IfModule mod_rewrite.c>"."\n".
						"RewriteEngine On"."\n".
						"RewriteCond %{HTTP_ACCEPT} image/webp"."\n".
						"RewriteCond %{REQUEST_URI} \.(jpe?g|png)"."\n".
						$RewriteCond.
						"RewriteRule ^(.*) \"/".$basename."\" [L]"."\n".
						"</IfModule>"."\n".
						"<IfModule mod_headers.c>"."\n".
						"Header append Vary Accept env=REDIRECT_accept"."\n".
						"</IfModule>"."\n".
						"AddType image/webp .webp"."\n".
						"# END WEBPWpFastestCache"."\n";

				if(!preg_match("/BEGIN\s*WEBPWpFastestCache/", $htaccess)){
					$htaccess = $data.$htaccess;
				}

				return $htaccess;
			}else{
				$htaccess = preg_replace("/#\s?BEGIN\s?WEBPWpFastestCache.*?#\s?END\s?WEBPWpFastestCache/s", "", $htaccess);
				return $htaccess;
			}
		}

		public function insertLBCRule($htaccess, $post){
			if(isset($post["wpFastestCacheLBC"]) && $post["wpFastestCacheLBC"] == "on"){


			$data = "# BEGIN LBCWpFastestCache"."\n".
					'<FilesMatch "\.(webm|ogg|mp4|ico|pdf|flv|jpg|jpeg|png|gif|webp|js|css|swf|x-html|css|xml|js|woff|woff2|ttf|svg|eot)(\.gz)?$">'."\n".
					'<IfModule mod_expires.c>'."\n".
					'AddType application/font-woff2 .woff2'."\n".
					'ExpiresActive On'."\n".
					'ExpiresDefault A0'."\n".
					'ExpiresByType video/webm A10368000'."\n".
					'ExpiresByType video/ogg A10368000'."\n".
					'ExpiresByType video/mp4 A10368000'."\n".
					'ExpiresByType image/webp A10368000'."\n".
					'ExpiresByType image/gif A10368000'."\n".
					'ExpiresByType image/png A10368000'."\n".
					'ExpiresByType image/jpg A10368000'."\n".
					'ExpiresByType image/jpeg A10368000'."\n".
					'ExpiresByType image/ico A10368000'."\n".
					'ExpiresByType image/svg+xml A10368000'."\n".
					'ExpiresByType text/css A10368000'."\n".
					'ExpiresByType text/javascript A10368000'."\n".
					'ExpiresByType application/javascript A10368000'."\n".
					'ExpiresByType application/x-javascript A10368000'."\n".
					'ExpiresByType application/font-woff2 A10368000'."\n".
					'</IfModule>'."\n".
					'<IfModule mod_headers.c>'."\n".
					'Header set Expires "max-age=A10368000, public"'."\n".
					'Header unset ETag'."\n".
					'Header set Connection keep-alive'."\n".
					'FileETag None'."\n".
					'</IfModule>'."\n".
					'</FilesMatch>'."\n".
					"# END LBCWpFastestCache"."\n";

				if(!preg_match("/BEGIN\s*LBCWpFastestCache/", $htaccess)){
					return $data.$htaccess;
				}else{
					return $htaccess;
				}
			}else{
				//delete levere browser caching
				$htaccess = preg_replace("/#\s?BEGIN\s?LBCWpFastestCache.*?#\s?END\s?LBCWpFastestCache/s", "", $htaccess);
				return $htaccess;
			}
		}

		public function insertGzipRule($htaccess, $post){
			if(isset($post["wpFastestCacheGzip"]) && $post["wpFastestCacheGzip"] == "on"){
		    	$data = "# BEGIN GzipWpFastestCache"."\n".
		          		"<IfModule mod_deflate.c>"."\n".
		          		"AddType x-font/woff .woff"."\n".
		          		"AddType x-font/ttf .ttf"."\n".
		          		"AddOutputFilterByType DEFLATE image/svg+xml"."\n".
		  				"AddOutputFilterByType DEFLATE text/plain"."\n".
		  				"AddOutputFilterByType DEFLATE text/html"."\n".
		  				"AddOutputFilterByType DEFLATE text/xml"."\n".
		  				"AddOutputFilterByType DEFLATE text/css"."\n".
		  				"AddOutputFilterByType DEFLATE text/javascript"."\n".
		  				"AddOutputFilterByType DEFLATE application/xml"."\n".
		  				"AddOutputFilterByType DEFLATE application/xhtml+xml"."\n".
		  				"AddOutputFilterByType DEFLATE application/rss+xml"."\n".
		  				"AddOutputFilterByType DEFLATE application/javascript"."\n".
		  				"AddOutputFilterByType DEFLATE application/x-javascript"."\n".
		  				"AddOutputFilterByType DEFLATE application/x-font-ttf"."\n".
						"AddOutputFilterByType DEFLATE application/vnd.ms-fontobject"."\n".
						"AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf"."\n".
		  				"</IfModule>"."\n";

				if(defined("WPFC_GZIP_FOR_COMBINED_FILES") && WPFC_GZIP_FOR_COMBINED_FILES){
					$data = $data."\n".'<FilesMatch "\d+index\.(css|js)(\.gz)?$">'."\n".
			  				"# to zip the combined css and js files"."\n\n".
							"RewriteEngine On"."\n".
							"RewriteCond %{HTTP:Accept-encoding} gzip"."\n".
							"RewriteCond %{REQUEST_FILENAME}\.gz -s"."\n".
							"RewriteRule ^(.*)\.(css|js) $1\.$2\.gz [QSA]"."\n\n".
							"# to revent double gzip and give the correct mime-type"."\n\n".
							"RewriteRule \.css\.gz$ - [T=text/css,E=no-gzip:1,E=FORCE_GZIP]"."\n".
							"RewriteRule \.js\.gz$ - [T=text/javascript,E=no-gzip:1,E=FORCE_GZIP]"."\n".
							"Header set Content-Encoding gzip env=FORCE_GZIP"."\n".
							"</FilesMatch>"."\n";
				}

				$data = $data."# END GzipWpFastestCache"."\n";

				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);
				return $data.$htaccess;

			}else{
				//delete gzip rules
				$htaccess = preg_replace("/\s*\#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache\s*/s", "", $htaccess);
				return $htaccess;
			}
		}

		public function insertRewriteRule($htaccess, $post){
			if(isset($post["wpFastestCacheStatus"]) && $post["wpFastestCacheStatus"] == "on"){
				$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
				$htaccess = $this->getHtaccess().$htaccess;
			}else{
				$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
				$this->deleteCache();
			}

			return $htaccess;
		}

		public function prefixRedirect(){
			$forceTo = "";
			
			if(defined("WPFC_DISABLE_REDIRECTION") && WPFC_DISABLE_REDIRECTION){
				return $forceTo;
			}

			if(preg_match("/^https:\/\//", home_url())){
				if(preg_match("/^https:\/\/www\./", home_url())){
					$forceTo = "\nRewriteCond %{HTTPS} =on"."\n".
					           "RewriteCond %{HTTP_HOST} ^www.".str_replace("www.", "", $_SERVER["HTTP_HOST"])."\n";
				}else{
					$forceTo = "\nRewriteCond %{HTTPS} =on"."\n".
							   "RewriteCond %{HTTP_HOST} ^".str_replace("www.", "", $_SERVER["HTTP_HOST"])."\n";
				}
			}else{
				if(preg_match("/^http:\/\/www\./", home_url())){
					$forceTo = "\nRewriteCond %{HTTP_HOST} ^".str_replace("www.", "", $_SERVER["HTTP_HOST"])."\n".
							   "RewriteRule ^(.*)$ ".preg_quote(home_url(), "/")."\/$1 [R=301,L]"."\n";
				}else{
					$forceTo = "\nRewriteCond %{HTTP_HOST} ^www.".str_replace("www.", "", $_SERVER["HTTP_HOST"])." [NC]"."\n".
							   "RewriteRule ^(.*)$ ".preg_quote(home_url(), "/")."\/$1 [R=301,L]"."\n";
				}
			}
			return $forceTo;
		}

		public function getHtaccess(){
			$mobile = "";
			$loggedInUser = "";
			$ifIsNotSecure = "";
			$trailing_slash_rule = "";
			$consent_cookie = "";

			if(isset($_POST["wpFastestCacheMobile"]) && $_POST["wpFastestCacheMobile"] == "on"){
				$mobile = "RewriteCond %{HTTP_USER_AGENT} !^.*(".$this->getMobileUserAgents().").*$ [NC]"."\n";
			}

			if(isset($_POST["wpFastestCacheLoggedInUser"]) && $_POST["wpFastestCacheLoggedInUser"] == "on"){
				$loggedInUser = "RewriteCond %{HTTP:Cookie} !wordpress_logged_in"."\n";
			}

			if(!preg_match("/^https/i", get_option("home"))){
				$ifIsNotSecure = "RewriteCond %{HTTPS} !=on";
			}

			if($this->is_trailing_slash()){
				$trailing_slash_rule = "RewriteCond %{REQUEST_URI} \/$"."\n";
			}else{
				//toDo
			}

			$data = "# BEGIN WpFastestCache"."\n".
					"<IfModule mod_rewrite.c>"."\n".
					"RewriteEngine On"."\n".
					"RewriteBase /"."\n".
					$this->ruleForWpContent()."\n".
					$this->prefixRedirect().
					$this->excludeRules()."\n".
					$this->excludeAdminCookie()."\n".
					$this->http_condition_rule()."\n".
					"RewriteCond %{HTTP_USER_AGENT} !(".$this->get_excluded_useragent().")"."\n".
					"RewriteCond %{HTTP_USER_AGENT} !(WP\sFastest\sCache\sPreload(\siPhone\sMobile)?\s*Bot)"."\n".
					"RewriteCond %{REQUEST_METHOD} !POST"."\n".
					$ifIsNotSecure."\n".
					"RewriteCond %{REQUEST_URI} !(\/){2}$"."\n".
					$trailing_slash_rule.
					"RewriteCond %{QUERY_STRING} !.+"."\n".$loggedInUser.
					$consent_cookie.
					"RewriteCond %{HTTP:Cookie} !comment_author_"."\n".
					"RewriteCond %{HTTP:Cookie} !woocommerce_items_in_cart"."\n".
					"RewriteCond %{HTTP:Cookie} !safirmobilswitcher=mobil"."\n".
					'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]'."\n".$mobile;
			

			if(ABSPATH == "//"){
				$data = $data."RewriteCond %{DOCUMENT_ROOT}/".WPFC_WP_CONTENT_BASENAME."/cache/all/$1/index.html -f"."\n";
			}else{
				//WARNING: If you change the following lines, you need to update webp as well
				$data = $data."RewriteCond %{DOCUMENT_ROOT}/".WPFC_WP_CONTENT_BASENAME."/cache/all/$1/index.html -f [or]"."\n";
				// to escape spaces
				$tmp_WPFC_WP_CONTENT_DIR = str_replace(" ", "\ ", WPFC_WP_CONTENT_DIR);

				$data = $data."RewriteCond ".$tmp_WPFC_WP_CONTENT_DIR."/cache/all/".$this->getRewriteBase(true)."$1/index.html -f"."\n";
			}

			$data = $data.'RewriteRule ^(.*) "/'.$this->getRewriteBase().WPFC_WP_CONTENT_BASENAME.'/cache/all/'.$this->getRewriteBase(true).'$1/index.html" [L]'."\n";
			
			//RewriteRule !/  "/wp-content/cache/all/index.html" [L]


			if(class_exists("WpFcMobileCache") && isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme){
				$wpfc_mobile = new WpFcMobileCache();

				if($this->isPluginActive('wptouch/wptouch.php') || $this->isPluginActive('wptouch-pro/wptouch-pro.php')){
					$wpfc_mobile->set_wptouch(true);
				}else{
					$wpfc_mobile->set_wptouch(false);
				}

				$data = $data."\n\n\n".$wpfc_mobile->update_htaccess($data);
			}

			$data = $data."</IfModule>"."\n".
					"<FilesMatch \"index\.(html|htm)$\">"."\n".
					"AddDefaultCharset UTF-8"."\n".
					"<ifModule mod_headers.c>"."\n".
					"FileETag None"."\n".
					"Header unset ETag"."\n".
					"Header set Cache-Control \"max-age=0, no-cache, no-store, must-revalidate\""."\n".
					"Header set Pragma \"no-cache\""."\n".
					"Header set Expires \"Mon, 29 Oct 1923 20:30:00 GMT\""."\n".
					"</ifModule>"."\n".
					"</FilesMatch>"."\n".
					"# END WpFastestCache"."\n";
			return preg_replace("/\n+/","\n", $data);
		}

		public function http_condition_rule(){
			$http_host = preg_replace("/(http(s?)\:)?\/\/(www\d*\.)?/i", "", trim(home_url(), "/"));

			if(preg_match("/\//", $http_host)){
				$http_host = strstr($http_host, '/', true);
			}

			if(preg_match("/www\./", home_url())){
				$http_host = "www.".$http_host;
			}

			return "RewriteCond %{HTTP_HOST} ^".$http_host;
		}

		public function ruleForWpContent(){
			return "";
			$newContentPath = str_replace(home_url(), "", content_url());
			if(!preg_match("/wp-content/", $newContentPath)){
				$newContentPath = trim($newContentPath, "/");
				return "RewriteRule ^".$newContentPath."/cache/(.*) ".WPFC_WP_CONTENT_DIR."/cache/$1 [L]"."\n";
			}
			return "";
		}

		public function getRewriteBase($sub = ""){
			if($sub && $this->is_subdirectory_install()){
				$trimedProtocol = preg_replace("/http:\/\/|https:\/\//", "", trim(home_url(), "/"));
				$path = strstr($trimedProtocol, '/');

				if($path){
					return trim($path, "/")."/";
				}else{
					return "";
				}
			}
			
			$url = rtrim(site_url(), "/");
			preg_match("/https?:\/\/[^\/]+(.*)/", $url, $out);

			if(isset($out[1]) && $out[1]){
				$out[1] = trim($out[1], "/");

				if(preg_match("/\/".preg_quote($out[1], "/")."\//", WPFC_WP_CONTENT_DIR)){
					return $out[1]."/";
				}else{
					return "";
				}
			}else{
				return "";
			}
		}



		public function checkSuperCache($path, $htaccess){
			if($this->isPluginActive('wp-super-cache/wp-cache.php')){
				return array("WP Super Cache needs to be deactive", "error");
			}else{
				@unlink($path."wp-content/wp-cache-config.php");

				$message = "";
				
				if(is_file($path."wp-content/wp-cache-config.php")){
					$message .= "<br>- be sure that you removed /wp-content/wp-cache-config.php";
				}

				if(preg_match("/supercache/", $htaccess)){
					$message .= "<br>- be sure that you removed the rules of super cache from the .htaccess";
				}

				return $message ? array("WP Super Cache cannot remove its own remnants so please follow the steps below".$message, "error") : "";
			}

			return "";
		}

		public function check_htaccess(){
			$path = ABSPATH;

			if($this->is_subdirectory_install()){
				$path = $this->getABSPATH();
			}
			
			if(!is_writable($path.".htaccess") && count($_POST) > 0){
				include_once(WPFC_MAIN_PATH."templates/htaccess.html");

				$htaccess = @file_get_contents($path.".htaccess");

				if(isset($this->options->wpFastestCacheLBC)){
					$htaccess = $this->insertLBCRule($htaccess, array("wpFastestCacheLBC" => "on"));
				}
				if(isset($this->options->wpFastestCacheGzip)){
					$htaccess = $this->insertGzipRule($htaccess, array("wpFastestCacheGzip" => "on"));
				}
				if(isset($this->options->wpFastestCacheStatus)){
					$htaccess = $this->insertRewriteRule($htaccess, array("wpFastestCacheStatus" => "on"));
				}
				
				$htaccess = preg_replace("/\n+/","\n", $htaccess);

				echo "<noscript id='wpfc-htaccess-data'>".$htaccess."</noscript>";
				echo "<noscript id='wpfc-htaccess-path-data'>".$path.".htaccess"."</noscript>";
				?>
				<script type="text/javascript">
					jQuery(document).ready(function(){
						Wpfc_New_Dialog.dialog("wpfc-modal-htaccess", {close: "default"}, function(modal){
							jQuery("#" + modal.id).find("label.mm-input-label").html(jQuery("#wpfc-htaccess-path-data").html());
							jQuery("#" + modal.id).find("textarea.wiz-inp-readonly-textarea").html(jQuery("#wpfc-htaccess-data").html());
						});
					});
				</script>
				<?php
			}

		}

		public function optionsPage(){
			$wpFastestCacheCombineCss = isset($this->options->wpFastestCacheCombineCss) ? 'checked="checked"' : "";
			$wpFastestCacheGoogleFonts = isset($this->options->wpFastestCacheGoogleFonts) ? 'checked="checked"' : "";
			$wpFastestCacheGzip = isset($this->options->wpFastestCacheGzip) ? 'checked="checked"' : "";
			$wpFastestCacheCombineJs = isset($this->options->wpFastestCacheCombineJs) ? 'checked="checked"' : "";
			$wpFastestCacheCombineJsPowerFul = isset($this->options->wpFastestCacheCombineJsPowerFul) ? 'checked="checked"' : "";
			$wpFastestCacheDisableEmojis = isset($this->options->wpFastestCacheDisableEmojis) ? 'checked="checked"' : "";

			$wpFastestCacheRenderBlocking = isset($this->options->wpFastestCacheRenderBlocking) ? 'checked="checked"' : "";
			
			$wpFastestCacheRenderBlockingCss = isset($this->options->wpFastestCacheRenderBlockingCss) ? 'checked="checked"' : "";

			$wpFastestCacheLanguage = isset($this->options->wpFastestCacheLanguage) ? $this->options->wpFastestCacheLanguage : "eng";
			

			$wpFastestCacheLazyLoad = isset($this->options->wpFastestCacheLazyLoad) ? 'checked="checked"' : "";
			$wpFastestCacheLazyLoad_keywords = isset($this->options->wpFastestCacheLazyLoad_keywords) ? $this->options->wpFastestCacheLazyLoad_keywords : "";
			$wpFastestCacheLazyLoad_placeholder = isset($this->options->wpFastestCacheLazyLoad_placeholder) ? $this->options->wpFastestCacheLazyLoad_placeholder : "default";


			$wpFastestCacheLBC = isset($this->options->wpFastestCacheLBC) ? 'checked="checked"' : "";
			$wpFastestCacheLoggedInUser = isset($this->options->wpFastestCacheLoggedInUser) ? 'checked="checked"' : "";
			$wpFastestCacheMinifyCss = isset($this->options->wpFastestCacheMinifyCss) ? 'checked="checked"' : "";

			$wpFastestCacheMinifyCssPowerFul = isset($this->options->wpFastestCacheMinifyCssPowerFul) ? 'checked="checked"' : "";


			$wpFastestCacheMinifyHtml = isset($this->options->wpFastestCacheMinifyHtml) ? 'checked="checked"' : "";
			$wpFastestCacheMinifyHtmlPowerFul = isset($this->options->wpFastestCacheMinifyHtmlPowerFul) ? 'checked="checked"' : "";

			$wpFastestCacheMinifyJs = isset($this->options->wpFastestCacheMinifyJs) ? 'checked="checked"' : "";

			$wpFastestCacheMobile = isset($this->options->wpFastestCacheMobile) ? 'checked="checked"' : "";
			$wpFastestCacheMobileTheme = isset($this->options->wpFastestCacheMobileTheme) ? 'checked="checked"' : "";
			$wpFastestCacheMobileTheme_themename = isset($this->options->wpFastestCacheMobileTheme_themename) ? $this->options->wpFastestCacheMobileTheme_themename : "";

			$wpFastestCacheNewPost = isset($this->options->wpFastestCacheNewPost) ? 'checked="checked"' : "";
			
			$wpFastestCacheRemoveComments = isset($this->options->wpFastestCacheRemoveComments) ? 'checked="checked"' : "";


			$wpFastestCachePreload = isset($this->options->wpFastestCachePreload) ? 'checked="checked"' : "";
			$wpFastestCachePreload_homepage = isset($this->options->wpFastestCachePreload_homepage) ? 'checked="checked"' : "";
			$wpFastestCachePreload_post = isset($this->options->wpFastestCachePreload_post) ? 'checked="checked"' : "";
			$wpFastestCachePreload_category = isset($this->options->wpFastestCachePreload_category) ? 'checked="checked"' : "";
			$wpFastestCachePreload_customposttypes = isset($this->options->wpFastestCachePreload_customposttypes) ? 'checked="checked"' : "";
			$wpFastestCachePreload_page = isset($this->options->wpFastestCachePreload_page) ? 'checked="checked"' : "";
			$wpFastestCachePreload_tag = isset($this->options->wpFastestCachePreload_tag) ? 'checked="checked"' : "";
			$wpFastestCachePreload_attachment = isset($this->options->wpFastestCachePreload_attachment) ? 'checked="checked"' : "";
			$wpFastestCachePreload_number = isset($this->options->wpFastestCachePreload_number) ? esc_attr($this->options->wpFastestCachePreload_number) : 4;
			$wpFastestCachePreload_restart = isset($this->options->wpFastestCachePreload_restart) ? 'checked="checked"' : "";




			$wpFastestCacheStatus = isset($this->options->wpFastestCacheStatus) ? 'checked="checked"' : "";
			$wpFastestCacheTimeOut = isset($this->cronJobSettings["period"]) ? $this->cronJobSettings["period"] : "";

			$wpFastestCacheUpdatePost = isset($this->options->wpFastestCacheUpdatePost) ? 'checked="checked"' : "";
			$wpFastestCacheWidgetCache = isset($this->options->wpFastestCacheWidgetCache) ? 'checked="checked"' : "";
			?>
			
			<div class="wrap">

				<h2>WP Fastest Cache Options</h2>
				
				<?php settings_errors("wpfc-notice"); ?>

				<div class="tabGroup">
					<?php
						$tabs = array(array("id"=>"wpfc-options","title"=>"Settings"),
									  array("id"=>"wpfc-deleteCache","title"=>"Delete Cache"));
						
						array_push($tabs, array("id"=>"wpfc-imageOptimisation","title"=>"Image Optimization"));
						array_push($tabs, array("id"=>"wpfc-premium","title"=>"Premium"));
						array_push($tabs, array("id"=>"wpfc-exclude","title"=>"Exclude"));
						array_push($tabs, array("id"=>"wpfc-cdn","title"=>"CDN"));
						array_push($tabs, array("id"=>"wpfc-db","title"=>"DB"));

						foreach ($tabs as $key => $value){
							$checked = "";

							//tab of "delete css and js" has been removed so there is need to check it
							if(isset($_POST["wpFastestCachePage"]) && $_POST["wpFastestCachePage"] && $_POST["wpFastestCachePage"] == "deleteCssAndJsCache"){
								$_POST["wpFastestCachePage"] = "deleteCache";
							}

							if(!isset($_POST["wpFastestCachePage"]) && $value["id"] == "wpfc-options"){
								$checked = ' checked="checked" ';
							}else if((isset($_POST["wpFastestCachePage"])) && ("wpfc-".$_POST["wpFastestCachePage"] == $value["id"])){
								$checked = ' checked="checked" ';
							}
							echo '<input '.$checked.' type="radio" id="'.$value["id"].'" name="tabGroup1" style="display:none;">'."\n";
							echo '<label for="'.$value["id"].'">'.$value["title"].'</label>'."\n";
						}
					?>
				    <br>
				    <div class="tab1" style="padding-left:10px;">
						<form method="post" name="wp_manager" action="options.php">
							<?php settings_fields( 'wpfc-group' ); ?>

							<input type="hidden" value="options" name="wpFastestCachePage">
							<div class="questionCon">
								<div class="question">Cache System</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheStatus; ?> id="wpFastestCacheStatus" name="wpFastestCacheStatus"><label for="wpFastestCacheStatus">Enable</label></div>
							</div>




							<?php

							$tester_arr = array(
											"tr-TR",
											"berkatan.com",
											"hciwla.org"
											);
														
							if(in_array(get_bloginfo('language'), $tester_arr) || in_array(str_replace("www.", "", $_SERVER["HTTP_HOST"]), $tester_arr)){ ?>
							<?php } ?>
								


							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
								<?php if(file_exists(WPFC_WP_CONTENT_DIR."/plugins/wp-fastest-cache-premium/pro/library/widget-cache.php")){ ?>
									<?php include_once WPFC_WP_CONTENT_DIR."/plugins/wp-fastest-cache-premium/pro/library/widget-cache.php"; ?>

									<?php if(class_exists("WpfcWidgetCache") && method_exists("WpfcWidgetCache", "add_filter_admin")){ ?>
										<div class="questionCon">
											<div class="question">Widget Cache</div>
											<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheWidgetCache; ?> id="wpFastestCacheWidgetCache" name="wpFastestCacheWidgetCache"><label for="wpFastestCacheWidgetCache">Reduce the number of SQL queries</label></div>
											<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/widget-cache-reduce-the-number-of-sql-queries/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
										</div>
									<?php }else{ ?>
										<div class="questionCon update-needed">
											<div class="question">Widget Cache</div>
											<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheWidgetCache; ?> id="wpFastestCacheWidgetCache"><label for="wpFastestCacheWidgetCache">Reduce the number of SQL queries</label></div>
											<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/widget-cache-reduce-the-number-of-sql-queries/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
										</div>
									<?php } ?>
								<?php }else{ ?>
									<div class="questionCon update-needed">
										<div class="question">Widget Cache</div>
										<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheWidgetCache; ?> id="wpFastestCacheWidgetCache"><label for="wpFastestCacheWidgetCache">Reduce the number of SQL queries</label></div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/widget-cache-reduce-the-number-of-sql-queries/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>
								<?php } ?>
							<?php }else{ ?>
								<div class="questionCon disabled">
									<div class="question">Widget Cache</div>
									<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheWidgetCache; ?> id="wpFastestCacheWidgetCache"><label for="wpFastestCacheWidgetCache">Reduce the number of SQL queries</label></div>
									<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/widget-cache-reduce-the-number-of-sql-queries/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
								</div>
							<?php } ?>



							<div class="questionCon">
								<div class="question">Preload</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCachePreload; ?> id="wpFastestCachePreload" name="wpFastestCachePreload"><label for="wpFastestCachePreload">Create the cache of all the site automatically</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/features/preload-settings/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php include(WPFC_MAIN_PATH."templates/update_now.php"); ?>

							<?php include(WPFC_MAIN_PATH."templates/preload.php"); ?>

							<div class="questionCon">
								<div class="question">Logged-in Users</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheLoggedInUser; ?> id="wpFastestCacheLoggedInUser" name="wpFastestCacheLoggedInUser"><label for="wpFastestCacheLoggedInUser">Don't show the cached version for logged-in users</label></div>
							</div>

							<div class="questionCon">
								<div class="question">Mobile</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMobile; ?> id="wpFastestCacheMobile" name="wpFastestCacheMobile"><label for="wpFastestCacheMobile">Don't show the cached version for desktop to mobile devices</label></div>
							</div>

							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
							<div class="questionCon">
								<div class="question">Mobile Theme</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMobileTheme; ?> id="wpFastestCacheMobileTheme" name="wpFastestCacheMobileTheme"><label for="wpFastestCacheMobileTheme">Create cache for mobile theme</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/mobile-cache/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php 
								$tester_arr_mobile = array(
									"tr-TR",
									"tr",
									"berkatan.com",
									"yenihobiler.com",
									"hobiblogu.com",
									"canliradyodinle.life",
									"canlitvturk.org",
									"haftahaftahamilelik.gen.tr",
									"tooxclusive.com",
									"canliradyodinle.fm"
									);

								if(in_array(get_bloginfo('language'), $tester_arr_mobile) || in_array(str_replace("www.", "", $_SERVER["HTTP_HOST"]), $tester_arr_mobile)){
									include_once WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/templates/mobile_theme.php";
								}
							?>
							
							<?php }else{ ?>
							<div class="questionCon disabled">
								<div class="question">Mobile Theme</div>
								<div class="inputCon"><input type="checkbox" id="wpFastestCacheMobileTheme"><label for="wpFastestCacheMobileTheme">Create cache for mobile theme</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/mobile-cache/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>
							<?php } ?>

							<div class="questionCon">
								<div class="question">New Post</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheNewPost; ?> id="wpFastestCacheNewPost" name="wpFastestCacheNewPost"><label for="wpFastestCacheNewPost">Clear cache files when a post or page is published</label></div>
							</div>

							<?php include(WPFC_MAIN_PATH."templates/newpost.php"); ?>

							<div class="questionCon">
								<div class="question">Update Post</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheUpdatePost; ?> id="wpFastestCacheUpdatePost" name="wpFastestCacheUpdatePost"><label for="wpFastestCacheUpdatePost">Clear cache files when a post or page is updated</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/tutorial/to-clear-cache-after-update"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php include(WPFC_MAIN_PATH."templates/updatepost.php"); ?>


							<div class="questionCon">
								<div class="question">Minify HTML</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMinifyHtml; ?> id="wpFastestCacheMinifyHtml" name="wpFastestCacheMinifyHtml"><label for="wpFastestCacheMinifyHtml">You can decrease the size of page</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/minify-html/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
							<div class="questionCon">
								<div class="question">Minify HTML Plus</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMinifyHtmlPowerFul; ?> id="wpFastestCacheMinifyHtmlPowerFul" name="wpFastestCacheMinifyHtmlPowerFul"><label for="wpFastestCacheMinifyHtmlPowerFul">More powerful minify html</label></div>
							</div>
							<?php }else{ ?>
							<div class="questionCon disabled">
								<div class="question">Minify HTML Plus</div>
								<div class="inputCon"><input type="checkbox" id="wpFastestCacheMinifyHtmlPowerFul"><label for="wpFastestCacheMinifyHtmlPowerFul">More powerful minify html</label></div>
							</div>
							<?php } ?>



							<div class="questionCon">
								<div class="question">Minify Css</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMinifyCss; ?> id="wpFastestCacheMinifyCss" name="wpFastestCacheMinifyCss"><label for="wpFastestCacheMinifyCss">You can decrease the size of css files</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/minify-css/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>



							<?php if(class_exists("WpFastestCachePowerfulHtml") && method_exists("WpFastestCachePowerfulHtml", "minify_css")){ ?>
							<div class="questionCon">
								<div class="question">Minify Css Plus</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMinifyCssPowerFul; ?> id="wpFastestCacheMinifyCssPowerFul" name="wpFastestCacheMinifyCssPowerFul"><label for="wpFastestCacheMinifyCssPowerFul">More powerful minify css</label></div>
							</div>
							<?php }else{ ?>
							<div class="questionCon disabled">
								<div class="question">Minify Css Plus</div>
								<div class="inputCon"><input type="checkbox" id="wpFastestCacheMinifyCssPowerFul"><label for="wpFastestCacheMinifyCssPowerFul">More powerful minify css</label></div>
							</div>
							<?php } ?>


							<div class="questionCon">
								<div class="question">Combine Css</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheCombineCss; ?> id="wpFastestCacheCombineCss" name="wpFastestCacheCombineCss"><label for="wpFastestCacheCombineCss">Reduce HTTP requests through combined css files</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/combine-js-css-files/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
								<?php if(method_exists("WpFastestCachePowerfulHtml", "minify_js_in_body")){ ?>
									<div class="questionCon">
										<div class="question">Minify Js</div>
										<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheMinifyJs; ?> id="wpFastestCacheMinifyJs" name="wpFastestCacheMinifyJs"><label for="wpFastestCacheMinifyJs">You can decrease the size of js files</label></div>
									</div>
								<?php }else{ ?>
									<div class="questionCon update-needed">
										<div class="question">Minify Js</div>
										<div class="inputCon"><input type="checkbox" id="wpFastestCacheMinifyJs"><label for="wpFastestCacheMinifyJs">You can decrease the size of js files</label></div>
									</div>
								<?php } ?>
							<?php }else{ ?>
							<div class="questionCon disabled">
								<div class="question">Minify Js</div>
								<div class="inputCon"><input type="checkbox" id="wpFastestCacheMinifyJs"><label for="wpFastestCacheMinifyJs">You can decrease the size of js files</label></div>
							</div>
							<?php } ?>

							<div class="questionCon">
								<div class="question">Combine Js</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheCombineJs; ?> id="wpFastestCacheCombineJs" name="wpFastestCacheCombineJs"><label for="wpFastestCacheCombineJs">Reduce HTTP requests through combined js files</label> <b style="color:red;">(header)</b></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/combine-js-css-files/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?> 
								<?php if(method_exists("WpFastestCachePowerfulHtml", "combine_js_in_footer")){ ?>
									<div class="questionCon"> <div class="question">Combine Js Plus</div>
										<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheCombineJsPowerFul; ?> id="wpFastestCacheCombineJsPowerFul" name="wpFastestCacheCombineJsPowerFul">
											<label for="wpFastestCacheCombineJsPowerFul">Reduce HTTP requests through combined js files</label> <b style="color:red;">(footer)</b>
										</div> 
									</div>
								<?php }else{ ?>
									<div class="questionCon update-needed">
										<div class="question">Combine Js Plus</div>
										<div class="inputCon"><input type="checkbox" id="wpFastestCacheCombineJsPowerFul"><label for="wpFastestCacheCombineJsPowerFul">Reduce HTTP requests through combined js files</label> <b style="color:red;">(footer)</b></div> 
									</div> 
								<?php } ?>
							<?php }else{ ?>
								<div class="questionCon disabled">
									<div class="question">Combine Js Plus</div>
									<div class="inputCon"><input type="checkbox" id="wpFastestCacheCombineJsPowerFul"><label for="wpFastestCacheCombineJsPowerFul">Reduce HTTP requests through combined js files</label> <b style="color:red;">(footer)</b></div>
								</div>
							<?php } ?>

							<div class="questionCon">
								<div class="question">Gzip</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheGzip; ?> id="wpFastestCacheGzip" name="wpFastestCacheGzip"><label for="wpFastestCacheGzip">Reduce the size of files sent from your server</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/enable-gzip-compression/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<?php
								if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
									include_once(WPFC_MAIN_PATH."templates/nginx_gzip.php"); 
								}
							?>

							<div class="questionCon">
								<div class="question">Browser Caching</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheLBC; ?> id="wpFastestCacheLBC" name="wpFastestCacheLBC"><label for="wpFastestCacheLBC">Reduce page load times for repeat visitors</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/leverage-browser-caching/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>

							<div class="questionCon">
								<div class="question">Disable Emojis</div>
								<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheDisableEmojis; ?> id="wpFastestCacheDisableEmojis" name="wpFastestCacheDisableEmojis"><label for="wpFastestCacheDisableEmojis">You can remove the emoji inline css and wp-emoji-release.min.js</label></div>
								<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/optimization/disableremove-wordpress-emojis/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
							</div>


							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?> 
								<?php if(method_exists("WpFastestCachePowerfulHtml", "render_blocking")){ ?>
									<div class="questionCon">
										<div class="question">Render Blocking Js</div>
										<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheRenderBlocking; ?> id="wpFastestCacheRenderBlocking" name="wpFastestCacheRenderBlocking"><label for="wpFastestCacheRenderBlocking">Remove render-blocking JavaScript</label></div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/render-blocking-js/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>
								<?php }else{ ?>
									<div class="questionCon update-needed">
										<div class="question">Render Blocking Js</div>
										<div class="inputCon"><input type="checkbox" id="wpFastestCacheRenderBlocking" name="wpFastestCacheRenderBlocking"><label for="wpFastestCacheRenderBlocking">Remove render-blocking JavaScript</label></div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/render-blocking-js/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>
								<?php } ?>
							<?php }else{ ?>
								<div class="questionCon disabled">
									<div class="question">Render Blocking Js</div>
									<div class="inputCon"><input type="checkbox" id="wpFastestCacheRenderBlocking" name="wpFastestCacheRenderBlocking"><label for="wpFastestCacheRenderBlocking">Remove render-blocking JavaScript</label></div>
									<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/render-blocking-js/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
								</div>
							<?php } ?>





							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?> 
								<?php if(method_exists("WpFastestCachePowerfulHtml", "google_fonts")){ ?>
									<div class="questionCon">
										<div class="question">Google Fonts</div>
										<div class="inputCon"><input type="checkbox" <?php echo $wpFastestCacheGoogleFonts; ?> id="wpFastestCacheGoogleFonts" name="wpFastestCacheGoogleFonts"><label for="wpFastestCacheGoogleFonts">Load Google Fonts asynchronously </label></div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/google-fonts-optimize-css-delivery/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>
								<?php }else{ ?>
									<div class="questionCon update-needed">
										<div class="question">Google Fonts</div>
										<div class="inputCon"><input type="checkbox" id="wpFastestCacheGoogleFonts" name="wpFastestCacheGoogleFonts"><label for="wpFastestCacheGoogleFonts">Load Google Fonts asynchronously</label></div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/google-fonts-optimize-css-delivery/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>
								<?php } ?>
							<?php }else{ ?>
								<div class="questionCon disabled">
									<div class="question">Google Fonts</div>
									<div class="inputCon"><input type="checkbox" id="wpFastestCacheGoogleFonts" name="wpFastestCacheGoogleFonts"><label for="wpFastestCacheGoogleFonts">Load Google Fonts asynchronously</label></div>
									<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/google-fonts-optimize-css-delivery/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
								</div>
							<?php } ?>



							<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
								<?php if(method_exists("WpFastestCachePowerfulHtml", "lazy_load")){ ?>
									<div class="questionCon">
										<div class="question">Lazy Load</div>
										<div class="inputCon">
											<input type="hidden" value="<?php echo $wpFastestCacheLazyLoad_placeholder; ?>" id="wpFastestCacheLazyLoad_placeholder" name="wpFastestCacheLazyLoad_placeholder">
											<input type="hidden" value="<?php echo $wpFastestCacheLazyLoad_keywords; ?>" id="wpFastestCacheLazyLoad_keywords" name="wpFastestCacheLazyLoad_keywords">
											<input type="checkbox" <?php echo $wpFastestCacheLazyLoad; ?> id="wpFastestCacheLazyLoad" name="wpFastestCacheLazyLoad"><label for="wpFastestCacheLazyLoad">Load images and iframes when they enter the browsers viewport</label>
										</div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/lazy-load-reduce-http-request-and-page-load-time/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>

									<?php 
										if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/templates/lazy-load.php")){
											include_once WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/templates/lazy-load.php"; 
										}
									?>

								<?php }else{ ?>
									<div class="questionCon update-needed">
										<div class="question">Lazy Load</div>
										<div class="inputCon"><input type="checkbox" id="wpFastestCacheLazyLoad" name="wpFastestCacheLazyLoad"><label for="wpFastestCacheLazyLoad">Lazy Load</label></div>
										<div class="get-info"><a target="_blank" href="http://www.wpfastestcache.com/premium/lazy-load-reduce-http-request-and-page-load-time/"><img src="<?php echo plugins_url("wp-fastest-cache/images/info.png"); ?>" /></a></div>
									</div>
								<?php } ?>
							<?php } ?>
							




							<div class="questionCon">
								<div class="question">Language</div>
								<div class="inputCon">
									<select id="wpFastestCacheLanguage" name="wpFastestCacheLanguage">
										<?php
											$lang_array = array(
																"cn" => "中文",
																"de" => "Deutsch",
																"eng" => "English",
																"es" => "Español",
																"fr" => "Français",
																"it" => "Italiana",
																"nl" => "Nederlands",
																"ja" => "日本語",
																"pl" => "Polski",
																"pt" => "Português",
																"ro" => "Română",
																"ru" => "Русский",
																"fi" => "Suomi",
																"sv" => "Svenska",
																"tr" => "Türkçe"
															);
											foreach($lang_array as $lang_array_key => $lang_array_value){
												$option_selected = "";

												if(isset($this->options->wpFastestCacheLanguage) && $this->options->wpFastestCacheLanguage && $this->options->wpFastestCacheLanguage != "eng"){
													if(isset($this->options->wpFastestCacheLanguage) && $this->options->wpFastestCacheLanguage == $lang_array_key){
														$option_selected = 'selected="selected"';
													}
												}else{
													if($lang_array_key == "eng"){
														$option_selected = 'selected="selected"';
													}
												}

												echo '<option '.$option_selected.' value="'.$lang_array_key.'">'.$lang_array_value.'</option>';
											}
										?>
									</select> 
								</div>
							</div>
							<div class="questionCon qsubmit">
								<div class="submit" style="float: none !important;"><input type="submit" value="Submit" class="button-primary"></div>
							</div>
						</form>
				    </div>
				    <div class="tab2">
				    	<div id="container-show-hide-logs" style="display:none; float:right; padding-right:20px; cursor:pointer;">
				    		<span id="show-delete-log">Show Logs</span>
				    		<span id="hide-delete-log" style="display:none;">Hide Logs</span>
				    	</div>

				    	<?php 
			   				if(class_exists("WpFastestCacheStatics")){
				   				$cache_statics = new WpFastestCacheStatics();
				   				$cache_statics->statics();
			   				}else{
			   					?>
					   			<div style="z-index:9999;width: 160px; height: 60px; position: absolute; margin-left: 254px; margin-top: 25px; color: white;">
						    		<div style="font-family:sans-serif;font-size:13px;text-align: center; border-radius: 5px; float: left; background-color: rgb(51, 51, 51); color: white; width: 147px; padding: 20px 50px;">
						    			<label>Only available in Premium version</label>
						    		</div>
						    	</div>
					   			<div style="opacity:0.3;float: right; padding-right: 20px; cursor: pointer;">
						    		<span id="show-delete-log">Show Logs</span>
						    		<span id="hide-delete-log" style="display:none;">Hide Logs</span>
						    	</div>
						    	<h2 style="opacity:0.3;padding-left:20px;padding-bottom:10px;">Cache Statistics</h2>
						    	<div id="wpfc-cache-statics" style="opacity:0.3;width:100%;float:right;margin:15px 0;">
									<style type="text/css">
										#wpfc-cache-statics > div{
											float: left;
											width: 24%;
											text-align: center;
										}
										#wpfc-cache-statics > div > p{
											font-size: 1.3em;
											font-weight: 600;
											margin-top: 10px;
										}
										#wpfc-cache-statics-desktop, #wpfc-cache-statics-mobile, #wpfc-cache-statics-css {
											border-right: 1px solid #ddd;
										}
									</style>
									<div id="wpfc-cache-statics-desktop" style="margin-left:1%;">
										<i class="flaticon-desktop1"></i> 
										<p id="wpfc-cache-statics-desktop-data">12.3Kb / 1 Items</p>
									</div>
									<div id="wpfc-cache-statics-mobile">
										<i class="flaticon-smart"></i> 
										<p id="wpfc-cache-statics-mobile-data">12.4Kb / 1 Items</p>
									</div>
									<div id="wpfc-cache-statics-css">
										<i class="flaticon-css4"></i> 
										<p id="wpfc-cache-statics-css-data">278.2Kb / 9 Items</p>
									</div>
									<div id="wpfc-cache-statics-js">
										<i class="flaticon-js"></i> 
										<p id="wpfc-cache-statics-js-data">338.4Kb / 16 Items</p>
									</div>
								</div>
			   					<?php
			   				}
				   		?>

				   		<div class="exclude_section_clear" style=" margin-left: 3%; width: 95%; margin-bottom: 20px; margin-top: 0;"><div></div></div>

				   		<h2 id="delete-cache-h2" style="padding-left:20px;padding-bottom:10px;">Delete Cache</h2>
				    	<form method="post" name="wp_manager" class="delete-line" action="options.php">
							<?php settings_fields( 'wpfc-group' ); ?>
				    		<input type="hidden" value="deleteCache" name="wpFastestCachePage">
				    		<div class="questionCon qsubmit left">
				    			<div class="submit"><input type="submit" value="Delete Cache" class="button-primary"></div>
				    		</div>
				    		<div class="questionCon right">
				    			<div style="padding-left:11px;">
				    			<label>You can delete all cache files</label><br>
				    			<label>Target folder</label> <b><?php echo $this->getWpContentDir("/cache/all"); ?></b>
				    			</div>
				    		</div>
				   		</form>
				   		<form method="post" name="wp_manager" class="delete-line" style="height: 120px;" action="options.php">
				   			<?php settings_fields( 'wpfc-group' ); ?>
				    		<input type="hidden" value="deleteCssAndJsCache" name="wpFastestCachePage">
				    		<div class="questionCon qsubmit left">
				    			<div class="submit"><input type="submit" value="Delete Cache and Minified CSS/JS" class="button-primary"></div>
				    		</div>
				    		<div class="questionCon right">
				    			<div style="padding-left:11px;">
				    			<label>If you modify any css file, you have to delete minified css files</label><br>
				    			<label>All cache files will be removed as well</label><br>
				    			<label>Target folder</label> <b><?php echo $this->getWpContentDir("/cache/all"); ?></b><br>
				    			<label>Target folder</label> <b><?php echo $this->getWpContentDir("/cache/wpfc-minified"); ?></b>
				    			</div>
				    		</div>
				   		</form>
				   		<?php 
				   				if(class_exists("WpFastestCacheLogs")){
					   				$logs = new WpFastestCacheLogs("delete");
					   				$logs->printLogs();
				   				}
				   		?>

				   		<div class="exclude_section_clear" style=" margin-left: 3%; width: 95%; margin-bottom: 12px; margin-top: 0;"><div></div></div>


				   		<h2 style="padding-bottom:10px;padding-left:20px;float:left;">Timeout Rules</h2>

				    	<!-- samples start: clones -->
				    	<div class="wpfc-timeout-rule-line" style="display:none;">
							<div class="wpfc-timeout-rule-line-left">
								<select name="wpfc-timeout-rule-prefix">
										<option selected="" value=""></option>
										<option value="all">All</option>
										<option value="homepage">Home Page</option>
										<option value="startwith">Start With</option>
										<option value="contain">Contain</option>
										<option value="exact">Exact</option>
								</select>
							</div>
							<div class="wpfc-timeout-rule-line-middle">
								<input type="text" name="wpfc-timeout-rule-content">
								<input type="text" name="wpfc-timeout-rule-schedule">
								<input type="text" name="wpfc-timeout-rule-hour">
								<input type="text" name="wpfc-timeout-rule-minute">
							</div>
						</div>
						<!-- item sample -->
	    				<div class="wpfc-timeout-item" tabindex="1" prefix="" content="" schedule="" style="position: relative;display:none;">
	    					<div class="app">
				    			<div class="wpfc-timeout-item-form-title">Title M</div>
				    			<span class="wpfc-timeout-item-details wpfc-timeout-item-url"></span>
	    					</div>
			    		</div>
		    			<!-- samples end -->

				    	<div style="float:left;margin-top:-37px;padding-left:628px;">
				    		<?php
				    			$disable_wp_cron = '';
				    			if(defined("DISABLE_WP_CRON")){
						    		if((is_bool(DISABLE_WP_CRON) && DISABLE_WP_CRON == true) || 
						    			(is_string(DISABLE_WP_CRON) && preg_match("/^true$/i", DISABLE_WP_CRON))){
						    			$disable_wp_cron = 'disable-wp-cron="true" ';

						    			include(WPFC_MAIN_PATH."templates/disable_wp_cron.php");
						    		}
						    	}
				    		?>
				    		<button type="button" <?php echo $disable_wp_cron;?> class="wpfc-add-new-timeout-button wpfc-dialog-buttons" style="display: inline-block;padding: 4px 10px;">
				    			<span>Add New Rule</span>
							</button>
				    	</div>

				    	<div class="wpfc-timeout-list" style="display: block;width:98%;float:left;">

				    	</div>

				    	<?php
				    		include(WPFC_MAIN_PATH."templates/timeout.php");
				    	?>

				    	<form method="post" name="wp_manager">
				    		<input type="hidden" value="timeout" name="wpFastestCachePage">
				    		<div class="wpfc-timeout-rule-container"></div>
				    	</form>
				    	<script type="text/javascript">

					    	<?php
					    		$schedules_rules = array();
						    	$crons = _get_cron_array();

						    	foreach ((array)$crons as $cron_key => $cron_value) {
						    		foreach ( (array) $cron_value as $hook => $events ) {
						    			if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
						    				if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
							    				foreach ( (array) $events as $event_key => $event ) {
							    					$tmp_array = array();

							    					if($id[1]){
							    						// new cronjob which is (wp_fastest_cache_d+)
								    					$tmp_std = json_decode($event["args"][0]);

								    					$tmp_array = array("schedule" => $event["schedule"],
								    									   "prefix" => $tmp_std->prefix,
								    									   "content" => esc_attr($tmp_std->content));

								    					if(isset($tmp_std->hour) && isset($tmp_std->minute)){
								    						$tmp_array["hour"] = $tmp_std->hour;
								    						$tmp_array["minute"] = $tmp_std->minute;
								    					}
							    					}else{
							    						// old cronjob which is (wp_fastest_cache)
							    						$tmp_array = array("schedule" => $event["schedule"],
								    									   "prefix" => "all",
								    									   "content" => "all");
							    					}
							    				}

							    				array_push($schedules_rules, $tmp_array);
						    				}
						    			}
						    		}
						    	}

					    		echo "WpFcTimeout.schedules = ".json_encode($this->cron_add_minute(array())).";";

					    		if(count($schedules_rules) > 0){
					    			echo "WpFcTimeout.init(".json_encode($schedules_rules).");";
					    		}else{
					    			echo "WpFcTimeout.init();";
					    		} ?>
				    	</script>
				    </div>


				    
				    <div class="tab3" style="display:none;"> </div>




				    <?php if(class_exists("WpFastestCacheImageOptimisation")){ ?>
					    <div class="tab4">
					    	<h2 style="padding-left:20px;padding-bottom:10px;">Optimize Image Tool</h2>

					    		<?php $xxx = new WpFastestCacheImageOptimisation(); ?>
					    		<?php $xxx->statics(); ?>
						    	<?php $xxx->imageList(); ?>
					    </div>
				    <?php }else{ ?>
						<div class="tab4" style="">
							<div style="z-index:9999;width: 160px; height: 60px; position: absolute; margin-left: 254px; margin-top: 74px; color: white;">
								<div style="font-family:sans-serif;font-size:13px;text-align: center; border-radius: 5px; float: left; background-color: rgb(51, 51, 51); color: white; width: 147px; padding: 20px 50px;">
									<label>Only available in Premium version</label>
								</div>
							</div>
							<h2 style="opacity: 0.3;padding-left:20px;padding-bottom:10px;">Optimize Image Tool</h2>
							<div id="container-show-hide-image-list" style="opacity: 0.3;float: right; padding-right: 20px; cursor: pointer;">
								<span id="show-image-list">Show Images</span>
								<span id="hide-image-list" style="display:none;">Hide Images</span>
							</div>
							<div style="opacity: 0.3;width:100%;float:left;" id="wpfc-image-static-panel">
								<div style="float: left; width: 100%;">
									<div style="float:left;padding-left: 22px;padding-right:15px;">
										<div style="display: inline-block;">
											<div style="width: 150px; height: 150px; position: relative; border-top-left-radius: 150px; border-top-right-radius: 150px; border-bottom-right-radius: 150px; border-bottom-left-radius: 150px; background-color: #ffcc00;">
												

												<div style="position: absolute; top: 0px; left: 0px; width: 150px; height: 150px; border-top-left-radius: 150px; border-top-right-radius: 150px; border-bottom-right-radius: 150px; border-bottom-left-radius: 150px; clip: rect(0px 150px 150px 75px);">
													<div style="position: absolute; top: 0px; left: 0px; width: 150px; height: 150px; border-radius: 150px; clip: rect(0px, 75px, 150px, 0px); transform: rotate(109.62deg); background-color: rgb(255, 165, 0); border-spacing: 109.62px;" id="wpfc-pie-chart-little"></div>
												</div>


												<div style="display:none;position: absolute; top: 0px; left: 0px; width: 150px; height: 150px; border-top-left-radius: 150px; border-top-right-radius: 150px; border-bottom-right-radius: 150px; border-bottom-left-radius: 150px; clip: rect(0px 150px 150px 25px); -webkit-transform: rotate(0deg); transform: rotate(0deg);" id="wpfc-pie-chart-big-container-first">
													<div style="position: absolute; top: 0px; left: 0px; width: 150px; height: 150px; border-top-left-radius: 150px; border-top-right-radius: 150px; border-bottom-right-radius: 150px; border-bottom-left-radius: 150px; clip: rect(0px 75px 150px 0px); -webkit-transform: rotate(180deg); transform: rotate(180deg); background-color: #FFA500;"></div>
												</div>
												<div style="display:none;position: absolute; top: 0px; left: 0px; width: 150px; height: 150px; border-top-left-radius: 150px; border-top-right-radius: 150px; border-bottom-right-radius: 150px; border-bottom-left-radius: 150px; clip: rect(0px 150px 150px 75px); -webkit-transform: rotate(180deg); transform: rotate(180deg);" id="wpfc-pie-chart-big-container-second-right">
													<div style="position: absolute; top: 0px; left: 0px; width: 150px; height: 150px; border-top-left-radius: 150px; border-top-right-radius: 150px; border-bottom-right-radius: 150px; border-bottom-left-radius: 150px; clip: rect(0px 75px 150px 0px); -webkit-transform: rotate(90deg); transform: rotate(90deg); background-color: #FFA500;" id="wpfc-pie-chart-big-container-second-left"></div>
												</div>

											</div>
											<div style="width: 114px;height: 114px;margin-top: -133px;background-color: white;margin-left: 18px;position: absolute;border-radius: 150px;">
												<p style="text-align:center;margin:27px 0 0 0;color: black;">Succeed</p>
												<p style="text-align: center; font-size: 18px; font-weight: bold; font-family: verdana; margin: -2px 0px 0px; color: black;" id="wpfc-optimized-statics-percent" class="">30.45</p>
												<p style="text-align:center;margin:0;color: black;">%</p>
											</div>
										</div>
									</div>
									<div style="float: left;padding-left:12px;" id="wpfc-statics-right">
										<ul style="list-style: none outside none;float: left;">
											<li>
												<div style="background-color: rgb(29, 107, 157);width:15px;height:15px;float:left;margin-top:4px;border-radius:5px;"></div>
												<div style="float:left;padding-left:6px;">All</div>
												<div style="font-size: 14px; font-weight: bold; color: black; float: left; width: 65%; margin-left: 5px;" id="wpfc-optimized-statics-total_image_number" class="">7196</div>
											</li>
											<li>
												<div style="background-color: rgb(29, 107, 157);width:15px;height:15px;float:left;margin-top:4px;border-radius:5px;"></div>
												<div style="float:left;padding-left:6px;">Pending</div>
												<div style="font-size: 14px; font-weight: bold; color: black; float: left; width: 65%; margin-left: 5px;" id="wpfc-optimized-statics-pending" class="">5002</div>
											</li>
											<li>
												<div style="background-color: #FF0000;width:15px;height:15px;float:left;margin-top:4px;border-radius:5px;"></div>
												<div style="float:left;padding-left:6px;">Errors</div>
												<div style="font-size: 14px; font-weight: bold; color: black; float: left; width: 65%; margin-left: 5px;" id="wpfc-optimized-statics-error" class="">3</div>
											</li>
										</ul>
										<ul style="list-style: none outside none;float: left;">
											<li>
												<div style="background-color: rgb(61, 207, 60);width:15px;height:15px;float:left;margin-top:4px;border-radius:5px;"></div>
												<div style="float:left;padding-left:6px;"><span>Optimized Images</span></div>
												<div style="font-size: 14px; font-weight: bold; color: black; float: left; width: 65%; margin-left: 5px;" id="wpfc-optimized-statics-optimized" class="">2191</div>
											</li>

											<li>
												<div style="background-color: rgb(61, 207, 60);width:15px;height:15px;float:left;margin-top:4px;border-radius:5px;"></div>
												<div style="float:left;padding-left:6px;"><span>Total Reduction</span></div>
												<div style="font-size: 14px; font-weight: bold; color: black; float: left; width: 80%; margin-left: 5px;" id="wpfc-optimized-statics-reduction" class="">78400.897</div>
											</li>
											<li></li>
										</ul>

										<ul style="list-style: none outside none;float: left;">
											<li>
												<h1 style="margin-top:0;float:left;">Credit: <span style="display: inline-block; height: 16px; width: auto;min-width:25px;" id="wpfc-optimized-statics-credit" class="">9910</span></h1>
												<span id="buy-image-credit">More</span>
											</li>
											<li>
												<input type="submit" class="button-primary" value="Optimize All" id="wpfc-optimize-images-button" style="width:100%;height:110px;">
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
				    <?php } ?>
				    <div class="tab5">
				    	<?php
				    		if(!get_option("WpFc_api_key")){
				    			update_option("WpFc_api_key", md5(microtime(true)));
				    		}

				    		if(!defined('WPFC_API_KEY')){ // for download_error.php
				    			define("WPFC_API_KEY", get_option("WpFc_api_key"));
				    		}
				    	?>
				    	<div id="wpfc-premium-container">
				    		<div class="wpfc-premium-step">
				    			<div class="wpfc-premium-step-header">
				    				<label>Discover Features</label>
				    			</div>
				    			<div class="wpfc-premium-step-content">
				    				In the premium version there are some new features which speed up the sites more.
				    			</div>
				    			<div class="wpfc-premium-step-image">
				    				<img src="<?php echo plugins_url("wp-fastest-cache/images/rocket.png"); ?>">
				    			</div>
				    			<div class="wpfc-premium-step-footer">
				    				<h1 id="new-features-h1">New Features</h1>
				    				<ul>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/image-optimization/">Image Optimization</a></li>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/mobile-cache/">Mobile Cache</a></li>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/minify-html-plus/">Minify HTML Plus</a></li>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/combine-js-plus/">Combine Js Plus</a></li>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/minify-js/">Minify Js</a></li>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/delete-cache-logs/">Delete Cache Logs</a></li>
				    					<li><a target="_blank" style="text-decoration: none;color: #444;" href="http://www.wpfastestcache.com/premium/cache-statics/">Cache Statics</a></li>
				    				</ul>
				    			</div>
				    		</div>
				    		<div class="wpfc-premium-step">
				    			<div class="wpfc-premium-step-header">
				    				<label>Checkout</label>
				    			</div>
				    			<div class="wpfc-premium-step-content">
				    				You need to pay before downloading the premium version.
				    			</div>
				    			<div class="wpfc-premium-step-image">
				    				<img width="140px" height="140px" src="<?php echo plugins_url("wp-fastest-cache/images/dollar.png"); ?>" />
				    			</div>
				    			<div class="wpfc-premium-step-footer">
				    				<?php
				    					if(get_bloginfo('language') == "tr-TR"){
				    						$premium_price = "150TL";
				    					}else{
					    					$premium_price = "$49.99";
				    					}

				    				?>
				    				<h1 style="float:left;" id="just-h1">Just</h1><h1><span style="margin-left:5px;" id="wpfc-premium-price"><?php echo $premium_price; ?></span></h1>
				    				<p>The download button will be available after paid. You can buy the premium version now.</p>

				    				<?php if(!preg_match("/Caiu\s*Na/i", get_bloginfo("name")) && !preg_match("/(caiuna|escort|porn)/i", $_SERVER["HTTP_HOST"])){ ?>
					    				<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
						    					<button id="wpfc-buy-premium-button" type="submit" class="wpfc-btn primaryDisableCta" style="width:200px;">
							    					<span>Purchased</span>
							    				</button>
						    				<?php }else{ ?>
							    				<form action="https://api.wpfastestcache.net/paypal/buypremium/" method="post">
							    					<input type="hidden" name="ip" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>">
							    					<input type="hidden" name="wpfclang" value="<?php echo isset($this->options->wpFastestCacheLanguage) ? esc_attr($this->options->wpFastestCacheLanguage) : ""; ?>">
							    					<input type="hidden" name="bloglang" value="<?php echo get_bloginfo('language'); ?>">
							    					<input type="hidden" name="hostname" value="<?php echo str_replace(array("http://", "www."), "", $_SERVER["HTTP_HOST"]); ?>">
								    				<button id="wpfc-buy-premium-button" type="submit" class="wpfc-btn primaryCta" style="width:200px;">
								    					<span>Buy</span>
								    				</button>
							    				</form>
						    			<?php } ?>
					    			<?php } ?>


				    			</div>
				    		</div>
				    		<div class="wpfc-premium-step">
				    			<div class="wpfc-premium-step-header">
				    				<label>Download & Update</label>
				    			</div>
				    			<div class="wpfc-premium-step-content">
				    				You can download and update the premium when you want if you paid.
				    			</div>
				    			<div class="wpfc-premium-step-image" style="">
				    				<img src="<?php echo plugins_url("wp-fastest-cache/images/download.png"); ?>">
				    			</div>
				    			<div class="wpfc-premium-step-footer">
				    				<h1 id="get-now-h1">Get It Now!</h1>
				    				<p>Please don't delete the free version. Premium version works with the free version.</p>


				    				<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
				    					<a href="http://www.wpfastestcache.com/blog/premium-update-before-v1-3-6/">
						    				<button id="wpfc-update-premium-button" class="wpfc-btn primaryDisableCta" style="width:200px;">
						    					<span data-type="update">Update</span>
						    				</button>
				    					</a>
				    				<?php }else{ ?>
					    				<button class="wpfc-btn primaryCta" id="wpfc-download-premium-button" class="wpfc-btn primaryDisableCta" style="width:200px;">
					    					<span data-type="download">Download</span>
					    				</button>

					    				<?php include(WPFC_MAIN_PATH."templates/download.html"); ?> 

					    				<script type="text/javascript">
					    					jQuery("#wpfc-download-premium-button").click(function(){
					    						//jQuery("#revert-loader-toolbar").show();

					    						Wpfc_New_Dialog.dialog("wpfc-modal-downloaderror", {close: "default"});

					    						var wpfc_api_url = '<?php echo "http://api.wpfastestcache.net/premium/newdownload/".str_replace(array("http://", "www."), "", $_SERVER["HTTP_HOST"])."/".get_option("WpFc_api_key"); ?>';
					    						jQuery("div[id^='wpfc-modal-downloaderror'] a.wpfc-download-now").attr("href", wpfc_api_url);

					    						// jQuery("body").append(data);
					    						// jQuery("#wpfc-download-now").attr("href", wpfc_api_url);
					    						// Wpfc_Dialog.dialog("wpfc-modal-downloaderror");
					    						// jQuery("#revert-loader-toolbar").hide();
						    					
						    					// jQuery.get("<?php echo plugins_url('wp-fastest-cache/templates'); ?>/download.html", function( data ) {
						    					// });
					    					});
					    				</script>
				    				<?php } ?>
				    				<!--
				    				<button class="wpfc-btn primaryNegativeCta" style="width:200px;">
				    					<span>Update</span>
				    					<label>(v 1.0)</label>
				    				</button>
				    			-->
				    			</div>
				    		</div>
				    	</div>
				    </div>
				    <div class="tab6" style="padding-left:20px;">
				    	<!-- samples start: clones -->
				    	<div class="wpfc-exclude-rule-line" style="display:none;">
							<div class="wpfc-exclude-rule-line-left">
								<select name="wpfc-exclude-rule-prefix">
										<option selected="" value=""></option>
										<option value="homepage">Home Page</option>
										<option value="category">Categories</option>
										<option value="tag">Tags</option>
										<option value="archive">Archives</option>
										<option value="post">Posts</option>
										<option value="page">Pages</option>
										<option value="attachment">Attachments</option>
										<option value="startwith">Start With</option>
										<option value="contain">Contain</option>
										<option value="exact">Exact</option>
										<option value="googleanalytics">has Google Analytics Parameters</option>
								</select>
							</div>
							<div class="wpfc-exclude-rule-line-middle">
								<input type="text" name="wpfc-exclude-rule-content" style="width:390px;">
								<input type="text" name="wpfc-exclude-rule-type" style="width:90px;">
							</div>
						</div>
						<!-- item sample -->
	    				<div class="wpfc-exclude-item" tabindex="1" type="" prefix="" content="" style="position: relative;display:none;">
	    					<div class="app">
				    			<div class="wpfc-exclude-item-form-title">Title M</div>
				    			<span class="wpfc-exclude-item-details wpfc-exclude-item-url"></span>
	    					</div>
			    		</div>
		    			<!-- samples end -->

		    			<h2 style="padding-bottom:10px;float:left;">Exclude Pages</h2>

				    	<div style="float:left;margin-top:-37px;padding-left:608px;">
					    	<button data-type="page" type="button" class="wpfc-add-new-exclude-button wpfc-dialog-buttons" style="display: inline-block;padding: 4px 10px;">
					    		<span>Add New Rule</span>
					    	</button>
				    	</div>

				    	<div class="wpfc-exclude-page-list" style="display: block;width:98%;float:left;">

				    	</div>

				    	<div class="exclude_section_clear">
				    		<div></div>
				    	</div>


				    	<h2 style="padding-bottom:10px;float:left;">Exclude User-Agents</h2>

				    	<div style="float:left;margin-top:-37px;padding-left:608px;">
					    	<button data-type="useragent" type="button" class="wpfc-add-new-exclude-button wpfc-dialog-buttons" style="display: inline-block;padding: 4px 10px;">
					    		<span>Add New Rule</span>
					    	</button>
				    	</div>

				    	<div class="wpfc-exclude-useragent-list" style="display: block;width:98%;float:left;">

				    	</div>


				    	<div class="exclude_section_clear">
				    		<div></div>
				    	</div>



				    	<h2 style="padding-bottom:10px;float:left;">Exclude Cookies</h2>

				    	<div style="float:left;margin-top:-37px;padding-left:608px;">
					    	<button data-type="cookie" type="button" class="wpfc-add-new-exclude-button wpfc-dialog-buttons" style="display: inline-block;padding: 4px 10px;">
					    		<span>Add New Rule</span>
					    	</button>
				    	</div>

				    	<div class="wpfc-exclude-cookie-list" style="display: block;width:98%;float:left;">

				    	</div>


				    	<div class="exclude_section_clear">
				    		<div></div>
				    	</div>


				    	<h2 style="padding-bottom:10px;float:left;">Exclude CSS</h2>

				    	<div style="float:left;margin-top:-37px;padding-left:608px;">
					    	<button data-type="css" type="button" class="wpfc-add-new-exclude-button wpfc-dialog-buttons" style="display: inline-block;padding: 4px 10px;">
					    		<span>Add New Rule</span>
					    	</button>
				    	</div>

				    	<div class="wpfc-exclude-css-list" style="display: block;width:98%;float:left;">

				    	</div>



				    	<div class="exclude_section_clear">
				    		<div></div>
				    	</div>



				    	<h2 style="padding-bottom:10px;float:left;">Exclude JS</h2>

				    	<div style="float:left;margin-top:-37px;padding-left:608px;">
					    	<button data-type="js" type="button" class="wpfc-add-new-exclude-button wpfc-dialog-buttons" style="display: inline-block;padding: 4px 10px;">
					    		<span>Add New Rule</span>
					    	</button>
				    	</div>

				    	<div class="wpfc-exclude-js-list" style="display: block;width:98%;float:left;">

				    	</div>


				    	<?php
				    		include(WPFC_MAIN_PATH."templates/exclude.php");
				    	?>

				    	<form method="post" name="wp_manager">
				    		<input type="hidden" value="exclude" name="wpFastestCachePage">
				    		<div class="wpfc-exclude-rule-container"></div>
				    		<!-- <div class="questionCon qsubmit">
								<div class="submit"><input type="submit" class="button-primary" value="Submit"></div>
							</div> -->
				    	</form>
				    	<script type="text/javascript">

					    	<?php 
					    		if($rules_json = get_option("WpFastestCacheExclude")){
					    			?>WpFcExcludePages.init(<?php echo $rules_json; ?>);<?php
					    		}else{
					    			?>WpFcExcludePages.init();<?php
					    		}
					    	?>
				    	</script>
				    </div>

				    <div class="tab7" style="padding-left:20px;">
				    	<h2 style="padding-bottom:10px;">CDN Settings</h2>
				    	<div>
				    		<div class="integration-page" style="display: block;width:98%;float:left;">

				    			<div wpfc-cdn-name="maxcdn" class="int-item int-item-left">
				    				<img style="border-radius:50px;" src="<?php echo plugins_url("wp-fastest-cache/images/stackpath.png"); ?>" />
				    				<div class="app">
				    					<div style="font-weight:bold;font-size:14px;">CDN by StackPath</div>
				    					<p>Secure and accelerate your web sites</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>


				    			<div wpfc-cdn-name="other" class="int-item">
				    				<img src="<?php echo plugins_url("wp-fastest-cache/images/othercdn.png"); ?>" />
				    				<div class="app">
				    					<div style="font-weight:bold;font-size:14px;">Other CDN Providers</div>
				    					<p>You can use any cdn provider.</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    			<div wpfc-cdn-name="photon" class="int-item int-item-left">
				    				<img src="<?php echo plugins_url("wp-fastest-cache/images/photoncdn.png"); ?>" />
				    				<div class="app">
				    					<div style="font-weight:bold;font-size:14px;">CDN by Photon</div>
				    					<p>Wordpress Content Delivery Network Services</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>


				    			<div wpfc-cdn-name="cloudflare" class="int-item">
				    				<img style="border-radius:50px;" src="<?php echo plugins_url("wp-fastest-cache/images/cloudflare.png"); ?>" />
				    				<div class="app">
				    					<div style="font-weight:bold;font-size:14px;">CDN by Cloudflare</div>
				    					<p>CDN, DNS, DDoS protection and security</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    		</div>
				    	</div>
				    	<script type="text/javascript">
				    		(function() {
					    		<?php
					    			$cdn_values = get_option("WpFastestCacheCDN");

					    			if($cdn_values){
					    				$std_obj = json_decode($cdn_values);
					    				$cdn_values_arr = array();

					    				if(is_array($std_obj)){
											$cdn_values_arr = $std_obj;
										}else{
											array_push($cdn_values_arr, $std_obj);
										}

					    				foreach ($cdn_values_arr as $cdn_key => $cdn_value) {
						    				if($cdn_value->id == "amazonaws" || $cdn_value->id == "keycdn" || $cdn_value->id == "cdn77"){
						    					$cdn_value->id = "other";
						    				}
						    				?>jQuery("div[wpfc-cdn-name='<?php echo $cdn_value->id;?>']").find("div.meta").addClass("isConnected");<?php
					    				}
					    			}
					    		?>
				    			jQuery("div.integration-page .int-item").click(function(e){
				    				jQuery("#revert-loader-toolbar").show();
				    				jQuery("div[id='wpfc-modal-maxcdn'], div[id='wpfc-modal-other'], div[id='wpfc-modal-photon']").remove();

					    			jQuery.ajax({
										type: 'GET', 
										url: ajaxurl,
										cache: false,
										data : {"action": "wpfc_cdn_options"},
										dataType : "json",
										success: function(data){
											if(data.id){
												if(data.id == "keycdn" || data.id == "cdn77" || data.id == "amazonaws"){
													data.id = "other";
												}
											}


											WpfcCDN.init({"id" : jQuery(e.currentTarget).attr("wpfc-cdn-name"),
							    				"template_main_url" : "<?php echo plugins_url('wp-fastest-cache/templates/cdn'); ?>",
							    				"values" : data
							    			});


											
											// if(data.id && jQuery(e.currentTarget).attr("wpfc-cdn-name") != data.id){
											// 	Wpfc_New_Dialog.dialog("wpfc-modal-onlyonecdn", {close: "default"});

											// 	Wpfc_New_Dialog.show_button("close");
												
											// 	jQuery("#revert-loader-toolbar").hide();
											// }else{
							    // 				WpfcCDN.init({"id" : jQuery(e.currentTarget).attr("wpfc-cdn-name"),
							    // 					"template_main_url" : "<?php echo plugins_url('wp-fastest-cache/templates/cdn'); ?>",
							    // 					"values" : data
							    // 				});
											// }
										}
									});
				    			});
				    		})();
				    	</script>
				    </div>

				    <div class="tab8" style="padding-left:20px;">
				    	<h2 style="padding-bottom:10px;">Database Cleanup</h2>
				    	<div>

			    		<?php if(!$this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){ ?>
				    			<style type="text/css">
				    				div.tab8 h2{
				    					opacity: 0.3 !important;
				    				}
				    				div.tab8 .integration-page{
				    					opacity: 0.3 !important;
				    				}
				    			</style>
				    			
				    			<div style="z-index:9999;width: 160px; height: 60px; position: absolute; margin-left: 230px; margin-top: 25px; color: white;">
						    		<div style="font-family:sans-serif;font-size:13px;text-align: center; border-radius: 5px; float: left; background-color: rgb(51, 51, 51); color: white; width: 147px; padding: 20px 50px;">
						    			<label>Only available in Premium version</label>
						    		</div>
						    	</div>
			    		<?php } ?>

				    		<div class="integration-page" style="display: block;width:98%;float:left;">

				    			<div wpfc-db-name="all_warnings" class="int-item int-item-left">
				    				<div style="float:left;width:45px;height:45px;margin-right:12px;">
				    					<span class="flaticon-technology"></span> 
				    				</div>
				    				<div class="app db">
				    					<div style="font-weight:bold;font-size:14px;">ALL <span class="db-number">(0)</span></div>
				    					<p>Run the all options</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    			<div wpfc-db-name="post_revisions" class="int-item int-item-right">
				    				<div style="float:left;width:45px;height:45px;margin-right:12px;">
				    					<span class="flaticon-draft"></span> 
				    				</div>
				    				<div class="app db">
				    					<div style="font-weight:bold;font-size:14px;">Post Revisions <span class="db-number">(0)</span></div>
				    					<p>Clean the all post revisions</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    			<div wpfc-db-name="trashed_contents" class="int-item int-item-left">
				    				<div style="float:left;width:45px;height:45px;margin-right:12px;">
				    					<span class="flaticon-recycling"></span> 
				    				</div>
				    				<div class="app db">
				    					<div style="font-weight:bold;font-size:14px;">Trashed Contents <span class="db-number">(0)</span></div>
				    					<p>Clean the all trashed posts & pages</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    			<div wpfc-db-name="trashed_spam_comments" class="int-item int-item-right">
				    				<div style="float:left;width:45px;height:45px;margin-right:12px;">
				    					<span class="flaticon-interface"></span> 
				    				</div>
				    				<div class="app db">
				    					<div style="font-weight:bold;font-size:14px;">Trashed & Spam Comments <span class="db-number">(0)</span></div>
				    					<p>Clean the all comments from trash & spam</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    			<div wpfc-db-name="trackback_pingback" class="int-item int-item-left">
				    				<div style="float:left;width:45px;height:45px;margin-right:12px;">
				    					<span class="flaticon-pingback"></span> 
				    				</div>
				    				<div class="app db">
				    					<div style="font-weight:bold;font-size:14px;">Trackbacks and Pingbacks <span class="db-number">(0)</span></div>
				    					<p>Clean the all trackbacks and pingbacks</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>

				    			<div wpfc-db-name="transient_options" class="int-item int-item-right">
				    				<div style="float:left;width:45px;height:45px;margin-right:12px;">
				    					<span class="flaticon-file"></span> 
				    				</div>
				    				<div class="app db">
				    					<div style="font-weight:bold;font-size:14px;">Transient Options <span class="db-number">(0)</span></div>
				    					<p>Clean the all transient options</p>
				    				</div>
				    				<div class="meta"></div>
				    			</div>




				    		</div>
				    	</div>
				    </div>

				    <?php include_once(WPFC_MAIN_PATH."templates/permission_error.html"); ?>












			</div>

			<div class="omni_admin_sidebar">
				<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
				<?php }else{ ?>
				<div class="omni_admin_sidebar_section" style="padding:0 !important;border:none !important;background:none !important;">
					<!-- ads area -->
				</div>
				<?php } ?>
				<div class="omni_admin_sidebar_section" id="vote-us">
					<h3 style="color: antiquewhite;">Rate Us</h3>
					<ul>
						<li><label>If you like it, Please vote and support us.</label></li>
					</ul>
					<script>
						jQuery("#vote-us").click(function(){
							var win=window.open("http://wordpress.org/support/view/plugin-reviews/wp-fastest-cache?free-counter?rate=5#postform", '_blank');
							win.focus();
						});
					</script>
				</div>
				<div class="omni_admin_sidebar_section">
					<?php if(class_exists("WpFastestCachePowerfulHtml")){ ?>
						<h3>Premium Support</h3>
						<ul>
							<li><label>You can send an email</label> <a target="_blank"><label>fastestcache@gmail.com</label></a></li>
						</ul>
					<?php }else{ ?>
						<h3>Having Issues?</h3>
						<ul>
							<li><label>You can create a ticket</label> <a target="_blank" href="http://wordpress.org/support/plugin/wp-fastest-cache"><label>WordPress support forum</label></a></li>
						</ul>
					<?php } ?>
				</div>
			</div>

			<div id="wpfc-plugin-setup-warning" class="mainContent" style="display:none;border:1px solid black">
			        <div class="pageView"style="display: block;">
			            <div class="fakeHeader">
			                <h3 class="title-h3">Error Occured</h3>
			            </div>
			            <div class="fieldRow active">

			            </div>
			            <div class="pagination">
			                <div class="next" style="text-align: center;float: none;">
			                    <button class="wpfc-btn primaryCta" id="wpfc-read-tutorial">
			                        <span class="label">Continue</span>
			                    </button>
			                </div>
			            </div>
			        </div>
			</div>
			<script type="text/javascript">
				var WPFC_SPINNER = {
					id: false,
					number: false,
					init: function(id, number){
						this.id = id;
						//this.number = number;
						this.set_number();
						this.click_event();
					},
					set_number: function(){
						this.number = jQuery("#" + this.id + " input.wpfc-form-spinner-input").val();
						this.number = parseInt(this.number);
					},
					click_event: function(){
						var id = this.id;
						var number = this.number;

						jQuery("#" + this.id + " .wpfc-form-spinner-up, #" + this.id + " .wpfc-form-spinner-down").click(function(e){
							if(jQuery(this).attr('class').match(/up$/)){
								number = number + 2;
							}else if(jQuery(this).attr('class').match(/down$/)){
								number = number - 2;
							}

							number = number < 2 ? 2 : number;
							number = number > 12 ? 12 : number;

							jQuery("#" + id + " .wpfc-form-spinner-number").text(number);
							jQuery("#" + id + " input.wpfc-form-spinner-input").val(number);
						});
					}
				};
			</script>
			<script type="text/javascript">
				jQuery("#wpFastestCachePreload").click(function(){
					if(typeof jQuery(this).attr("checked") != "undefined"){
						if(jQuery("div[id^='wpfc-modal-preload-']").length === 0){
							Wpfc_New_Dialog.dialog("wpfc-modal-preload", {close: function(){
								Wpfc_New_Dialog.clone.find("div.window-content input").each(function(){
									if(typeof jQuery(this).attr("checked") != "undefined"){
										jQuery("div.tab1 div[template-id='wpfc-modal-preload'] div.window-content input[name='" + jQuery(this).attr("name") + "']").attr("checked", true);
									}else{
										jQuery("div.tab1 div[template-id='wpfc-modal-preload'] div.window-content input[name='" + jQuery(this).attr("name") + "']").attr("checked", false);
									}

									Wpfc_New_Dialog.clone.remove();
								});
							}});

							Wpfc_New_Dialog.show_button("close");
							WPFC_SPINNER.init("wpfc-form-spinner-preload", 6);
						}
					}
				});
			</script>

			<?php if(!class_exists("WpFastestCacheImageOptimisation")){ ?>
				<div id="wpfc-premium-tooltip" style="display:none;width: 160px; height: 60px; position: absolute; margin-left: 354px; margin-top: 112px; color: white;">
					<div style="float:left;width:13px;">
						<div style="width: 0px; height: 0px; border-top: 6px solid transparent; border-right: 6px solid #333333; border-bottom: 6px solid transparent; float: right; margin-right: 0px; margin-top: 25px;"></div>
					</div>
					<div style="font-family:sans-serif;font-size:13px;text-align: center; border-radius: 5px; float: left; background-color: rgb(51, 51, 51); color: white; width: 147px; padding: 10px 0px;">
						<label>Only available in Premium version</label>
					</div>
				</div>

				<script type="text/javascript">
					jQuery("div.questionCon.disabled").click(function(e){
						if(typeof window.wpfc.tooltip != "undefined"){
							clearTimeout(window.wpfc.tooltip);
						}

						var inputCon = jQuery(e.currentTarget).find(".inputCon");
						var left = 30;

						jQuery(e.currentTarget).children().each(function(i, child){
							left = left + jQuery(child).width();
						});

						jQuery("#wpfc-premium-tooltip").css({"margin-left" : left + "px", "margin-top" : (jQuery(e.currentTarget).offset().top - jQuery(".tab1").offset().top + 25) + "px"});
						jQuery("#wpfc-premium-tooltip").fadeIn( "slow", function() {
							window.wpfc.tooltip = setTimeout(function(){ jQuery("#wpfc-premium-tooltip").hide(); }, 1000);
						});
						return false;
					});
				</script>
			<?php }else{ ?>
				<script type="text/javascript">
					jQuery(".update-needed").click(function(){
						if(jQuery("div[id^='wpfc-modal-updatenow-']").length === 0){
							Wpfc_New_Dialog.dialog("wpfc-modal-updatenow", {close: function(){
								Wpfc_New_Dialog.clone.find("div.window-content input").each(function(){
									if(jQuery(this).attr("checked")){
										var id = jQuery(this).attr("action-id");
										jQuery("div.tab1 div[template-id='wpfc-modal-updatenow'] div.window-content input#" + id).attr("checked", true);
									}
								});

								Wpfc_New_Dialog.clone.remove();
							}});

							Wpfc_New_Dialog.show_button("close");
						}

						return false;
					});
				</script>
			<?php } ?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					//if "Mobile Theme" is selected, "Mobile" is selected as well
					jQuery("#wpFastestCacheMobileTheme").click(function(e){
						if(jQuery(this).is(':checked')){
							jQuery("#wpFastestCacheMobile").attr('checked', true);
						}
					});

					//if "Mobile Theme" has been selected, "Mobile" option cannot be changed
					jQuery("#wpFastestCacheMobile").click(function(e){
						if(jQuery("#wpFastestCacheMobileTheme").is(':checked')){
							jQuery(this).attr('checked', true);
						}
					});
				});
			</script>
			<script>
				jQuery(document).ready(function() {
					Wpfclang.init("<?php echo $wpFastestCacheLanguage; ?>");
				});
			</script>
			<?php
			if(isset($_SERVER["SERVER_SOFTWARE"]) && $_SERVER["SERVER_SOFTWARE"] && !preg_match("/iis/i", $_SERVER["SERVER_SOFTWARE"]) && !preg_match("/nginx/i", $_SERVER["SERVER_SOFTWARE"])){
				if(!isset($_POST["wpFastestCachePage"])){
					$this->check_htaccess();
				}
			}
		}
	}
?>