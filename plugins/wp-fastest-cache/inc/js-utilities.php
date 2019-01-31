<?php
	class JsUtilities{
		private $wpfc;
		private $html = "";
		private $jsLinks = array();
		private $jsLinksExcept = "";
		private $url = "";
		private $minify;

		public function __construct($wpfc, $html, $minify = false){
			//$this->html = preg_replace("/\s+/", " ", ((string) $html));
			$this->minify = $minify;
			$this->wpfc = $wpfc;
			$this->html = $html;

			$this->setJsLinksExcept();
			$this->setJsLinks();
		}

		public function check_exclude($js_url = false){
			if($js_url){
				foreach((array)$this->wpfc->exclude_rules as $key => $value){

					if(isset($value->prefix) && $value->prefix && $value->type == "js"){
						if($value->prefix == "contain"){
							$preg_match_rule = preg_quote($value->content, "/");
						}

						if(preg_match("/".$preg_match_rule."/i", $js_url)){
							return true;
						}
					}
				}
			}
		}

		public function combine_js(){
			if(count($this->jsLinks) > 0){
				$prev_content = "";
				foreach($this->jsLinks as $key => $value){
					$script_tag = substr($this->html, $value["start"], ($value["end"] - $value["start"] + 1));

					if(!preg_match("/<script[^>]+json[^>]+>.+/", $script_tag) && !preg_match("/<script[^>]+text\/template[^>]+>.+/", $script_tag)){
						if($href = $this->checkInternal($script_tag)){
							if(strpos($this->jsLinksExcept, $href) === false){
								if($this->check_exclude($href)){
									$this->mergeJs($prev_content, $this->jsLinks[$key - 1]);
									$prev_content = "";
									continue;
								}

								$minifiedJs = $this->minify($href);

								if($minifiedJs){
									if(!is_dir($minifiedJs["cachFilePath"])){
										$this->wpfc->createFolder($minifiedJs["cachFilePath"], $minifiedJs["jsContent"], "js");
									}

									if($jsFiles = @scandir($minifiedJs["cachFilePath"], 1)){

										$jsFiles[0] = preg_replace("/\.gz$/", "", $jsFiles[0]);

										if($jsContent = $this->file_get_contents_curl($minifiedJs["url"]."/".$jsFiles[0]."?v=".time())){

											if(preg_match("/^[\"\']use strict[\"\']/i", $jsContent)){
												$this->mergeJs($prev_content, $this->jsLinks[$key - 1]);
												$prev_content = "";
											}else{
												$prev_content = $jsContent."\n".$prev_content;

												$script_tag = "<!-- ".$script_tag." -->";

												if(($key + 1) == count($this->jsLinks)){
													$this->mergeJs($prev_content, $value, true);
													$prev_content = "";
												}else{
													$this->html = substr_replace($this->html, $script_tag, $value["start"], ($value["end"] - $value["start"] + 1));
												}
											}
										}
									}
								}else{
									$this->mergeJs($prev_content, $this->jsLinks[$key - 1]);
									$prev_content = "";
								}
							}else{
								if($key > 0 && $prev_content){
									$this->mergeJs($prev_content, $this->jsLinks[$key - 1]);
									$prev_content = "";
								}
							}
						}else{
							if($key > 0 && $prev_content){
								$this->mergeJs($prev_content, $this->jsLinks[$key - 1]);
								$prev_content = "";
							}
						}
					}else{
						if($key > 0 && $prev_content){
							$this->mergeJs($prev_content, $this->jsLinks[$key - 1]);
							$prev_content = "";
						}
					}
				}
			}

			return $this->html;
		}


		public function setJsLinks(){
			$data = $this->html;
			$script_list = array();
			$script_start_index = false;

			for($i = 0; $i < strlen( $data ); $i++) {
				if(isset($data[$i-6])){
				    if(substr($data, $i-6, 7) == "<script"){
				    	$script_start_index = $i-6;
					}
				}

				if(isset($data[$i-8])){
					if($script_start_index){
						if(substr($data, $i-8, 9) == "</script>"){
							array_push($script_list, array("start" => $script_start_index, "end" => $i));
							$script_start_index = false;
						}
					}
				}
			}
			if(count($script_list) > 0){
				$this->jsLinks = array_reverse($script_list);
			}

			// to update jsLinksExcept 
			foreach($this->jsLinks as $key => $value){
				$script_tag = substr($this->html, $value["start"], ($value["end"] - $value["start"] + 1));

				if(preg_match("/wp-spamshield\/js\/jscripts\.php/i", $script_tag)){
					$this->jsLinksExcept = $this->jsLinksExcept.$script_tag;
				}

				//amazonjs/components/js/jquery-tmpl/jquery.tmpl.min.js?ver=1.0.0pre
				if(preg_match("/jquery-tmpl\/jquery\.tmpl\.min\.js/i", $script_tag)){
					$this->jsLinksExcept = $this->jsLinksExcept.$script_tag;
				}
			}
		}

		public function setJsLinksExcept(){
			$comment_tags = $this->find_tags("<!--", "-->");
			$document_write = $this->find_tags("document.write(", ")");

			foreach ($comment_tags as $key => $value) {
				if(preg_match("/<script/i", $value["text"]) && preg_match("/<\/script/i", $value["text"])){
					$this->jsLinksExcept = $value["text"].$this->jsLinksExcept;
				}
			}

			foreach ($document_write as $key => $value) {
				$this->jsLinksExcept = $value["text"].$this->jsLinksExcept;
			}
		}

		public function minify($url){
			$this->url = $url;

			$md5 = $this->wpfc->create_name($url);

			$cachFilePath = WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".$md5;
			$jsLink = WPFC_WP_CONTENT_URL."/cache/wpfc-minified/".$md5;

			if(is_dir($cachFilePath)){
				return array("cachFilePath" => $cachFilePath, "jsContent" => "", "url" => $jsLink);
			}else{
				if($js = $this->file_get_contents_curl($url)){

					if($this->minify){
						if(class_exists("WpFastestCachePowerfulHtml")){
							$powerful_html = new WpFastestCachePowerfulHtml();
							$js = $powerful_html->minify_js($js);
						}else{
							$js = "\n// source --> ".$url." \n".$js;
						}
					}else{
						$js = "\n// source --> ".$url." \n".$js;
					}

					return array("cachFilePath" => $cachFilePath, "jsContent" => $js, "url" => $jsLink);
				}
			}
			return false;
		}

		public function checkInternal($link){
			$httpHost = str_replace("www.", "", $_SERVER["HTTP_HOST"]);

			if(preg_match("/^<script[^\>]+\>/i", $link, $script)){
				if(preg_match("/src=[\"\'](.*?)[\"\']/", $script[0], $src)){
					if(preg_match("/alexa\.com\/site\_stats/i", $src[1])){
						return false;
					}

					if(preg_match("/^\/[^\/]/", $src[1])){
						return $src[1];
					}

					if(preg_match("/".preg_quote($httpHost, "/")."/i", $src[1])){
						//<script src="https://server1.opentracker.net/?site=www.site.com"></script>
						if(preg_match("/[\?\=].*".preg_quote($httpHost, "/")."/i", $src[1])){
							return false;
						}

						return $src[1];
					}
				}
			}
			
			return false;
		}


		public function mergeJs($js_content, $value, $last = false){
			$name = md5($js_content);

			$name = base_convert(crc32($name), 20, 36);

			$cachFilePath = WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".$name;

			if(!is_dir($cachFilePath)){
				$this->wpfc->createFolder($cachFilePath, $js_content, "js");
			}

			if($jsFiles = @scandir($cachFilePath, 1)){

				$jsFiles[0] = preg_replace("/\.gz$/", "", $jsFiles[0]);
				
				$prefixLink = str_replace(array("http:", "https:"), "", WPFC_WP_CONTENT_URL);
				$newLink = "<script src='".$prefixLink."/cache/wpfc-minified/".$name."/".$jsFiles[0]."' type=\"text/javascript\"></script>";

				$script_tag = substr($this->html, $value["start"], ($value["end"] - $value["start"] + 1));
				
				if($last){
					$script_tag = $newLink."\n<!-- ".$script_tag." -->\n";
				}else{
					$script_tag = $newLink."\n".$script_tag;
				}

				$this->html = substr_replace($this->html, $script_tag, $value["start"], ($value["end"] - $value["start"] + 1));
			}
		}

		public function file_get_contents_curl($url) {
			if($data = $this->wpfc->read_file($url)){
	    		return $data;
	    	}

			if(!preg_match("/\.php$/", $url)){
				$url = $url."?v=".time();
			}

			if(preg_match("/^\/[^\/]/", $url)){
				$url = home_url().$url;
			}

			$url = preg_replace("/^\/\//", "http://", $url);

			$response = wp_remote_get($url, array('timeout' => 10 ) );

			if ( !$response || is_wp_error( $response ) ) {
				return false;
			}else{
				if(wp_remote_retrieve_response_code($response) == 200){
					$data = wp_remote_retrieve_body( $response );

					if(preg_match("/<\/\s*html\s*>\s*$/i", $data)){
						return false;
					}else{
						return $data;	
					}
				}
			}
		}

		public function find_tags($start_string, $end_string){
			$data = $this->html;

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
	}
?>