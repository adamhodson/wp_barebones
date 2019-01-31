<?php
	class WpFastestCacheCreateCache extends WpFastestCache{
		public $options = array();
		public $cdn;
		private $startTime;
		private $blockCache = false;
		private $err = "";
		public $cacheFilePath = "";
		public $exclude_rules = false;
		public $preload_user_agent = false;
		public $current_page_type = false;

		public function __construct(){
			//to fix: PHP Notice: Undefined index: HTTP_USER_AGENT
			$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] ? strip_tags($_SERVER['HTTP_USER_AGENT']) : "Empty User Agent";
			
			if(preg_match("/(WP\sFastest\sCache\sPreload(\siPhone\sMobile)?\s*Bot)/", $_SERVER['HTTP_USER_AGENT'])){
				$this->preload_user_agent = true;
			}else{
				$this->preload_user_agent = false;
			}


			$this->options = $this->getOptions();

			$this->set_cdn();

			$this->set_cache_file_path();

			$this->set_exclude_rules();

			if(isset($this->options->wpFastestCacheDisableEmojis) && $this->options->wpFastestCacheDisableEmojis){
				add_action('init', array($this, 'disable_emojis'));
			}
		}

		public function disable_emojis(){
			remove_action('wp_head', 'print_emoji_detection_script', 7);
			remove_action('admin_print_scripts', 'print_emoji_detection_script');
			remove_filter('the_content_feed', 'wp_staticize_emoji');
			remove_filter('comment_text_rss', 'wp_staticize_emoji');
			remove_action('wp_print_styles', 'print_emoji_styles');
			remove_action('admin_print_styles', 'print_emoji_styles');
			remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
		}

		public function detect_current_page_type(){
			if(preg_match("/\?/", $_SERVER["REQUEST_URI"])){
				return true;
			}
			
			if(preg_match("/^\/wp-json/", $_SERVER["REQUEST_URI"])){
				return true;
			}

			if(is_front_page()){
				echo "<!--WPFC_PAGE_TYPE_homepage-->";
			}else if(is_category()){
				echo "<!--WPFC_PAGE_TYPE_category-->";
			}else if(is_tag()){
				echo "<!--WPFC_PAGE_TYPE_tag-->";
			}else if(is_singular('post')){
				echo "<!--WPFC_PAGE_TYPE_post-->";
			}else if(is_page()){
				echo "<!--WPFC_PAGE_TYPE_page-->";
			}else if(is_attachment()){
				echo "<!--WPFC_PAGE_TYPE_attachment-->";
			}else if(is_archive()){
				echo "<!--WPFC_PAGE_TYPE_archive-->";
			}
		}

		public function set_exclude_rules(){
			if($json_data = get_option("WpFastestCacheExclude")){
				$this->exclude_rules = json_decode($json_data);
			}
		}

		public function set_cache_file_path(){

			if($this->isMobile() && isset($this->options->wpFastestCacheMobile)){
				if(class_exists("WpFcMobileCache") && isset($this->options->wpFastestCacheMobileTheme)){
					$wpfc_mobile = new WpFcMobileCache();
					$this->cacheFilePath = $this->getWpContentDir("/cache/wpfc-mobile-cache").$_SERVER["REQUEST_URI"];
				}
			}else{
				if($this->isPluginActive('gtranslate/gtranslate.php')){
					if(isset($_SERVER["HTTP_X_GT_LANG"])){
						$this->cacheFilePath = $this->getWpContentDir("/cache/all/").$_SERVER["HTTP_X_GT_LANG"].$_SERVER["REQUEST_URI"];
					}else if(isset($_SERVER["REDIRECT_URL"]) && $_SERVER["REDIRECT_URL"] != "/index.php"){
						$this->cacheFilePath = $this->getWpContentDir("/cache/all/").$_SERVER["REDIRECT_URL"];
					}else if(isset($_SERVER["REQUEST_URI"])){
						$this->cacheFilePath = $this->getWpContentDir("/cache/all/").$_SERVER["REQUEST_URI"];
					}
				}else{
					$this->cacheFilePath = $this->getWpContentDir("/cache/all/").$_SERVER["REQUEST_URI"];
				}
			}

			//WPML language switch
			//https://wpml.org/forums/topic/wpml-language-switch-wp-fastest-cache-issue/
			$language_negotiation_type = apply_filters('wpml_setting', false, 'language_negotiation_type');
			if ($this->isPluginActive('sitepress-multilingual-cms/sitepress.php') && 2 == $language_negotiation_type){
			    $current_language = apply_filters('wpml_current_language', false);
			    
			    $this->cacheFilePath = str_replace('/cache/all/', '/cache/all/'.$current_language.'/', $this->cacheFilePath);
			    $this->cacheFilePath = str_replace('/cache/wpfc-mobile-cache/', '/cache/wpfc-mobile-cache/'.$current_language.'/', $this->cacheFilePath);
			}



			$this->cacheFilePath = $this->cacheFilePath ? rtrim($this->cacheFilePath, "/")."/" : "";
			$this->cacheFilePath = str_replace("/cache/all//", "/cache/all/", $this->cacheFilePath);


			if(strlen($_SERVER["REQUEST_URI"]) > 1){ // for the sub-pages
				if(!preg_match("/\.html/i", $_SERVER["REQUEST_URI"])){
					if($this->is_trailing_slash()){
						if(!preg_match("/\/$/", $_SERVER["REQUEST_URI"])){
							if(defined('WPFC_CACHE_QUERYSTRING') && WPFC_CACHE_QUERYSTRING){
							
							}else if(preg_match("/gclid\=/i", $this->cacheFilePath)){
								
							}else if(preg_match("/fbclid\=/i", $this->cacheFilePath)){

							}else if(preg_match("/utm_(source|medium|campaign|content|term)/i", $this->cacheFilePath)){

							}else{
								$this->cacheFilePath = false;
							}
						}
					}else{
						//toDo
					}
				}
			}
			
			$this->remove_url_paramters();

			// to decode path if it is not utf-8
			if($this->cacheFilePath){
				$this->cacheFilePath = urldecode($this->cacheFilePath);
			}
		}

		public function remove_url_paramters(){
			$action = false;

			//to remove query strings for cache if Google Click Identifier are set
			if(preg_match("/gclid\=/i", $this->cacheFilePath)){
				$action = true;
			}

			//to remove query strings for cache if facebook parameters are set
			if(preg_match("/fbclid\=/i", $this->cacheFilePath)){
				$action = true;
			}

			//to remove query strings for cache if google analytics parameters are set
			if(preg_match("/utm_(source|medium|campaign|content|term)/i", $this->cacheFilePath)){
				$action = true;
			}

			if($action){
				if(strlen($_SERVER["REQUEST_URI"]) > 1){ // for the sub-pages

					$this->cacheFilePath = preg_replace("/\/*\?.+/", "", $this->cacheFilePath);
					$this->cacheFilePath = $this->cacheFilePath."/";

					define('WPFC_CACHE_QUERYSTRING', true);
				}
			}
		}

		public function set_cdn(){
			$cdn_values = get_option("WpFastestCacheCDN");
			if($cdn_values){
				$std_obj = json_decode($cdn_values);
				$arr = array();

				if(is_array($std_obj)){
					$arr = $std_obj;
				}else{
					array_push($arr, $std_obj);
				}

				foreach ($arr as $key => &$std) {
					$std->originurl = trim($std->originurl);
					$std->originurl = trim($std->originurl, "/");
					$std->originurl = preg_replace("/http(s?)\:\/\/(www\.)?/i", "", $std->originurl);

					$std->cdnurl = trim($std->cdnurl);
					$std->cdnurl = trim($std->cdnurl, "/");
					
					if(!preg_match("/https\:\/\//", $std->cdnurl)){
						$std->cdnurl = "//".preg_replace("/http(s?)\:\/\/(www\.)?/i", "", $std->cdnurl);
					}
				}
				
				$this->cdn = $arr;
			}
		}

		public function checkShortCode($content){
			if(preg_match("/\[wpfcNOT\]/", $content)){
				if(!is_home() || !is_archive()){
					$this->blockCache = true;
				}
				$content = str_replace("[wpfcNOT]", "", $content);
			}
			return $content;
		}

		public function createCache(){		
			if(isset($this->options->wpFastestCacheStatus)){

				// to check logged-in user
				if(isset($this->options->wpFastestCacheLoggedInUser) && $this->options->wpFastestCacheLoggedInUser == "on"){
					foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
						if(preg_match("/wordpress_logged_in/i", $cookie_key)){
							ob_start(array($this, "cdn_rewrite"));

							return 0;
						}
					}
				}

				// to exclude admin users
				$users_groups = get_users(array("role" => "administrator", "fields" => array("user_login")));
				foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
					if(preg_match("/wordpress_logged_in/i", $cookie_key)){
						foreach ($users_groups as $user_key => $user_value) {
							if(preg_match("/^".preg_quote($user_value->user_login, "/")."/", $cookie_value)){
								ob_start(array($this, "cdn_rewrite"));

								return 0;
							}
						}
					}
				}

				// to check comment author
				foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
					if(preg_match("/comment_author_/i", $cookie_key)){
						ob_start(array($this, "cdn_rewrite"));

						return 0;
					}
				}

				// to check woocommerce_items_in_cart
				foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
					//if(preg_match("/^wp\_woocommerce\_session/", $cookie_key)){
					if(preg_match("/^woocommerce\_items\_in\_cart/", $cookie_key)){
						ob_start(array($this, "cdn_rewrite"));
						
						return 0;
					}
				}

				if(isset($_COOKIE) && isset($_COOKIE['safirmobilswitcher'])){
					ob_start(array($this, "cdn_rewrite"));

					return 0;
				}

				if(isset($_COOKIE) && isset($_COOKIE["wptouch-pro-view"])){
					if($this->is_wptouch_smartphone()){
						if($_COOKIE["wptouch-pro-view"] == "desktop"){
							ob_start(array($this, "cdn_rewrite"));

							return 0;
						}
					}
				}

				if(preg_match("/\?/", $_SERVER["REQUEST_URI"]) && !preg_match("/\/\?fdx\_switcher\=true/", $_SERVER["REQUEST_URI"])){ // for WP Mobile Edition
					if(preg_match("/\?amp(\=1)?/i", $_SERVER["REQUEST_URI"])){
						//
					}else if(defined('WPFC_CACHE_QUERYSTRING') && WPFC_CACHE_QUERYSTRING){
						//
					}else{
						ob_start(array($this, "cdn_rewrite"));
						
						return 0;
					}
				}

				if(preg_match("/(".$this->get_excluded_useragent().")/", $_SERVER['HTTP_USER_AGENT'])){
					return 0;
				}

				if(isset($_SERVER['REQUEST_URI']) && preg_match("/(\/){2}$/", $_SERVER['REQUEST_URI'])){
					return 0;
				}

				if(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST"){
					return 0;
				}

				if(preg_match("/^https/i", get_option("home")) && !is_ssl()){
					//Must be secure connection
					return 0;
				}

				if(!preg_match("/^https/i", get_option("home")) && is_ssl()){
					//must be normal connection
					if(!$this->isPluginActive('really-simple-ssl/rlrsssl-really-simple-ssl.php')){
						if(!$this->isPluginActive('really-simple-ssl-pro/really-simple-ssl-pro.php')){
							if(!$this->isPluginActive('ssl-insecure-content-fixer/ssl-insecure-content-fixer.php')){
								if(!$this->isPluginActive('https-redirection/https-redirection.php')){
									if(!$this->isPluginActive('better-wp-security/better-wp-security.php')){
										return 0;
									}
								}
							}
						}
					}
				}

				if(preg_match("/www\./i", get_option("home")) && !preg_match("/www\./i", $_SERVER['HTTP_HOST'])){
					return 0;
				}

				if(!preg_match("/www\./i", get_option("home")) && preg_match("/www\./i", $_SERVER['HTTP_HOST'])){
					return 0;
				}

				//different domain names may be used for different languages
				if($this->isPluginActive('polylang/polylang.php')){
					if(!preg_match("/".preg_quote(str_replace("www.", "", $_SERVER["HTTP_HOST"]), "/")."/i", get_option("home"))){
						return 0;
					}
				}

				if($this->exclude_page()){
					//echo "<!-- Wp Fastest Cache: Exclude Page -->"."\n";
					return 0;
				}

				// http://mobiledetect.net/ does not contain the following user-agents
				if(preg_match("/Nokia309|Casper_VIA/i", $_SERVER['HTTP_USER_AGENT'])){
					return 0;
				}

				if(preg_match("/Empty\sUser\sAgent/i", $_SERVER['HTTP_USER_AGENT'])){ // not to show the cache for command line
					return 0;
				}


				//to show cache version via php if htaccess rewrite rule does not work
				if(!$this->preload_user_agent && $this->cacheFilePath && @file_exists($this->cacheFilePath."index.html")){
					if($content = @file_get_contents($this->cacheFilePath."index.html")){
						if(defined('WPFC_REMOVE_VIA_FOOTER_COMMENT') && WPFC_REMOVE_VIA_FOOTER_COMMENT){
							die($content);
						}else{
							$content = $content."<!-- via php -->";
							die($content);
						}
					}
				}else{
					if($this->isMobile()){
						if(class_exists("WpFcMobileCache") && isset($this->options->wpFastestCacheMobileTheme)){
							if(isset($this->options->wpFastestCacheMobileTheme_themename) && $this->options->wpFastestCacheMobileTheme_themename){
								$create_cache = true;
							}else if($this->isPluginActive('wptouch/wptouch.php') || $this->isPluginActive('wptouch-pro/wptouch-pro.php')){
								//to check that user-agent exists in wp-touch's list or not
								if($this->is_wptouch_smartphone()){
									$create_cache = true;
								}else{
									$create_cache = false;
								}
							}else if($this->isPluginActive('any-mobile-theme-switcher/any-mobile-theme-switcher.php')){
								if($this->is_anymobilethemeswitcher_mobile()){
									$create_cache = true;
								}else{
									$create_cache = false;
								}
							}else{
								if((preg_match('/iPhone/', $_SERVER['HTTP_USER_AGENT']) && preg_match('/Mobile/', $_SERVER['HTTP_USER_AGENT'])) || (preg_match('/Android/', $_SERVER['HTTP_USER_AGENT']) && preg_match('/Mobile/', $_SERVER['HTTP_USER_AGENT']))){
									$create_cache = true;
								}else{
									$create_cache = false;
								}
							}
						}else if(!isset($this->options->wpFastestCacheMobile) && !isset($this->options->wpFastestCacheMobileTheme)){
							$create_cache = true;
						}else{
							$create_cache = false;
						}
					}else{
						$create_cache = true;
					}

					if($create_cache){
						$this->startTime = microtime(true);


						add_action('wp', array($this, "detect_current_page_type"));
						add_action('get_footer', array($this, "detect_current_page_type"));
						add_action('get_footer', array($this, "wp_print_scripts_action"));

						ob_start(array($this, "callback"));
					}
				}
			}
		}

		public function wp_print_scripts_action(){
			echo "<!--WPFC_FOOTER_START-->";
		}

		public function ignored($buffer){
			$list = array(
						"\/wp\-comments\-post\.php",
						"\/sitemap\.xml",
						"\/sitemap_index\.xml",
						"\/wp\-login\.php",
						"\/robots\.txt",
						"\/wp\-cron\.php",
						"\/wp\-content",
						"\/wp\-admin",
						"\/wp\-includes",
						"\/index\.php",
						"\/xmlrpc\.php",
						"\/wp\-api\/",
						"leaflet\-geojson\.php",
						"\/clientarea\.php"
					);
			if($this->isPluginActive('woocommerce/woocommerce.php')){
				if($this->current_page_type != "homepage"){
					global $post;

					if(isset($post->ID) && $post->ID){
						if(function_exists("wc_get_page_id")){
							$woocommerce_ids = array();

							//wc_get_page_id('product')
							//wc_get_page_id('product-category')
							
							array_push($woocommerce_ids, wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('receipt'), wc_get_page_id('confirmation'), wc_get_page_id('myaccount'));

							if (in_array($post->ID, $woocommerce_ids)) {
								return true;
							}
						}
					}

					//"\/product"
					//"\/product-category"

					array_push($list, "\/cart", "\/checkout", "\/receipt", "\/confirmation", "\/wc-api\/");
				}
			}

			if($this->isPluginActive('wp-easycart/wpeasycart.php')){
				array_push($list, "\/cart");
			}

			if($this->isPluginActive('easy-digital-downloads/easy-digital-downloads.php')){
				array_push($list, "\/cart", "\/checkout");
			}

			if(preg_match("/".implode("|", $list)."/i", $_SERVER["REQUEST_URI"])){
				return true;
			}

			return false;
		}

		public function exclude_page($buffer = false){
			$preg_match_rule = "";
			$request_url = urldecode(trim($_SERVER["REQUEST_URI"], "/"));

			if($this->exclude_rules){

				foreach((array)$this->exclude_rules as $key => $value){
					$value->type = isset($value->type) ? $value->type : "page";

					if($value->prefix == "googleanalytics"){
						if(preg_match("/utm_(source|medium|campaign|content|term)/i", $request_url)){
							return true;
						}
					}else if($buffer && isset($value->prefix) && $value->prefix && ($value->type == "page")){
						$value->content = trim($value->content);
						$value->content = trim($value->content, "/");

						if(preg_match("/^(homepage|category|tag|post|page|archive|attachment)$/", $value->prefix)){
							if(preg_match('/<\!--WPFC_PAGE_TYPE_'.$value->prefix.'-->/i', $buffer)){
								return true;
							} 
						}else if($value->prefix == "exact"){
							if(strtolower($value->content) == strtolower($request_url)){
								return true;	
							}
						}else{
							if($value->prefix == "startwith"){
								$preg_match_rule = "^".preg_quote($value->content, "/");
							}else if($value->prefix == "contain"){
								$preg_match_rule = preg_quote($value->content, "/");
							}

							if(preg_match("/".$preg_match_rule."/i", $request_url)){
								return true;
							}
						}
					}else if($value->type == "useragent"){
						if(preg_match("/".preg_quote($value->content, "/")."/i", $_SERVER['HTTP_USER_AGENT'])){
							return true;
						}
					}else if($value->type == "cookie"){
						if(isset($_SERVER['HTTP_COOKIE'])){
							if(preg_match("/".preg_quote($value->content, "/")."/i", $_SERVER['HTTP_COOKIE'])){
								return true;
							}
						}
					}
				}
				
			}
			return false;
		}

		public function is_json($buffer){
			if(isset($_SERVER["HTTP_ACCEPT"]) && preg_match("/json/i", $_SERVER["HTTP_ACCEPT"])){
				return true;
			}

			if(preg_match("/^\/wp-json/", $_SERVER["REQUEST_URI"])){
				return true;
			}

			if(preg_match("/^\s*\{\s*[\"\']/i", $buffer)){
				return true;
			}

			if(preg_match("/^\s*\[\s*\{\s*[\"\']/i", $buffer)){
				return true;
			}

			return false;
		}

		public function is_xml($buffer){
			if(preg_match("/^\s*\<\?xml/i", $buffer)){
				return true;
			}
			return false;
		}

		public function set_current_page_type($buffer){
			preg_match('/<\!--WPFC_PAGE_TYPE_([a-z]+)-->/i', $buffer, $out);

			$this->current_page_type = isset($out[1]) ? $out[1] : false;
		}

		public function callback($buffer){
			$this->set_current_page_type($buffer);

			$buffer = $this->checkShortCode($buffer);

			// for Wordfence: not to cache 503 pages
			if(defined('DONOTCACHEPAGE') && $this->isPluginActive('wordfence/wordfence.php')){
				if(function_exists("http_response_code") && http_response_code() == 503){
					return $buffer."<!-- DONOTCACHEPAGE is defined as TRUE -->";
				}
			}

			if($this->exclude_page($buffer)){
				$buffer = preg_replace('/<\!--WPFC_PAGE_TYPE_[a-z]+-->/i', '', $buffer);	
				return $buffer;
			}

			$buffer = preg_replace('/<\!--WPFC_PAGE_TYPE_[a-z]+-->/i', '', $buffer);

			if(preg_match("/Mediapartners-Google|Google\sWireless\sTranscoder/i", $_SERVER['HTTP_USER_AGENT'])){
				return $buffer;
			}else if($this->is_xml($buffer)){
				return $buffer;
			}else if (is_user_logged_in() || $this->isCommenter()){
				return $buffer;
			}else if($this->is_json($buffer)){
				return $buffer;
			}else if($this->isPasswordProtected($buffer)){
				return $buffer."<!-- Password protected content has been detected -->";
			}else if($this->isWpLogin($buffer)){
				return $buffer."<!-- wp-login.php -->";
			}else if($this->hasContactForm7WithCaptcha($buffer)){
				return $buffer."<!-- This page was not cached because ContactForm7's captcha -->";
			}else if(is_404() || preg_match("/<title>404\sNot\sFound<\/title>/", $buffer)){
				return $buffer;
			}else if($this->ignored($buffer)){
				return $buffer;
			}else if($this->blockCache === true){
				return $buffer."<!-- wpfcNOT has been detected -->";
			}else if(isset($_GET["preview"])){
				return $buffer."<!-- not cached -->";
			}else if($this->checkHtml($buffer)){
				return $buffer."<!-- html is corrupted -->";
			}else if((function_exists("http_response_code")) && (http_response_code() == 301 || http_response_code() == 302)){
				return $buffer;
			}else if(!$this->cacheFilePath){
				return $buffer."<!-- permalink_structure ends with slash (/) but REQUEST_URI does not end with slash (/) -->";
			}else{
				$content = $buffer;

				if(isset($this->options->wpFastestCacheRenderBlocking) && method_exists("WpFastestCachePowerfulHtml", "render_blocking")){
					if(class_exists("WpFastestCachePowerfulHtml")){
						if(!$this->is_amp($content)){
							$powerful_html = new WpFastestCachePowerfulHtml();

							if(isset($this->options->wpFastestCacheRenderBlockingCss)){
								$content = $powerful_html->render_blocking($content, true);
							}else{
								$content = $powerful_html->render_blocking($content);
							}
						}
					}
				}

				if(isset($this->options->wpFastestCacheCombineCss)){
					require_once "css-utilities.php";
					$css = new CssUtilities($this, $content);
					$content = $css->combineCss();
					unset($css);
				}else if(isset($this->options->wpFastestCacheMinifyCss)){
					require_once "css-utilities.php";
					$css = new CssUtilities($this, $content);
					$content = $css->minifyCss();
					unset($css);
				}

				if(isset($this->options->wpFastestCacheCombineJs) || isset($this->options->wpFastestCacheMinifyJs) || isset($this->options->wpFastestCacheCombineJsPowerFul)){
					require_once "js-utilities.php";
				}

				if(isset($this->options->wpFastestCacheCombineJs)){

					$head_new = $this->get_header($content);

				    if($head_new){
						if(isset($this->options->wpFastestCacheMinifyJs) && $this->options->wpFastestCacheMinifyJs){
							$js = new JsUtilities($this, $head_new, true);
						}else{
							$js = new JsUtilities($this, $head_new);
						}

						$tmp_head = $js->combine_js();

						$content = str_replace($head_new, $tmp_head, $content);

						unset($r);
						unset($js);
						unset($tmp_head);
						unset($head_new);
				    }
				}

				if(class_exists("WpFastestCachePowerfulHtml")){
					if(!isset($powerful_html)){
						$powerful_html = new WpFastestCachePowerfulHtml();
					}

					$powerful_html->set_html($content);

					if(isset($this->options->wpFastestCacheCombineJsPowerFul) && method_exists("WpFastestCachePowerfulHtml", "combine_js_in_footer")){
						if(isset($this->options->wpFastestCacheMinifyJs) && $this->options->wpFastestCacheMinifyJs){
							$content = $powerful_html->combine_js_in_footer($this, true);
						}else{
							$content = $powerful_html->combine_js_in_footer($this);
						}
					}
					
					if(isset($this->options->wpFastestCacheRemoveComments)){
						$content = $powerful_html->remove_head_comments();
					}

					if(isset($this->options->wpFastestCacheMinifyHtmlPowerFul)){
						$content = $powerful_html->minify_html();
					}

					if(isset($this->options->wpFastestCacheMinifyJs) && method_exists("WpFastestCachePowerfulHtml", "minify_js_in_body")){
						$content = $powerful_html->minify_js_in_body($this, $this->exclude_rules);
					}
				}

				if($this->err){
					return $buffer."<!-- ".$this->err." -->";
				}else{
					$content = $this->cacheDate($content);
					$content = $this->minify($content);
					$content = str_replace("<!--WPFC_FOOTER_START-->", "", $content);


					if(isset($this->options->wpFastestCacheLazyLoad)){
						$execute_lazy_load = true;
						
						// to disable Lazy Load if the page is amp
						if($this->is_amp($content)){
							$execute_lazy_load = false;
						}
						
						// to disable for Ajax Load More on the pages
						if($this->isPluginActive('ajax-load-more/ajax-load-more.php') && preg_match("/\/page\/\d+\//", $_SERVER["REQUEST_URI"])){
							$execute_lazy_load = false;
						}

						if($execute_lazy_load){
							if(!class_exists("WpFastestCacheLazyLoad")){
								include_once $this->get_premium_path("lazy-load.php");
							}

							$content = $powerful_html->lazy_load($content);

							if(method_exists("WpFastestCacheLazyLoad",'get_js_source_new')){
								$lazy_load_js = WpFastestCacheLazyLoad::get_js_source_new();
							}else if(method_exists("WpFastestCacheLazyLoad",'get_js_source')){
								$lazy_load_js = WpFastestCacheLazyLoad::get_js_source();
							}

							$content = preg_replace("/\s*<\/head\s*>/i", $lazy_load_js."</head>", $content, 1);
						}
					}


					$content = $this->cdn_rewrite($content);


					// WP Hide & Security Enhancer
					if($this->isPluginActive('wp-hide-security-enhancer/wp-hide.php')){
						global $wph;
						$content = $wph->functions->content_urls_replacement($content, $wph->functions->get_replacement_list());
					}

					$content = $this->fix_pre_tag($content, $buffer);

					if($this->cacheFilePath){
						$this->createFolder($this->cacheFilePath, $content);
					}

					return $content."<!-- need to refresh to see cached version -->";
				}
			}
		}

		public function fix_pre_tag($content, $buffer){
			if(preg_match("/<pre[^\>]*>/i", $buffer)){
				preg_match_all("/<pre[^\>]*>((?!<\/pre>).)+<\/pre>/is", $buffer, $pre_buffer);
				preg_match_all("/<pre[^\>]*>((?!<\/pre>).)+<\/pre>/is", $content, $pre_content);

				if(isset($pre_content[0]) && isset($pre_content[0][0])){
					foreach ($pre_content[0] as $key => $value){
						/*
						location ~ / {
						    set $path /path/$1/index.html;
						}
						*/
						$pre_buffer[0][$key] = preg_replace('/\$(\d)/', '\\\$$1', $pre_buffer[0][$key]);

						

						$content = preg_replace("/".preg_quote($value, "/")."/", $pre_buffer[0][$key], $content);
					}
				}
			}
			
			return $content;
		}

		public function cdn_rewrite($content){
			if($this->cdn){
				$content = preg_replace_callback("/(srcset|src|href|data-cvpsrc|data-cvpset|data-thumb|data-bg-url|data-large_image|data-lazyload|data-source-url|data-srcsmall|data-srclarge|data-srcfull|data-slide-img|data-lazy-original)\s{0,2}\=[\'\"]([^\'\"]+)[\'\"]/i", array($this, 'cdn_replace_urls'), $content);

				//url()
				$content = preg_replace_callback("/(url)\(([^\)\>]+)\)/i", array($this, 'cdn_replace_urls'), $content);

				//{"concatemoji":"http:\/\/your_url.com\/wp-includes\/js\/wp-emoji-release.min.js?ver=4.7"}
				$content = preg_replace_callback("/\{\"concatemoji\"\:\"[^\"]+\"\}/i", array($this, 'cdn_replace_urls'), $content);
				
				//<script>var loaderRandomImages=["https:\/\/www.site.com\/wp-content\/uploads\/2016\/12\/image.jpg"];</script>
				$content = preg_replace_callback("/[\"\']([^\'\"]+)[\"\']\s*\:\s*[\"\']https?\:\\\\\/\\\\\/[^\"\']+[\"\']/i", array($this, 'cdn_replace_urls'), $content);

				// <script>
				// jsFileLocation:"//domain.com/wp-content/plugins/revslider/public/assets/js/"
				// </script>
				$content = preg_replace_callback("/(jsFileLocation)\s*\:[\"\']([^\"\']+)[\"\']/i", array($this, 'cdn_replace_urls'), $content);


				// <form data-product_variations="[{&quot;src&quot;:&quot;//domain.com\/img.jpg&quot;}]">
				$content = preg_replace_callback("/data-product_variations\=[\"\'][^\"\']+[\"\']/i", array($this, 'cdn_replace_urls'), $content);
			}

			return $content;
		}

		public function get_header($content){
			$head_first_index = strpos($content, "<head");
			$head_last_index = strpos($content, "</head>");

			return substr($content, $head_first_index, ($head_last_index-$head_first_index + 1));
		}

		public function minify($content){
			$content = preg_replace("/<\/html>\s+/", "</html>", $content);
			$content = str_replace("\r", "", $content);
			return isset($this->options->wpFastestCacheMinifyHtml) ? preg_replace("/^\s+/m", "", ((string) $content)) : $content;
		}

		public function checkHtml($buffer){
			if(preg_match('/<html[^\>]*>/si', $buffer) && preg_match('/<body[^\>]*>/si', $buffer)){
				return false;
			}
			// if(strlen($buffer) > 10){
			// 	return false;
			// }

			return true;
		}

		public function cacheDate($buffer){
			if($this->isMobile() && class_exists("WpFcMobileCache") && isset($this->options->wpFastestCacheMobile) && isset($this->options->wpFastestCacheMobileTheme)){
				$comment = "<!-- Mobile: WP Fastest Cache file was created in ".$this->creationTime()." seconds, on ".date("d-m-y G:i:s", current_time('timestamp'))." -->";
			}else{
				$comment = "<!-- WP Fastest Cache file was created in ".$this->creationTime()." seconds, on ".date("d-m-y G:i:s", current_time('timestamp'))." -->";
			}

			if(defined('WPFC_REMOVE_FOOTER_COMMENT') && WPFC_REMOVE_FOOTER_COMMENT){
				return $buffer;
			}else{
				return $buffer.$comment;
			}
		}

		public function creationTime(){
			return microtime(true) - $this->startTime;
		}

		public function isCommenter(){
			$commenter = wp_get_current_commenter();
			return isset($commenter["comment_author_email"]) && $commenter["comment_author_email"] ? true : false;
		}
		public function isPasswordProtected($buffer){
			if(preg_match("/action\=[\'\"].+postpass.*[\'\"]/", $buffer)){
				return true;
			}

			foreach($_COOKIE as $key => $value){
				if(preg_match("/wp\-postpass\_/", $key)){
					return true;
				}
			}

			return false;
		}

		public function create_name($list){
			$arr = is_array($list) ? $list : array(array("href" => $list));
			$name = "";
			
			foreach ($arr as $tag_key => $tag_value){
				$tmp = preg_replace("/(\.css|\.js)\?.*/", "$1", $tag_value["href"]); //to remove version number
				$name = $name.$tmp;
			}
			
			return base_convert(crc32($name), 20, 36);
		}

		public function createFolder($cachFilePath, $buffer, $extension = "html", $prefix = false){
			$create = false;
			$file_name = "index.";
			$update_db_statistic = true;
			
			if($buffer && strlen($buffer) > 100 && $extension == "html"){
				if(!preg_match("/^\<\!\-\-\sMobile\:\sWP\sFastest\sCache/i", $buffer)){
					if(!preg_match("/^\<\!\-\-\sWP\sFastest\sCache/i", $buffer)){
						$create = true;
					}
				}

				if($this->preload_user_agent){
					if(file_exists($cachFilePath."/"."index.".$extension)){
						$update_db_statistic = false;
						@unlink($cachFilePath."/"."index.".$extension);
					}
				}
			}

			if(($extension == "css" || $extension == "js") && $buffer && strlen($buffer) > 5){
				$create = true;
				$file_name = base_convert(substr(time(), -6), 20, 36).".";
				$buffer = trim($buffer);

				if($extension == "js"){
					if(substr($buffer, -1) != ";"){
						$buffer .= ";";
					}
				}
			}

			if($create){
				if (!is_user_logged_in() && !$this->isCommenter()){
					if(!is_dir($cachFilePath)){
						if(is_writable($this->getWpContentDir()) || ((is_dir($this->getWpContentDir()."/cache")) && (is_writable($this->getWpContentDir()."/cache")))){
							if (@mkdir($cachFilePath, 0755, true)){

								file_put_contents($cachFilePath."/".$file_name.$extension, $buffer);
								
								if(class_exists("WpFastestCacheStatics")){
									if($update_db_statistic && !preg_match("/After\sCache\sTimeout/i", $_SERVER['HTTP_USER_AGENT'])){
										if(preg_match("/wpfc\-mobile\-cache/", $cachFilePath)){
											$extension = "mobile";
										}
										
						   				$cache_statics = new WpFastestCacheStatics($extension, strlen($buffer));
						   				$cache_statics->update_db();
									}
				   				}

				   				if($extension == "html"){
				   					if(!file_exists(WPFC_WP_CONTENT_DIR."/cache/index.html")){
				   						@file_put_contents(WPFC_WP_CONTENT_DIR."/cache/index.html", "");
				   					}
				   				}else{
				   					if(!file_exists(WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/index.html")){
				   						@file_put_contents(WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/index.html", "");
				   					}
				   				}

							}else{
							}
						}else{

						}
					}else{
						if(file_exists($cachFilePath."/".$file_name.$extension)){

						}else{

							file_put_contents($cachFilePath."/".$file_name.$extension, $buffer);
							
							if(class_exists("WpFastestCacheStatics")){
								if($update_db_statistic && !preg_match("/After\sCache\sTimeout/i", $_SERVER['HTTP_USER_AGENT'])){
									if(preg_match("/wpfc\-mobile\-cache/", $cachFilePath)){
										$extension = "mobile";
									}

					   				$cache_statics = new WpFastestCacheStatics($extension, strlen($buffer));
					   				$cache_statics->update_db();
								}
			   				}
						}
					}
				}
			}elseif($extension == "html"){
				$this->err = "Buffer is empty so the cache cannot be created";
			}
		}

		public function is_amp($content){
			$request_uri = trim($_SERVER["REQUEST_URI"], "/");

			if(preg_match("/^amp/", $request_uri) || preg_match("/\/amp\//", $request_uri) || preg_match("/amp$/", $request_uri)){
				if(preg_match("/<html[^\>]+amp[^\>]*>/i", $content)){
					return true;
				}
			}

			return false;
		}

		public function isMobile(){
			foreach ($this->get_mobile_browsers() as $value) {
				if(preg_match("/".$value."/i", $_SERVER['HTTP_USER_AGENT'])){
					return true;
				}
			}

			foreach ($this->get_operating_systems() as $key => $value) {
				if(preg_match("/".$value."/i", $_SERVER['HTTP_USER_AGENT'])){
					return true;
				}
			}
		}

		public function isWpLogin($buffer){
			// if(preg_match("/<form[^\>]+loginform[^\>]+>((?:(?!<\/form).)+)user_login((?:(?!<\/form).)+)user_pass((?:(?!<\/form).)+)<\/form>/si", $buffer)){
			// 	return true;
			// }
			if($GLOBALS["pagenow"] == "wp-login.php"){
				return true;
			}

			return false;
		}

		public function hasContactForm7WithCaptcha($buffer){
			if(is_single() || is_page()){
				if(preg_match("/<input[^\>]+_wpcf7_captcha[^\>]+>/i", $buffer)){
					return true;
				}
			}
			
			return false;
		}

		public function is_wptouch_smartphone(){
			// https://plugins.svn.wordpress.org/wptouch/tags/4.0.4/core/mobile-user-agents.php
			// wptouch: ipad is accepted as a desktop so no need to create cache if user agent is ipad 
			// https://wordpress.org/support/topic/plugin-wptouch-wptouch-wont-display-mobile-version-on-ipad?replies=12
			if(preg_match("/ipad/i", $_SERVER['HTTP_USER_AGENT'])){
				return false;
			}

			$wptouch_smartphone_list = array();

			$wptouch_smartphone_list[] = array( 'iPhone' ); // iPhone
			$wptouch_smartphone_list[] = array( 'Android', 'Mobile' ); // Android devices
			$wptouch_smartphone_list[] = array( 'BB', 'Mobile Safari' ); // BB10 devices
			$wptouch_smartphone_list[] = array( 'BlackBerry', 'Mobile Safari' ); // BB 6, 7 devices
			$wptouch_smartphone_list[] = array( 'Firefox', 'Mobile' ); // Firefox OS devices
			$wptouch_smartphone_list[] = array( 'IEMobile/11', 'Touch' ); // Windows IE 11 touch devices
			$wptouch_smartphone_list[] = array( 'IEMobile/10', 'Touch' ); // Windows IE 10 touch devices
			$wptouch_smartphone_list[] = array( 'IEMobile/9.0' ); // Windows Phone OS 9
			$wptouch_smartphone_list[] = array( 'IEMobile/8.0' ); // Windows Phone OS 8
			$wptouch_smartphone_list[] = array( 'IEMobile/7.0' ); // Windows Phone OS 7
			$wptouch_smartphone_list[] = array( 'OPiOS', 'Mobile' ); // Opera Mini iOS
			$wptouch_smartphone_list[] = array( 'Coast', 'Mobile' ); // Opera Coast iOS

			foreach ($wptouch_smartphone_list as $key => $value) {
				if(isset($value[0]) && isset($value[1])){
					if(preg_match("/".preg_quote($value[0], "/")."/i", $_SERVER['HTTP_USER_AGENT'])){
						if(preg_match("/".preg_quote($value[1], "/")."/i", $_SERVER['HTTP_USER_AGENT'])){
							return true;
						}
					}
				}else if(isset($value[0])){
					if(preg_match("/".preg_quote($value[0], "/")."/i", $_SERVER['HTTP_USER_AGENT'])){
						return true;
					}
				}
			}

			return false;
		}

		public function is_anymobilethemeswitcher_mobile(){
			// https://plugins.svn.wordpress.org/any-mobile-theme-switcher/tags/1.9/any-mobile-theme-switcher.php
			$user_agent = $_SERVER['HTTP_USER_AGENT'];

			switch(true){
				case (preg_match('/ipad/i',$user_agent));
					return true;     
				break;

				case (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent));
					return true;     
				break;

				case (preg_match('/android/i',$user_agent) && preg_match('/mobile/i',$user_agent));
					return true;
				break;

				case (preg_match('/opera mini/i',$user_agent));
					return true;     
				break;

				case (preg_match('/blackberry/i',$user_agent));
					return true;     
				break;

				case (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent));
					return true;     
				break;

				case (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent));
					return true;     
				break;

				case (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent)); 
					return true;
				break;
			}

			return false;
		}
	}
?>