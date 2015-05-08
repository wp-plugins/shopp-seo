<?php

/*
	Plugin Name: Shopp SEO
	Plugin URI: https://mainehost.com/wordpress-plugins/	
	Description: Adds SEO features to Shopp. Requires Shopp and WordPress SEO.
	Author: Maine Hosting Solutions
	Author URI: http://mainehost.com/
	Version: 1.0.6	
*/

if(!class_exists("shopp_seo_mhs")) {
	/**
	 * @package default
	 */
	class shopp_seo_mhs {
		/**
		 * @var bool Used to log if there's a dependency error.
		 */
		private $dep_error = false;
		/**
		 * @var string Tracks the folder this plugin lives in for use in creating the menu
		 * and assorted other functions.
		 */
		protected $ssmhs_folder = '';
		/**
		 * @var string Used for the title option.
		 */
		protected $seo_title = 'ssmhs_%s_title';
		/**
		 * @var string Used for the description option.
		 */
		protected $seo_desc = 'ssmhs_%s_desc';

		/**
		 * @var string Used for noindex, follow option.
		 */
		protected $seo_noindex = 'ssmhs_%s_noindex';

		/**
		 * @var array Stores the areas where we need to work on meta.
		 */
		protected $meta_area = array('Catalog'=>'catalog','Account'=>'account','Cart'=>'cart','Checkout'=>'checkout','New Collection'=>'new','Featured Collection'=>'featured','On Sale Collection'=>'onsale','Promotion Collection'=>'promo','Best Sellers Collection'=>'bestsellers','Random Collection'=>'random','Related Collection'=>'related');

		/**
		 * @var array Stores the area descriptions to explain what they are working on.
		 */
		protected $meta_area_desc = array(
			'catalog'=>'The landing page of Shopp, usually /shop/',
			'account'=>'Where customers register and log in.',
			'cart'=>'Page showing the cart contents for customers.',
			'checkout'=>'Shown when a customer is processing through the checkout.',
			'new'=>'New products collection.',
			'featured'=>'Featured products collection.',
			'onsale'=>'On Sale products collection.',
			'promo'=>'Promotion products collection.',
			'bestsellers'=>'Best Sellers products collection.',
			'random'=>'Random products collection.',
			'related'=>'Related products collection.'
		);

		/**
		 * @var string Tracks where in Shopp we are.
		 */
		protected $shopp_slug = '';

	    /**
	     * Setup hooks, actions, filters, and whatever is needed for the plugin to run.
	     */
		function __construct() {
			register_activation_hook( __FILE__, array($this,'activate'));

			add_action('plugins_loaded', array($this,'notice_check'));
			add_action('admin_menu', array($this,'menu'));
			add_action('admin_enqueue_scripts', array($this,'admin_scripts'));

			add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this,'add_action_link'));

			define('SSEO_NAME','Shopp SEO');
			define('SSEO_MENU_NAME','Shopp SEO');
			define('SSEO_SHOPP_NAME','Shopp');
			define('SSEO_WP_SEO_NAME','WordPress SEO by Yoast');
			define('SSEO_SHOPP_PATH','shopp/Shopp.php');
			define('SSEO_WPSEO_PATH','wordpress-seo/wp-seo.php');
			define('SSEO_WPSEOP_PATH','wordpress-seo-premium/wp-seo-premium.php');
			define('SSEO_DEP_ERROR','<div class="error"><p>%s is not installed or active. ' . SSEO_NAME . ' will not function until %s is installed and activated.</p></div>');
			define('SSEO_TITLE_P', 30);
			define('SSEO_DESC_P', 30);
			define('SSEO_ROBOTS_P', 40);
		}
		/**
		 * Run when this plugin is activated.
		 */
		function activate() {
			$this->check_dependencies();
		}	
		/**
		 * Adds the extra links on the plugins page.
		 * @param array $links - The exsting default links.
		 * @return array - Merge in my link array to the existing and return that.
		 */
		function add_action_link($links) {
			$path = admin_url();

			$mylinks = array(
				'<a href="https://wordpress.org/support/view/plugin-reviews/shopp-seo" target="_blank">Rate and Review</a>',
				'<a href="' . $path . 'options-general.php?page=shopp-seo">Settings</a>'
			);

			return array_merge($mylinks, $links);
		}
		/**
		 * Used to check if dependencies are active when a plugin is deactivated.
		 */
		function notice_check() {
			$this->dependencies();

			if($this->dep_error) {
	            add_action('admin_notices', array($this,'deactivate_notice'));
	        }
	        else {
				$title_p = (get_option('ssmhs_title_priority') >= 0) ? get_option('ssmhs_title_priority') : SSEO_TITLE_P;
				$desc_p = (get_option('ssmhs_desc_priority') >= 0) ? get_option('ssmhs_desc_priority') : SSEO_DESC_P;
				$robots_p = (get_option('ssmhs_robots_priority') >= 0) ? get_option('ssmhs_robots_priority') : SSEO_ROBOTS_P;

				add_action('admin_init', array($this,'metabox'));
				add_action('shopp_product_saved', array($this,'save_fields'));
				add_action('wpseo_head', array($this,'the_robots'), $robots_p);

				add_filter('wpseo_title', array($this,'the_title'), $title_p);
				add_filter('wpseo_metadesc', array($this,'the_description'), $desc_p);			
				add_filter('shopp_meta_description', array($this,'remove_shopp_desc'));				
	        }
		}		
	    /**
	     * Gives an error if trying to activate the plugin without dependencies.
	     * @param string $message The error message returned.
	     * @param mixed $errno Error number returned.
	     * @return mixed It will either echo the error or fire trigger_error() as needed.
	     */
		function br_trigger_error($message = false, $errno) {
		    if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') {
		        echo '<strong>' . $message . '</strong>';
		        exit;
		    }
		    else {
		        trigger_error($message, $errno);
		    }
		}
		/**
		 * Echos the error generated by deactivating a dependency.
		 * @return string
		 */
		function deactivate_notice() {
			echo $this->dep_error;
		}
		/**
		 * Checks to see that the dependencies are installed and active.
		 * @param type $stage Whether it's currently activating or deactivating a plugin.
		 */
		function dependencies() {		
			if(!in_array(SSEO_SHOPP_PATH, apply_filters('active_plugins', get_option('active_plugins')))) {
				$this->dep_error .= sprintf(SSEO_DEP_ERROR, SSEO_SHOPP_NAME, SSEO_SHOPP_NAME);
			}
			if((!in_array(SSEO_WPSEO_PATH, apply_filters('active_plugins', get_option('active_plugins')))) && ((!in_array(SSEO_WPSEOP_PATH, apply_filters('active_plugins', get_option('active_plugins')))))) {
				$this->dep_error .= sprintf(SSEO_DEP_ERROR, SSEO_WP_SEO_NAME, SSEO_WP_SEO_NAME);
			}
		}
		/**
		 * Core function to check for dependencies.
		 */
		function check_dependencies() {
			$this->dependencies();

			if($this->dep_error) $this->br_trigger_error($this->dep_error, E_USER_ERROR);
		}
		/**
		 * Setup our javascript for the admin area.
		 */
		function admin_scripts() {
			wp_enqueue_script('mhs_seo_admin', plugin_dir_url( __FILE__ ) . 'admin.js', array( 'jquery'), false, true);
		}
		/**
		 * Creates the menu in WP admin for the plugin.
		 */
		function menu() {
			$this->ssmhs_folder = plugin_basename(dirname(__FILE__));

			add_submenu_page('options-general.php', SSEO_MENU_NAME, SSEO_MENU_NAME,'administrator', $this->ssmhs_folder, array($this,'admin'));
		}
		/**
		 * Admin area for this plugin.
		 */
		function admin() {
			require 'sseo_admin.php';
		}
	    /**
	     * Removes the meta description that Shopp includes since it creates two elements as a result.
	     * @param mixed $content Passed in from Shopp's meta description.
	     * @return false Always returns false to remove it from the <head>
	     */
		function remove_shopp_desc($content) {
			return false;
		}
	    /**
	     * Check to see if this is a Shopp collection that is not a category.
	     * @return bool True if it's a non-category collection or false.
	     */
		function needs_meta() {
			$slug = isset(ShoppCollection()->slug) ? shopp('collection','get-slug') : false;

			if(is_shopp_collection() && in_array($slug, $this->meta_area) && !is_home() && !is_front_page()) {
				$this->shopp_slug = $slug;
				return true;
			}
			elseif(is_account_page()) {
				$this->shopp_slug = 'account';
				return true;
			}
			elseif(is_cart_page()) {
				$this->shopp_slug = 'cart';
				return true;
			}
			elseif(is_checkout_page()) {
				$this->shopp_slug = 'checkout';
				return true;
			}
			elseif(is_catalog_frontpage()) {
				$this->shopp_slug = 'catalog';
				return true;
			}
			else {
				return false;
			}
		}
	    /**
	     * Replaces pseudo from Yoast that do not get replaced inside the Shopp non-category collection.
	     * @param string $content - The text to search for replacements in.
	     * @return string The replaced text.
	     */
		function pre_replacements($content) {
			# Shopp uses paged and Yoast does not replace %%pagenumber%% with paged.
			$page = get_query_var('paged');
			return str_replace('%%pagenumber%%', $page, $content);
		}
	    /**
	     * Filter function for wpseo_title. Replaces title text with the title defined by this plugin
		 * if this is a Shopp non-category collection.
	     * @param string $title The title passed in from wpseo_title.
	     * @return string Returns the adjusted title on success or the passed in one otherwise.
	     */
		function the_title($title) {
			if($this->needs_meta() && $this->shopp_slug) {
				$r_title = get_option(sprintf($this->seo_title, $this->shopp_slug));

				if($r_title) {
					$r_title = $this->pre_replacements($r_title);
					$title = wpseo_replace_vars($r_title);
				}
			}

			return $title;
		}
		/**
		 * Filter function for wpseo_metadesc to put in our description.
		 * @param string $desc The description passed in from the Yoast filter or false if I'm setting
		 * it manually.
		 * @return string
		 */
		function the_description($desc) {
			if($this->needs_meta() && $this->shopp_slug) {
				$r_desc = get_option(sprintf($this->seo_desc, $this->shopp_slug));

				if($r_desc) {
					$r_desc = $this->pre_replacements($r_desc);
					$desc = wpseo_replace_vars($r_desc);
				}
			}

			return $desc;
		}
	    /**
	     * Action function for wp_head. Injects the meta description tag if it's a Shopp non-category collection.
	     * @return string The meta description tag on success.
	     */
		function the_robots() {
			$noindex = get_option(sprintf($this->seo_noindex, $this->shopp_slug));

			if($noindex) {
				echo '<meta name="robots" content="noindex,follow" />' . "\n";
			}

		}
		/**
		 * Creates the meta boxes on Shopp categories and products.
		 */
		function metabox() {
			add_meta_box( 'seo_fields', __(SSEO_NAME), array($this,'seo_fields'), 'shopp_product', 'advanced', 'high' );
			add_meta_box( 'seo_fields_cat', __(SSEO_NAME), array($this,'seo_fields'), 'shopp_page_shopp-category', 'advanced', 'high' );

			# Load up Yoast's taxonomy class to handle the term update
			if($_GET['page'] == 'shopp-categories' && $_GET['id']) {
				$tax = new WPSEO_Taxonomy();
			}
		}
		/**
		 * Draws the actual meta boxes for categories and products.
		 */
		function seo_fields() {
			global $post, $wpdb;

			if($post->ID) {
				$title = get_post_meta($post->ID,'_yoast_wpseo_title', true);
				$desc = get_post_meta($post->ID,'_yoast_wpseo_metadesc', true);
			}
			else {
				$tax = true;
				$meta = get_option('wpseo_taxonomy_meta');
				$title = $meta['shopp_category'][$_GET['id']]['wpseo_title'];
				$desc = $meta['shopp_category'][$_GET['id']]['wpseo_desc'];
			}

			echo '
				<p>
				<b>SEO Title:</b><br /><input type="text" name="' . ((!$tax) ? 'mhs_seo_title' : 'wpseo_title') . '" size="50" value="' . $title . '" id="mhs_seo_title" class="mhs_seo_title" style="width: 100%;">
				<div id="mhs_seo_title_area"></div>
				</p>
				<p>
				<b>SEO Description:</b><br />
				<textarea name="' . ((!$tax) ? 'mhs_seo_desc' : 'wpseo_desc') . '" rows="5" cols="50" id="mhs_seo_desc" style="width: 100%;" class="mhs_seo_desc" wrap>' . $desc . '</textarea>
				<div id="mhs_seo_desc_area"></div>';
		}
		/**
		 * Fired to save the meta fields.
		 * @param type $Product The product variable from Shopp.
		 */
		function save_fields($Product) {
			$Product->load_data(); # Reloads the product from Shopp
			$post_id = $Product->id;

			if($post_id === null and array_key_exists('id', $_GET)) $post_id = (int) $_GET['id'];

			if($post_id) {
				update_post_meta($post_id, '_yoast_wpseo_title', $_POST['mhs_seo_title']);
				update_post_meta($post_id, '_yoast_wpseo_metadesc', $_POST['mhs_seo_desc']);
			}
		}
	}
}

$shopp_seo_mhs = new shopp_seo_mhs();

?>