<?php
	class WpFastestCacheColumn{
		public function __construct(){}

		public function add(){
			add_filter('manage_posts_columns', array($this, 'wpfc_clear_column_head'));
   			add_action('manage_posts_custom_column', array($this, 'wpfc_clear_column_content'), 10, 2);
   			add_filter('manage_pages_columns', array($this, 'wpfc_clear_column_head'));
   			add_action('manage_pages_custom_column', array($this, 'wpfc_clear_column_content'), 10, 2);
   			add_action('admin_enqueue_scripts', array($this, 'load_js'));
   			add_action('wp_ajax_wpfc_clear_cache_column', array($this, "clear_cache_column"));
		}

		public function clear_cache_column(){
			$GLOBALS["wp_fastest_cache"]->singleDeleteCache(false, esc_sql($_GET["id"]));

			die(json_encode(array("success" => true)));
		}

		public function load_js(){
			wp_enqueue_script("wpfc-column", plugins_url("wp-fastest-cache/js/column.js"), array(), time(), true);
		}

		public function wpfc_clear_column_head($defaults) {
			$defaults['wpfc_column_clear_cache'] = 'Cache';
			return $defaults;
		}

		public function wpfc_clear_column_content($column_name, $post_ID) {
			if($column_name == "wpfc_column_clear_cache"){
				echo '<button wpfc-clear-column="'.$post_ID.'" class="button wpfc-clear-column-action">
	                    <span>Clear</span>
					</button>';
			}
		}
	}
?>