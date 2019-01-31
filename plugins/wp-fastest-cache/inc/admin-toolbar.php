<?php
	class WpFastestCacheAdminToolbar{
		public function __construct(){}

		public function add(){
			if(is_admin()){
				add_action('wp_before_admin_bar_render', array($this, "wpfc_tweaked_toolbar_on_admin_panel"));
				add_action('admin_enqueue_scripts', array($this, 'load_toolbar_js'));
				add_action('admin_enqueue_scripts', array($this, 'load_toolbar_css'));
			}else{
				if(is_admin_bar_showing()){
					add_action('wp_before_admin_bar_render', array($this, "wpfc_tweaked_toolbar_on_frontpage"));
					add_action('wp_enqueue_scripts', array($this, 'load_toolbar_js'));
					add_action('wp_enqueue_scripts', array($this, 'load_toolbar_css'));
					add_action('wp_footer', array($this, 'print_my_inline_script'));
				}
			}
		}

		public function load_toolbar_js(){
			wp_enqueue_script("wpfc-toolbar", plugins_url("wp-fastest-cache/js/toolbar.js"), array(), time(), true);
		}

		public function load_toolbar_css(){
			wp_enqueue_style("wp-fastest-cache-toolbar", plugins_url("wp-fastest-cache/css/toolbar.css"), array(), time(), "all");
		}

		public function print_my_inline_script() {
			?>
			<script type="text/javascript">var wpfc_ajaxurl = "<?php echo admin_url( 'admin-ajax.php' );?>";</script>
			<?php
		}

		public function wpfc_tweaked_toolbar_on_frontpage() {
			global $wp_admin_bar;

			$wp_admin_bar->add_node(array(
				'id'    => 'wpfc-toolbar-parent',
				'title' => 'Clear Cache'
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-clear-cache-of-this-page',
				'title' => 'Clear Cache of This Page',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache',
				'title' => 'Delete Cache',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache-and-minified',
				'title' => 'Delete Cache and Minified CSS/JS',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));
		}

		public function wpfc_tweaked_toolbar_on_admin_panel() {
			global $wp_admin_bar;

			$wp_admin_bar->add_node(array(
				'id'    => 'wpfc-toolbar-parent',
				'title' => 'Clear Cache'
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache',
				'title' => 'Delete Cache',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));

			$wp_admin_bar->add_menu( array(
				'id'    => 'wpfc-toolbar-parent-delete-cache-and-minified',
				'title' => 'Delete Cache and Minified CSS/JS',
				'parent'=> 'wpfc-toolbar-parent',
				'meta' => array("class" => "wpfc-toolbar-child")
			));
		}
	}
?>