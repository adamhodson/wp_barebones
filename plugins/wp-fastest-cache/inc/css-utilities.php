<?php
	class CssUtilities{
		private $html = "";
		private $tags = array();
		private $except = "";
		private $wpfc;

		public function __construct($wpfc, $html){
			$this->wpfc = $wpfc;
			$this->html = $html;
			$this->set_except_tags();
			$this->set_tags();
			$this->tags_reorder();
		}

		public function check_exclude($css_url = false){
			if($css_url){
				foreach((array)$this->wpfc->exclude_rules as $key => $value){

					if(isset($value->prefix) && $value->prefix && $value->type == "css"){
						if($value->prefix == "contain"){
							$preg_match_rule = preg_quote($value->content, "/");
						}

						if(preg_match("/".$preg_match_rule."/i", $css_url)){
							return true;
						}
					}
				}
			}
		}

		public function combineCss(){
			$all = array();
			$group = array();

			foreach ($this->tags as $key => $value) {
				if(preg_match("/<link/i", $value["text"])){

					if($this->except){
						if(strpos($this->except, $value["text"]) !== false){
							array_push($all, $group);
							$group = array();
							continue;
						}
					}

					if(!$this->checkInternal($value["text"])){
						array_push($all, $group);
						$group = array();
						continue;
					}

					if($this->check_exclude($value["text"])){
						array_push($all, $group);
						$group = array();
						continue;
					}

					if(count($group) > 0){
						if($group[0]["media"] == $value["media"]){
							array_push($group, $value);
						}else{
							array_push($all, $group);
							$group = array();
							array_push($group, $value);
						}
					}else{
						array_push($group, $value);
					}

					if($value === end($this->tags)){
						array_push($all, $group);
					}


				}

				if(preg_match("/<style/i", $value["text"])){
					if(count($group) > 0){
						array_push($all, $group);
						$group = array();
					}
				}
			}

			if(count($all) > 0){
				$all = array_reverse($all);

				foreach ($all as $group_key => $group_value) {
					if(count($group_value) > 0){

						$combined_css = "";
						$combined_name = $this->wpfc->create_name($group_value);
						$combined_link = "";

						$cachFilePath = WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".$combined_name;
						$cssLink = str_replace(array("http:", "https:"), "", WPFC_WP_CONTENT_URL)."/cache/wpfc-minified/".$combined_name;

						if(is_dir($cachFilePath)){
							if($cssFiles = @scandir($cachFilePath, 1)){
								$combined_link = '<link rel="stylesheet" type="text/css" href="'.$cssLink."/".$cssFiles[0].'" media="'.$group_value[0]["media"].'"/>';

								if($css_content = $this->wpfc->read_file($cssLink."/".$cssFiles[0])){
									$combined_link = $this->to_inline($combined_link, $css_content);
								}
							}
						}else{
							$combined_css = $this->create_content(array_reverse($group_value));
							$combined_css = $this->fix_charset($combined_css);

							if($combined_css){
								
								if($this->wpfc->cdn){
									$combined_css = preg_replace_callback("/(url)\(([^\)]+)\)/i", array($this->wpfc, 'cdn_replace_urls'), $combined_css);
								}


								$this->wpfc->createFolder($cachFilePath, $combined_css, "css");
								
								if(is_dir($cachFilePath)){
									if($cssFiles = @scandir($cachFilePath, 1)){
										$combined_link = '<link rel="stylesheet" type="text/css" href="'.$cssLink."/".$cssFiles[0].'" media="'.$group_value[0]["media"].'"/>';

										$combined_link = $this->to_inline($combined_link, $combined_css);
									}
								}
							}
						}

						if($combined_link){
							foreach (array_reverse($group_value) as $tag_key => $tag_value) {
								$text = substr($this->html, $tag_value["start"], ($tag_value["end"]-$tag_value["start"] + 1));

								if($tag_key > 0){
									$this->html = substr_replace($this->html, "<!-- ".$text." -->", $tag_value["start"], ($tag_value["end"] - $tag_value["start"] + 1));
								}else{
									$this->html = substr_replace($this->html, "<!-- ".$text." -->"."\n".$combined_link, $tag_value["start"], ($tag_value["end"] - $tag_value["start"] + 1));

								}
							}
						}
					}
				}
			}

			return $this->html;
		}

		public function create_content($group_value){
			$combined_css = "";
			foreach ($group_value as $tag_key => $tag_value) {
				$minifiedCss = $this->minify($tag_value["href"]);

				if($minifiedCss){
					$combined_css = $minifiedCss["cssContent"].$combined_css;
				}else{
					return false;
				}
			}

			return $combined_css;
		}

		public function minifyCss(){
			$data = $this->html;

			if(count($this->tags) > 0){
				foreach (array_reverse($this->tags) as $key => $value) {
					$text = substr($data, $value["start"], ($value["end"]-$value["start"] + 1));

					if(preg_match("/<link/i", $text)){
						if($href = $this->checkInternal($text)){
							if($this->check_exclude($href)){
								continue;
							}

							$minifiedCss = $this->minify($href);

							if($minifiedCss){
								$prefixLink = str_replace(array("http:", "https:"), "", $minifiedCss["url"]);
								$text = preg_replace("/href\=[\"\'][^\"\']+[\"\']/", "href='".$prefixLink."'", $text);

								$text = $this->to_inline($text, $minifiedCss["cssContent"]);

								$this->html = substr_replace($this->html, $text, $value["start"], ($value["end"] - $value["start"] + 1));

							}

						}
					}
				}
			}

			return $this->html;
		}

		public function to_inline($link, $css_content){
			if(!isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheRenderBlocking)){
				return $link;
			}

			if(!preg_match("/\smedia\=[\'\"]all[\'\"]/i", $link)){
				return $link;
			}

			if(isset($css_content["11000"])){
				return $link;
			}

			$link = "<style>".$css_content."</style>";

			return $link;
		}

		public function tags_reorder(){
		    $sorter = array();
		    $ret = array();

		    foreach ($this->tags as $ii => $va) {
		        $sorter[$ii] = $va['start'];
		    }

		    asort($sorter);

		    foreach ($sorter as $ii => $va) {
		        $ret[$ii] = $this->tags[$ii];
		    }

		    $this->tags = $ret;
		}

		public function set_except_tags(){
			$comment_tags = $this->find_tags("<!--", "-->");

			foreach ($comment_tags as $key => $value) {
				$this->except = $value["text"].$this->except;
			}

			// to execute if html contains <noscript> tag
			if(preg_match("/<noscript/i", $this->html)){
				$noscript_tags = $this->find_tags("<noscript", "</noscript>");

				foreach ($noscript_tags as $key => $value) {
					$this->except = $value["text"].$this->except;

					if(isset($GLOBALS["wp_fastest_cache_options"]->wpFastestCacheLazyLoad)){
						// to set noscript for lazy load
						// <noscript><img src="http://google.com/image.jpg"></noscript>
						$GLOBALS["wp_fastest_cache"]->noscript = $value["text"].$GLOBALS["wp_fastest_cache"]->noscript;
					}
				}
			}

			// $("head").append( "<link rel='stylesheet' id='ms-fonts'  href='//fonts.googleapis.com/css?family=Exo+2:regular' type='text/css' media='all' />" );
			$script_tags = $this->find_tags("<script", "</script>");

			foreach ($script_tags as $key => $value) {
				$link_tags = $this->find_tags("<link", ">", $value["text"]);

				if(count($link_tags) > 0){
					$this->except = $value["text"].$this->except;
				}
			}
		}

		public function set_tags(){
			$style_tags = $this->find_tags("<style", "</style>");
			$this->tags = array_merge($this->tags, $style_tags);
			
			$link_tags = $this->find_tags("<link", ">");

			foreach ($link_tags as $key => $value) {
				//<link rel='stylesheet' id='avada-dynamic-css-css'  href='/wp-content/uploads/avada-styles/avada-9.css?timestamp=1485306359&#038;ver=4.7.2' type='text/css' media='all' />
				if(preg_match("/avada-dynamic-css-css/", $value["text"])){
					continue;
				}

				preg_match("/media\=[\'\"]([^\'\"]+)[\'\"]/", $value["text"], $media);
				preg_match("/href\=[\'\"]([^\'\"]+)[\'\"]/", $value["text"], $href);

				$media[1] = (isset($media[1]) && $media[1]) ? trim($media[1]) : "";
				$value["media"] = (isset($media[1]) && $media[1]) ? $media[1] : "all";

				if(isset($href[1])){
					$href[1] = trim($href[1]);
					$value["href"] = (isset($href[1]) && $href[1]) ? $href[1] : "";

					if(preg_match("/href\s*\=/i", $value["text"])){
						if(preg_match("/rel\s*\=\s*[\'\"]\s*stylesheet\s*[\'\"]/i", $value["text"])){
							array_push($this->tags, $value);
						}
					}
				}
			}
		}

		public function find_tags($start_string, $end_string, $source = false){
			if($source){
				$data = $source;
			}else{
				$data = $this->html;
			}

			$list = array();
			$start_index = false;
			$end_index = false;

			for($i = 0; $i < strlen( $data ); $i++) {
			    if(substr($data, $i, strlen($start_string)) == $start_string){
			    	$start_index = $i;
				}

				if($start_index && $i > $start_index){
					if(substr($data, $i, strlen($end_string)) == $end_string){
						$end_index = $i + strlen($end_string)-1;
						$text = substr($data, $start_index, ($end_index-$start_index + 1));
						

						array_push($list, array("start" => $start_index, "end" => $end_index, "text" => $text));


						$start_index = false;
						$end_index = false;
					}
				}
			}

			return $list;
		}

		public function minify($url){
			$this->url = $url;
			$md5 = $this->wpfc->create_name($url);

			$cachFilePath = WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".$md5;
			$cssLink = WPFC_WP_CONTENT_URL."/cache/wpfc-minified/".$md5;

			if(is_dir($cachFilePath)){
				if($cssFiles = @scandir($cachFilePath, 1)){
					if($cssContent = $this->file_get_contents_curl($cssLink."/".$cssFiles[0])){
						return array("cachFilePath" => $cachFilePath, "cssContent" => $cssContent, "url" => $cssLink."/".$cssFiles[0], "realUrl" => $url);
					}else{
						return false;
					}
				}
			}else{
				if($cssContent = $this->file_get_contents_curl($url, "?v=".time())){

					$original_content_length = strlen($cssContent);

					if(isset($this->wpfc->options->wpFastestCacheMinifyCss) && $this->wpfc->options->wpFastestCacheMinifyCss){
						$cssContent = $this->_process($cssContent);
					}

					$cssContent = $this->fixPathsInCssContent($cssContent, $url);

					if(isset($this->wpfc->options->wpFastestCacheMinifyCssPowerFul) && $this->wpfc->options->wpFastestCacheMinifyCssPowerFul){
						if(class_exists("WpFastestCachePowerfulHtml")){
							$powerful_html = new WpFastestCachePowerfulHtml();
							$cssContent = $powerful_html->minify_css($cssContent);
						}
					}


					$cssContent = str_replace("\xEF\xBB\xBF", '', $cssContent);

					// If the content is empty, the file is not created. This breaks "combine css" feature 
					if(strlen($cssContent) == 0 && $original_content_length > 0){
						return array("cssContent" => "", "url" => $url);
					}

					if(!is_dir($cachFilePath)){
						if($this->wpfc->cdn){
							$cssContent = preg_replace_callback("/(url)\(([^\)]+)\)/i", array($this->wpfc, 'cdn_replace_urls'), $cssContent);
						}

						$this->wpfc->createFolder($cachFilePath, $cssContent, "css");
					}

					if($cssFiles = @scandir($cachFilePath, 1)){
						return array("cachFilePath" => $cachFilePath, "cssContent" => $cssContent, "url" => $cssLink."/".$cssFiles[0], "realUrl" => $url);
					}
				}
			}
			return false;
		}

		public function fixPathsInCssContent($css, $url){
			$this->url_for_fix = $url;

			$css = preg_replace("/@import\s+[\"\']([^\;\"\'\)]+)[\"\'];/", "@import url($1);", $css);
			$css = preg_replace_callback("/url\(([^\)\n]*)\)/", array($this, 'newImgPath'), $css);
			$css = preg_replace_callback('/@import\s+url\(([^\)]+)\);/i', array($this, 'fix_import_rules'), $css);
			$css = $this->fix_charset($css);

			return $css; 
		}


		public function newImgPath($matches){
			$matches[1] = trim($matches[1]);
			
			if(preg_match("/data\:font\/opentype/i", $matches[1])){
				$matches[1] = $matches[1];
			}else if(preg_match("/data\:image\/svg\+xml/i", $matches[1])){
				$matches[1] = $matches[1];
			}else{
				$matches[1] = str_replace(array("\"","'"), "", $matches[1]);
				$matches[1] = trim($matches[1]);
				
				if(!$matches[1]){
					$matches[1] = "";
				}else if(preg_match("/^(\/\/|http|\/\/fonts|data:image|data:application)/", $matches[1])){
					if(preg_match("/fonts\.googleapis\.com/", $matches[1])){ // for safari browser
						$matches[1] = '"'.$matches[1].'"';
					}else{
						$matches[1] = $matches[1];
					}
				}else if(preg_match("/^\//", $matches[1])){
					$homeUrl = str_replace(array("http:", "https:"), "", home_url());
					$matches[1] = $homeUrl.$matches[1];
				}else if(preg_match("/^\.\/.+/i", $matches[1])){
					//$matches[1] = str_replace("./", get_template_directory_uri()."/", $matches[1]);
					$matches[1] = str_replace("./", dirname($this->url_for_fix)."/", $matches[1]);
				}else if(preg_match("/^(?P<up>(\.\.\/)+)(?P<name>.+)/", $matches[1], $out)){
					$count = strlen($out["up"])/3;
					$url = dirname($this->url);
					for($i = 1; $i <= $count; $i++){
						$url = substr($url, 0, strrpos($url, "/"));
					}
					$url = str_replace(array("http:", "https:"), "", $url);
					$matches[1] = $url."/".$out["name"];
				}else{
					$url = str_replace(array("http:", "https:"), "", dirname($this->url));
					$matches[1] = $url."/".$matches[1];
				}
			}

			return "url(".$matches[1].")";
		}

		public function fix_charset($css){
			preg_match_all('/@charset[^\;]+\;/i', $css, $charsets);
			if(count($charsets[0]) > 0){
				$css = preg_replace('/@charset[^\;]+\;/i', "", $css);
				foreach($charsets[0] as $charset){
					$css = $charset."\n".$css;
				}
			}
			return $css;
		}

		public function fix_import_rules($matches){
			if($this->is_internal_css($matches[1])){
				if($cssContent = $this->file_get_contents_curl($matches[1], "?v=".time())){
					$tmp_url = $this->url;
					$this->url = $matches[1];
					$cssContent = $this->fixPathsInCssContent($cssContent, $matches[1]);
					$this->url = $tmp_url;
					return $cssContent;
				}
			}

			return $matches[0];
		}

		protected $_inHack = false;
	 
	    protected function _process($css){
	        $css = preg_replace("/^\s+/m", "", ((string) $css));
	        $css = str_replace("\r", "", $css);
	        
	        $css = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@'
	            ,array($this, '_commentCB'), $css);

	        //to remove empty chars from url()
			$css = preg_replace("/url\((\s+)([^\)]+)(\s+)\)/", "url($2)", $css);

	        return trim($css);
	    }
	    
	    protected function _commentCB($m){
	        $hasSurroundingWs = (trim($m[0]) !== $m[1]);
	        $m = $m[1]; 
	        // $m is the comment content w/o the surrounding tokens, 
	        // but the return value will replace the entire comment.
	        if ($m === 'keep') {
	            return '/**/';
	        }
	        if ($m === '" "') {
	            // component of http://tantek.com/CSS/Examples/midpass.html
	            return '/*" "*/';
	        }
	        if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $m)) {
	            // component of http://tantek.com/CSS/Examples/midpass.html
	            return '/*";}}/* */';
	        }
	        if ($this->_inHack) {
	            // inversion: feeding only to one browser
	            if (preg_match('@
	                    ^/               # comment started like /*/
	                    \\s*
	                    (\\S[\\s\\S]+?)  # has at least some non-ws content
	                    \\s*
	                    /\\*             # ends like /*/ or /**/
	                @x', $m, $n)) {
	                // end hack mode after this comment, but preserve the hack and comment content
	                $this->_inHack = false;
	                return "/*/{$n[1]}/**/";
	            }
	        }
	        if (substr($m, -1) === '\\') { // comment ends like \*/
	            // begin hack mode and preserve hack
	            $this->_inHack = true;
	            return '/*\\*/';
	        }
	        if ($m !== '' && $m[0] === '/') { // comment looks like /*/ foo */
	            // begin hack mode and preserve hack
	            $this->_inHack = true;
	            return '/*/*/';
	        }
	        if ($this->_inHack) {
	            // a regular comment ends hack mode but should be preserved
	            $this->_inHack = false;
	            return '/**/';
	        }
	        // Issue 107: if there's any surrounding whitespace, it may be important, so 
	        // replace the comment with a single space
	        return $hasSurroundingWs // remove all other comments
	            ? ' '
	            : '';
	    }

	    public function checkInternal($link){
			$httpHost = str_replace("www.", "", $_SERVER["HTTP_HOST"]);
			
			if(preg_match("/href=[\"\'](.*?)[\"\']/", $link, $href)){

				if(preg_match("/^\/[^\/]/", $href[1])){
					return $href[1];
				}

				if(@strpos($href[1], $httpHost)){
					return $href[1];
				}

				// if(preg_match("/fonts\.googleapis\.com/i", $href[1])){
				// 	//http://fonts.googleapis.com/css?family=Raleway%3A400%2C600
				// 	if(preg_match("/Raleway/i", $href[1])){
				// 		return false;
				// 	}

				// 	return $href[1];
				// }
			}
			return false;
		}

		public function is_internal_css($url){
			$http_host = trim($_SERVER["HTTP_HOST"], "www.");

			$url = trim($url);
			$url = trim($url, "'");
			$url = trim($url, '"');

			$url = str_replace(array("http://", "https://"), "", $url);

			$url = trim($url, '//');
			$url = trim($url, 'www.');

			if($url){
				if(preg_match("/".$http_host."/i", $url)){
					return true;
				}

				// if(preg_match("/fonts\.googleapis\.com/i", $url)){
				// 	//http://fonts.googleapis.com/css?family=Raleway%3A400%2C600
				// 	if(preg_match("/Raleway/i", $url)){
				// 		return false;
				// 	}

				// 	return true;
				// }
			}

			return false;
		}

	    public function file_get_contents_curl($url, $version = ""){
	    	if($data = $this->wpfc->read_file($url)){
	    		return $data;
	    	}

			$url = str_replace('&#038;', '&', $url);
	    	
	    	if(preg_match("/\.php\?/i", $url)){
	    		$version = "";
			}

			if(preg_match("/(fonts\.googleapis\.com|iire-social-icons)/i", $url)){
				$version = "";
				$url = str_replace(array("'",'"'), "", $url);
			}

	    	$url = $url.$version;

			if(preg_match("/^\/[^\/]/", $url)){
				$url = get_option("home").$url;
			}

			if(preg_match("/http\:\/\//i", home_url())){
				$url = preg_replace("/^\/\//", "http://", $url);
			}else if(preg_match("/https\:\/\//i", home_url())){
				$url = preg_replace("/^\/\//", "https://", $url);
			}

			//$response = wp_remote_get($url, array('timeout' => 10, 'headers' => array("cache-control" => array("no-store, no-cache, must-revalidate", "post-check=0, pre-check=0"))));
			$response = wp_remote_get($url, array('timeout' => 10, 'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.110 Safari/537.36'));
			
			if ( !$response || is_wp_error( $response ) ) {
				return false;
			}else{
				if(wp_remote_retrieve_response_code($response) == 200){
					$data = wp_remote_retrieve_body( $response );

					if(preg_match("/\<\!DOCTYPE/i", $data) || preg_match("/<\/\s*html\s*>/i", $data)){
						return false;
					}else if(!$data){
						return "/* empty */";
					}else{
						return $data;	
					}
				}else if(wp_remote_retrieve_response_code($response) == 404){
					if(preg_match("/\.css/", $url)){
						return "/*404*/";
					}else{
						return "<!-- 404 -->";
					}
				}
			}
		}
	}
?>