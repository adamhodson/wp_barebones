<?php
	class CdnWPFC{
		public static function cloudflare_clear_cache($email = false, $key = false, $zoneid = false){
			if(!$email && !$key && !$zoneid){
				if($cdn_values = get_option("WpFastestCacheCDN")){
					$std_obj = json_decode($cdn_values);

					foreach ($std_obj as $key => $value) {
						if($value->id == "cloudflare"){
							$email = $value->cdnurl;
							$key = $value->originurl;
							break;
						}
					}

					if($email && $key){
						$zone = self::cloudflare_get_zone_id($email, $key, false);

						if($zone["success"]){
							$zoneid = $zone["zoneid"];
						}
					}
				}
			}
			
			if($email && $key && $zoneid){
				$header = array("method" => "DELETE",
								'headers' => array(
												"X-Auth-Email" => $email,
												"X-Auth-Key" => $key,
												"Content-Type" => "application/json"
												),
								"body" => '{"purge_everything":true}'
								);

				$response = wp_remote_request('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/purge_cache', $header);
			}
		}

		public static function cloudflare_disable_rocket_loader($email = false, $key = false, $zoneid = false){
			if($email && $key && $zoneid){
				$header = array("method" => "PATCH",
								'headers' => array(
												"X-Auth-Email" => $email,
												"X-Auth-Key" => $key,
												"Content-Type" => "application/json"
												),
								'body' => '{"value":"off"}'
								);

				$response = wp_remote_request('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/settings/rocket_loader', $header);

				if(!$response || is_wp_error($response)){
					return array("success" => false, "error_message" => "Unable to disable rocket loader option");
				}else{
					$body = json_decode(wp_remote_retrieve_body($response));

					if($body->success){
						return array("success" => true);
					}else if(isset($body->errors) && isset($body->errors[0])){
						return array("success" => false, "error_message" => $body->errors[0]->message);
					}else{
						return array("success" => false, "error_message" => "Unknown error: 101");
					}
				}

				return array("success" => false, "error_message" => "Unknown error");
			}
		}


		public static function cloudflare_set_browser_caching($email = false, $key = false, $zoneid = false){
			if($email && $key && $zoneid){
				$header = array("method" => "PATCH",
								'headers' => array(
												"X-Auth-Email" => $email,
												"X-Auth-Key" => $key,
												"Content-Type" => "application/json"
												),
								'body' => '{"value":16070400}'
								);

				$response = wp_remote_request('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/settings/browser_cache_ttl', $header);

				if(!$response || is_wp_error($response)){
					return array("success" => false, "error_message" => "Unable to disable rocket loader option");
				}else{
					$body = json_decode(wp_remote_retrieve_body($response));

					if($body->success){
						return array("success" => true);
					}else if(isset($body->errors) && isset($body->errors[0])){
						return array("success" => false, "error_message" => $body->errors[0]->message);
					}else{
						return array("success" => false, "error_message" => "Unknown error: 101");
					}
				}

				return array("success" => false, "error_message" => "Unknown error");
			}
		}

		public static function cloudflare_disable_minify($email = false, $key = false, $zoneid = false){
			if($email && $key && $zoneid){
				$header = array("method" => "PATCH",
								'headers' => array(
												"X-Auth-Email" => $email,
												"X-Auth-Key" => $key,
												"Content-Type" => "application/json"
												),
								'body' => '{"value":{"css":"off","html":"off","js":"off"}}'
								);

				$response = wp_remote_request('https://api.cloudflare.com/client/v4/zones/'.$zoneid.'/settings/minify', $header);

				if(!$response || is_wp_error($response)){
					return array("success" => false, "error_message" => "Unable to disable minify options");
				}else{
					$body = json_decode(wp_remote_retrieve_body($response));

					if($body->success){
						return array("success" => true);
					}else if(isset($body->errors) && isset($body->errors[0])){
						return array("success" => false, "error_message" => $body->errors[0]->message);
					}else{
						return array("success" => false, "error_message" => "Unknown error: 101");
					}
				}

				return array("success" => false, "error_message" => "Unknown error");
			}else{
				wp_die("bad request");
			}
		}

		public static function cloudflare_get_zone_id($email = false, $key = false){
			$hostname = preg_replace("/^(https?\:\/\/)?(www\d*\.)?/", "", $_SERVER["HTTP_HOST"]);

			$header = array("method" => "GET",
							'headers' => array(
											"X-Auth-Email" => $email,
											"X-Auth-Key" => $key,
											"Content-Type" => "application/json"
											),
							);
			
			/*
			status=active has been removed because status may be "pending"
			*/
			$response = wp_remote_request('https://api.cloudflare.com/client/v4/zones/?page=1&per_page=1000', $header);

			if(!$response || is_wp_error($response)){
				$res = array("success" => false, "error_message" => $response->get_error_message());
			}else{
				$zone = json_decode(wp_remote_retrieve_body($response));

				if(isset($zone->errors) && isset($zone->errors[0])){
					$res = array("success" => false, "error_message" => $zone->errors[0]->message);
				}else{
					if(isset($zone->result) && isset($zone->result[0])){
						foreach ($zone->result as $zone_key => $zone_value) {
							if(preg_match("/".$zone_value->name."/", $hostname)){
								$res = array("success" => true, "zoneid" => $zone_value->id);
							}
						}

						if(!$res["success"]){
							$res = array("success" => false, "error_message" => "No zone name ".$hostname);
						}
					}else{
						$res = array("success" => false, "error_message" => "There is no zone");
					}
				}
			}

			return $res;
		}


		public static function cloudflare_change_settings(){
			//admin OR author OR editor
			if(current_user_can('manage_options') || current_user_can('delete_published_posts') || current_user_can('edit_published_posts')){
				if(isset($_GET["url"]) && isset($_GET["origin_url"])){
					$email = $_GET["url"];
					$key = $_GET["origin_url"];
				}

				$zone = CdnWPFC::cloudflare_get_zone_id($email, $key);

				if($zone["success"]){

					$minify = CdnWPFC::cloudflare_disable_minify($email, $key, $zone["zoneid"]);
					$rocket_loader = CdnWPFC::cloudflare_disable_rocket_loader($email, $key, $zone["zoneid"]);
					$purge_cache = CdnWPFC::cloudflare_clear_cache($email, $key, $zone["zoneid"]);
					$browser_caching = CdnWPFC::cloudflare_set_browser_caching($email, $key, $zone["zoneid"]);

					if($minify["success"]){
						if($rocket_loader["success"]){
							if($browser_caching["success"]){
								$res = array("success" => true);
							}else{
								$res = array("success" => false, "error_message" => $browser_caching["error_message"]);
							}
						}else{
							$res = array("success" => false, "error_message" => $rocket_loader["error_message"]);
						}
					}else{
						$res = array("success" => false, "error_message" => $minify["error_message"]);
					}
				}else{
					$res = $zone;
				}

				wp_send_json($res);
			}else{
				wp_die("Must be admin");
			}
		}

		public static function check_url(){
			if(current_user_can('manage_options')){
				if(isset($_GET["type"]) && $_GET["type"] == "cloudflare"){
					CdnWPFC::cloudflare_change_settings();
				}

				if(preg_match("/wp\.com/", $_GET["url"]) || $_GET["url"] == "random"){
					wp_send_json(array("success" => true));
				}

				$host = str_replace("www.", "", $_SERVER["HTTP_HOST"]);
				$_GET["url"] = esc_url_raw($_GET["url"]);				
				
				if(!preg_match("/^http/", $_GET["url"])){
					$_GET["url"] = "http://".$_GET["url"];
				}
				
				$response = wp_remote_get($_GET["url"], array('timeout' => 20 ) );

				$header = wp_remote_retrieve_headers($response);

				if ( !$response || is_wp_error( $response ) ) {
					$res = array("success" => false, "error_message" => $response->get_error_message());
					
					if($response->get_error_code() == "http_request_failed"){
						if($response->get_error_message() == "Failure when receiving data from the peer"){
							$res = array("success" => true);
						}else if(preg_match("/cURL\serror\s6/i", $response->get_error_message())){
							//cURL error 6: Couldn't resolve host
							if(preg_match("/".preg_quote($host, "/")."/i", $_GET["url"])){
								$res = array("success" => true);
							}
						}
					}
				}else{
					$response_code = wp_remote_retrieve_response_code( $response );
					if($response_code == 200){
						$res = array("success" => true);
					}else{
						if(method_exists($response, "get_error_message")){
							$res = array("success" => false, "error_message" => $response->get_error_message());
						}else{
							$res = array("success" => false, "error_message" => wp_remote_retrieve_response_message($response));
						}

						if(isset($header["server"]) && preg_match("/squid/i", $header["server"])){
							$res = array("success" => true);
						}

						if(($response_code == 401) && (preg_match("/res\.cloudinary\.com/i", $_GET["url"]))){
							$res = array("success" => true);
						}

						if(($response_code == 403) && (preg_match("/stackpathdns\.com/i", $_GET["url"]))){
							$res = array("success" => true);
						}
					}
				}
				
				wp_send_json($res);
			}else{
				wp_die("Must be admin");
			}
		}
		
		public static function cdn_options(){
			if(current_user_can('manage_options')){
				$cdn_values = get_option("WpFastestCacheCDN");
				if($cdn_values){
					echo $cdn_values;
				}else{
					echo json_encode(array("success" => false)); 
				}
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public static function remove_cdn_integration(){
			if(current_user_can('manage_options')){
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

	    				if($cdn_value->id == $_POST["id"]){
	    					unset($cdn_values_arr[$cdn_key]);
	    				}
    				}

    				$cdn_values_arr = array_values($cdn_values_arr);
    			}

    			if(count($cdn_values_arr) > 0){
    				update_option("WpFastestCacheCDN", json_encode($cdn_values_arr));
    			}else{
					delete_option("WpFastestCacheCDN");
    			}

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public static function cdn_template(){
			if(current_user_can('manage_options')){
				if($_POST["id"] == "maxcdn"){
					$path = WPFC_MAIN_PATH."templates/cdn/maxcdn.php";
				}else if($_POST["id"] == "other"){
					$path = WPFC_MAIN_PATH."templates/cdn/other.php";
				}else if($_POST["id"] == "photon"){
					$path = WPFC_MAIN_PATH."templates/cdn/photon.php";
				}else if($_POST["id"] == "cloudflare"){
					$path = WPFC_MAIN_PATH."templates/cdn/cloudflare.php";
				}else{
					die("Wrong cdn");
				}


				ob_start();
				include_once($path);
				$content = ob_get_contents();
				ob_end_clean();

				$res = array("success" => false, "content" => "");

				if($data = @file_get_contents($path)){
					$res["success"] = true;
					$res["content"] = $content;
				}

				echo json_encode($res);
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public static function save_cdn_integration(){
			if(current_user_can('manage_options')){
				if(isset($_POST) && isset($_POST["values"])){
					foreach ($_POST["values"] as $val_key => &$val_value) {
						$val_value = sanitize_text_field($val_value);
					}
				}
				
				if($data = get_option("WpFastestCacheCDN")){
					$cdn_exist = false;
					$arr = json_decode($data);

					if(is_array($arr)){
						foreach ($arr as $cdn_key => &$cdn_value) {
							if($cdn_value->id == $_POST["values"]["id"]){
								$cdn_value = $_POST["values"];
								$cdn_exist = true;
							}
						}

						if(!$cdn_exist){
							array_push($arr, $_POST["values"]);	
						}

						update_option("WpFastestCacheCDN", json_encode($arr));
					}else{
						$tmp_arr = array();
						
						if($arr->id == $_POST["values"]["id"]){
							array_push($tmp_arr, $_POST["values"]);
						}else{
							array_push($tmp_arr, $arr);
							array_push($tmp_arr, $_POST["values"]);
						}

						update_option("WpFastestCacheCDN", json_encode($tmp_arr));
					}
				}else{
					$arr = array();
					array_push($arr, $_POST["values"]);

					add_option("WpFastestCacheCDN", json_encode($arr), null, "yes");
				}
				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

	}
?>