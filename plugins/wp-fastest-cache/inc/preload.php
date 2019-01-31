<?php
	class PreloadWPFC{
		public static function set_preload($slug){
			$preload_arr = array();

			if(!empty($_POST) && isset($_POST["wpFastestCachePreload"])){
				foreach ($_POST as $key => $value) {
					$key = esc_attr($key);
					$value = esc_attr($value);
					
					preg_match("/wpFastestCachePreload_(.+)/", $key, $type);

					if(!empty($type)){
						if($type[1] == "restart"){
							//to need to remove "restart" value
						}else if($type[1] == "number"){
							$preload_arr[$type[1]] = $value; 
						}else{
							$preload_arr[$type[1]] = 0; 
						}
					}
				}
			}

			if($data = get_option("WpFastestCachePreLoad")){
				$preload_std = json_decode($data);

				if(!empty($preload_arr)){
					foreach ($preload_arr as $key => &$value) {
						if(!empty($preload_std->$key)){
							if($key != "number"){
								$value = $preload_std->$key;
							}
						}
					}

					$preload_std = $preload_arr;
				}else{
					foreach ($preload_std as $key => &$value) {
						if($key != "number"){
							$value = 0;
						}
					}
				}

				update_option("WpFastestCachePreLoad", json_encode($preload_std));

				if(!wp_next_scheduled($slug."_Preload")){
					wp_schedule_event(time() + 5, 'everyfiveminute', $slug."_Preload");
				}
			}else{
				if(!empty($preload_arr)){
					add_option("WpFastestCachePreLoad", json_encode($preload_arr), null, "yes");

					if(!wp_next_scheduled($slug."_Preload")){
						wp_schedule_event(time() + 5, 'everyfiveminute', $slug."_Preload");
					}
				}else{
					//toDO
				}
			}
		}

		public static function create_preload_cache($options, $wpfc_remote_get){
			if($data = get_option("WpFastestCachePreLoad")){
				if(!isset($options->wpFastestCacheStatus)){
					die("Cache System must be enabled");
				}


				$count_posts = wp_count_posts("post");
				$count_pages = wp_count_posts('page');

				$pre_load = json_decode($data);

				if(defined("WPFC_PRELOAD_NUMBER") && WPFC_PRELOAD_NUMBER){
					$number = WPFC_PRELOAD_NUMBER;
				}else{
					$number = $pre_load->number;
				}

				
				$urls_limit = isset($options->wpFastestCachePreload_number) ? $options->wpFastestCachePreload_number : 4; // must be even
				$urls = array();

				if(isset($options->wpFastestCacheMobileTheme) && $options->wpFastestCacheMobileTheme){
					$mobile_theme = true;
					$number = $number/2;
				}else{
					$mobile_theme = false;
				}
				

				// HOME
				if(isset($pre_load->homepage) && $pre_load->homepage > -1){
					if($mobile_theme){
						array_push($urls, array("url" => get_option("home"), "user-agent" => "mobile"));
						$number--;
					}

					array_push($urls, array("url" => get_option("home"), "user-agent" => "desktop"));
					$number--;
					
					$pre_load->homepage = -1;
				}


				// CUSTOM POSTS
				if($number > 0 && isset($pre_load->customposttypes) && $pre_load->customposttypes > -1){
		    		global $wpdb;
					$post_types = get_post_types(array('public' => true), "names", "and"); 
					$where_query = "";

					foreach ($post_types as $post_type_key => $post_type_value) {
						if(!in_array($post_type_key, array("post", "page", "attachment"))){
							$where_query = $where_query.$wpdb->prefix."posts.post_type = '".$post_type_value."' OR ";
						}

					}

					if($where_query){
						$where_query = preg_replace("/(\s*OR\s*)$/", "", $where_query);
					}

		    		$recent_custom_posts = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS  ".$wpdb->prefix."posts.ID FROM ".$wpdb->prefix."posts  WHERE 1=1  AND (".$where_query.") AND ((".$wpdb->prefix."posts.post_status = 'publish'))  ORDER BY ".$wpdb->prefix."posts.ID DESC LIMIT ".$pre_load->customposttypes.", ".$number, ARRAY_A);

		    		if(count($recent_custom_posts) > 0){
		    			foreach ($recent_custom_posts as $key => $post) {
		    				if($mobile_theme){
		    					array_push($urls, array("url" => get_permalink($post["ID"]), "user-agent" => "mobile"));
		    					$number--;
		    				}

	    					array_push($urls, array("url" => get_permalink($post["ID"]), "user-agent" => "desktop"));
	    					$number--;

		    				$pre_load->customposttypes = $pre_load->customposttypes + 1;
		    			}
		    		}else{
		    			$pre_load->customposttypes = -1;
		    		}
				}


				// POST
				if($number > 0 && isset($pre_load->post) && $pre_load->post > -1){
		    		// $recent_posts = wp_get_recent_posts(array(
								// 			'numberposts' => $number,
								// 		    'offset' => $pre_load->post,
								// 		    'orderby' => 'ID',
								// 		    'order' => 'DESC',
								// 		    'post_type' => 'post',
								// 		    'post_status' => 'publish',
								// 		    'suppress_filters' => true
								// 		    ), ARRAY_A);
		    		global $wpdb;
		    		$recent_posts = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS  ".$wpdb->prefix."posts.ID FROM ".$wpdb->prefix."posts  WHERE 1=1  AND (".$wpdb->prefix."posts.post_type = 'post') AND ((".$wpdb->prefix."posts.post_status = 'publish'))  ORDER BY ".$wpdb->prefix."posts.ID DESC LIMIT ".$pre_load->post.", ".$number, ARRAY_A);


		    		if(count($recent_posts) > 0){
		    			foreach ($recent_posts as $key => $post) {
		    				if($mobile_theme){
		    					array_push($urls, array("url" => get_permalink($post["ID"]), "user-agent" => "mobile"));
		    					$number--;
		    				}

	    					array_push($urls, array("url" => get_permalink($post["ID"]), "user-agent" => "desktop"));
	    					$number--;

		    				$pre_load->post = $pre_load->post + 1;
		    			}
		    		}else{
		    			$pre_load->post = -1;
		    		}
				}


				// ATTACHMENT
				if($number > 0 && isset($pre_load->attachment) && $pre_load->attachment > -1){
					global $wpdb;
		    		$recent_attachments = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS  ".$wpdb->prefix."posts.ID FROM ".$wpdb->prefix."posts  WHERE 1=1  AND (".$wpdb->prefix."posts.post_type = 'attachment') ORDER BY ".$wpdb->prefix."posts.ID DESC LIMIT ".$pre_load->attachment.", ".$number, ARRAY_A);

		    		if(count($recent_attachments) > 0){
		    			foreach ($recent_attachments as $key => $attachment) {
		    				if($mobile_theme){
		    					array_push($urls, array("url" => get_permalink($attachment["ID"]), "user-agent" => "mobile"));
		    					$number--;
		    				}

	    					array_push($urls, array("url" => get_permalink($attachment["ID"]), "user-agent" => "desktop"));
	    					$number--;

		    				$pre_load->attachment = $pre_load->attachment + 1;
		    			}
		    		}else{
		    			$pre_load->attachment = -1;
		    		}
				}

				// PAGE
				if($number > 0 && isset($pre_load->page) && $pre_load->page > -1){
					$pages = get_pages(array(
							'sort_order' => 'DESC',
							'sort_column' => 'ID',
							'parent' => -1,
							'hierarchical' => 0,
							'number' => $number,
							'offset' => $pre_load->page,
							'post_type' => 'page',
							'post_status' => 'publish'
					));

					if(count($pages) > 0){
						foreach ($pages as $key => $page) {
							$page_url = get_option("home")."/".get_page_uri($page->ID);

		    				if($mobile_theme){
		    					array_push($urls, array("url" => $page_url, "user-agent" => "mobile"));
		    					$number--;
		    				}

	    					array_push($urls, array("url" => $page_url, "user-agent" => "desktop"));
	    					$number--;

		    				$pre_load->page = $pre_load->page + 1;
						}
					}else{
						$pre_load->page = -1;
					}
				}

				// CATEGORY
				if($number > 0 && isset($pre_load->category) && $pre_load->category > -1){
					// $categories = get_terms(array(
					// 							'taxonomy'          => array('category', 'product_cat'),
					// 						    'orderby'           => 'id', 
					// 						    'order'             => 'DESC',
					// 						    'hide_empty'        => false, 
					// 						    'number'            => $number, 
					// 						    'fields'            => 'all', 
					// 						    'pad_counts'        => false, 
					// 						    'offset'            => $pre_load->category
					// 						));

					global $wpdb;
		    		$categories = $wpdb->get_results("SELECT  t.*, tt.* FROM ".$wpdb->prefix."terms AS t  INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('category', 'product_cat', 'hersteller', 'anschlussart', 'typ') ORDER BY t.term_id ASC LIMIT ".$pre_load->category.", ".$number, ARRAY_A);

					if(count($categories) > 0){
						foreach ($categories as $key => $category) {
							if($mobile_theme){
								array_push($urls, array("url" => get_term_link($category["slug"], $category["taxonomy"]), "user-agent" => "mobile"));
								$number--;
							}

							array_push($urls, array("url" => get_term_link($category["slug"], $category["taxonomy"]), "user-agent" => "desktop"));
							$number--;

							$pre_load->category = $pre_load->category + 1;

						}
					}else{
						$pre_load->category = -1;
					}
				}

				// TAG
				if($number > 0 && isset($pre_load->tag) && $pre_load->tag > -1){
					// $tags = get_terms(array(
					// 							'taxonomy'          => array('post_tag', 'product_tag'),
					// 						    'orderby'           => 'id', 
					// 						    'order'             => 'DESC',
					// 						    'hide_empty'        => false, 
					// 						    'number'            => $number, 
					// 						    'fields'            => 'all', 
					// 						    'pad_counts'        => false, 
					// 						    'offset'            => $pre_load->tag
					// 						));

					global $wpdb;
		    		$tags = $wpdb->get_results("SELECT  t.*, tt.* FROM ".$wpdb->prefix."terms AS t  INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('post_tag', 'product_tag') ORDER BY t.term_id ASC LIMIT ".$pre_load->tag.", ".$number, ARRAY_A);


					if(count($tags) > 0){
						foreach ($tags as $key => $tag) {
							if($mobile_theme){
								array_push($urls, array("url" => get_term_link($tag["slug"], $tag["taxonomy"]), "user-agent" => "mobile"));
								$number--;
							}

							array_push($urls, array("url" => get_term_link($tag["slug"], $tag["taxonomy"]), "user-agent" => "desktop"));
							$number--;

							$pre_load->tag = $pre_load->tag + 1;

						}
					}else{
						$pre_load->tag = -1;
					}
				}






				if(count($urls) > 0){
					foreach ($urls as $key => $arr) {
						$user_agent = "";

						if($arr["user-agent"] == "desktop"){
							$user_agent = "WP Fastest Cache Preload Bot";
						}else if($arr["user-agent"] == "mobile"){
							$user_agent = "WP Fastest Cache Preload iPhone Mobile Bot";
						}

						if($wpfc_remote_get($arr["url"], $user_agent)){
							$status = "<strong style=\"color:lightgreen;\">OK</strong>";
						}else{
							$status = "<strong style=\"color:red;\">ERROR</strong>";
						}

						echo $status." ".$arr["url"]." (".$arr["user-agent"].")<br>";
					}
					echo "<br>";
					echo count($urls)." page have been cached";

		    		update_option("WpFastestCachePreLoad", json_encode($pre_load));

		    		echo "<br><br>";

		    		// if(isset($pre_load->homepage)){
		    		// 	if($pre_load->homepage == -1){
		    		// 		echo "Homepage: 1/1"."<br>";
		    		// 	}
		    		// }

		    		// if(isset($pre_load->post)){
			    	// 	if($pre_load->post > -1){
			    	// 		echo "Posts: ".$pre_load->post."/".$count_posts->publish."<br>";
			    	// 	}else{
			    	// 		echo "Posts: ".$count_posts->publish."/".$count_posts->publish."<br>";
			    	// 	} 
		    		// }

		    		// if(isset($pre_load->page)){
		    		// 	if($pre_load->page > -1){
		    		// 		echo "Pages: ".$pre_load->page."/".$count_pages->publish."<br>";
		    		// 	}else{
		    		// 		echo "Pages: ".$count_pages->publish."/".$count_pages->publish."<br>";
		    		// 	}
		    		// }
				}else{
					if(isset($options->wpFastestCachePreload_restart)){
						foreach ($pre_load as $pre_load_key => &$pre_load_value) {
							if($pre_load_key != "number"){
								$pre_load_value = 0;
							}
						}

						update_option("WpFastestCachePreLoad", json_encode($pre_load));

						echo "Preload Restarted";

						include_once('cdn.php');
						CdnWPFC::cloudflare_clear_cache();
					}else{
						echo "Completed";
						
						wp_clear_scheduled_hook("wp_fastest_cache_Preload");
					}
				}
			}

			if(isset($_GET) && isset($_GET["type"])  && $_GET["type"] == "preload"){
				die();
			}
		}
	}
?>