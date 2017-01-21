<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://funnelcockpit.com
 * @since      1.0.0
 *
 * @package    funnelcockpit
 * @subpackage funnelcockpit/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    funnelcockpit
 * @subpackage funnelcockpit/public
 * @author     FunnelCockpit <support@funnelcockpit.com>
 */
class FunnelCockpit_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $funnelcockpit    The ID of this plugin.
	 */
	private $funnelcockpit;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $funnelcockpit       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $funnelcockpit, $version ) {

		$this->funnelcockpit = $funnelcockpit;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in funnelcockpit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The funnelcockpit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->funnelcockpit, plugin_dir_url( __FILE__ ) . 'css/funnelcockpit-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in funnelcockpit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The funnelcockpit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->funnelcockpit, plugin_dir_url( __FILE__ ) . 'js/funnelcockpit-public.js', array( 'jquery' ), $this->version, false );

	}

	public function funnelpage_template($single) {
		global $post;

		if ($post->post_type == 'funnelpage') {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
			remove_action( 'wp_head', 'wp_generator' );
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link');
			remove_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_filter('show_admin_bar', '__return_false');

			// setup funnel page
			$funnelPageId = get_post_meta($post->ID, 'funnelpage_id', true);
			$funnelPageHead = get_transient( 'funnelpage_' . $funnelPageId . '_head' );
			$funnelPageBody = get_transient( 'funnelpage_' . $funnelPageId . '_body' );
			if ( false === $funnelPageHead || false === $funnelPageBody ) {
				$response = wp_remote_get( 'http://api.funnelcockpit.com/funnel-page/' . $funnelPageId ); // TODO: https!
				if (isset($response['body'])) {
					$funnelPage = json_decode($response['body']);
					$funnelPageHead = $funnelPage->head;
					$funnelPageBody = $funnelPage->body;
					set_transient( 'funnelpage_' . $funnelPageId . '_head', $funnelPage->head, 60 * 30 );
					set_transient( 'funnelpage_' . $funnelPageId . '_body', $funnelPage->body, 60 * 30 );
					set_transient( 'funnelpage_' . $funnelPageId . '_time', time(), 60 * 30 );
				}
			}

			add_action('wp_head', function() use ($funnelPageHead) {
				echo preg_replace("#</?(head)[^>]*>#i", "", $funnelPageHead);;
			});

			$path = plugin_dir_path( __FILE__ ). 'funnelpage-template.php';
			if (file_exists($path)) {
				return $path;
			}
		}
		return $single;
	}


	/**
	 * Remove the slug from published post permalinks. Only affect our custom post type, though.
	 */
	function gp_remove_cpt_slug( $post_link, $post, $leavename ) {

		if ( 'funnelpage' != $post->post_type || 'publish' != $post->post_status ) {
			return $post_link;
		}

		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	/**
	 * Have WordPress match postname to any of our public post types (post, page, funnelpage)
	 * All of our public post types can have /post-name/ as the slug, so they better be unique across all posts
	 * By default, core only accounts for posts and pages where the slug is /post-name/
	 */
	function gp_parse_request_trick( $query ) {

		// Only noop the main query
		if ( ! $query->is_main_query() )
			return;

		// Only noop our very specific rewrite rule match
		if ( 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}

		// 'name' will be set if post permalinks are just post_name, otherwise the page rule will match
		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array( 'post', 'page', 'funnelpage' ) );
		}
	}

}
