<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    funnelcockpit
 * @subpackage funnelcockpit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    funnelcockpit
 * @subpackage funnelcockpit/admin
 * @author     Your Name <email@example.com>
 */
class FunnelCockpit_Admin {

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
	 * @param      string    $funnelcockpit       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $funnelcockpit, $version ) {

		$this->funnelcockpit = $funnelcockpit;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->funnelcockpit, plugin_dir_url( __FILE__ ) . 'css/funnelcockpit-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->funnelcockpit, plugin_dir_url( __FILE__ ) . 'js/funnelcockpit-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function admin_init() {
		add_meta_box("funnelpage_id_meta", "FunnelCockpit " . _x('Page', 'post type singular name', 'funnelcockpit'), array(&$this, 'funnelpage_id_meta'), "funnelpage", "normal", "default");
		register_setting('funnelcockpit_options', 'funnelcockpit_apikey_private');
		register_setting('funnelcockpit_options', 'funnelcockpit_apikey_public');
		register_setting('funnelcockpit_options', 'funnelcockpit_funnel_id');
	}

	public function funnelpage_register() {
		$labels = array(
			'menu_name' => _x('FunnelCockpit', 'post type general name', 'funnelcockpit'),
			'name' => _x('Pages', 'post type general name', 'funnelcockpit'),
			'singular_name' => _x('Page', 'post type singular name', 'funnelcockpit'),
			'add_new' => _x('Add New', 'portfolio item', 'funnelcockpit'),
			'add_new_item' => __('Add New Page', 'funnelcockpit'),
			'edit_item' => __('Edit Page', 'funnelcockpit'),
			'new_item' => __('New Page', 'funnelcockpit'),
			'view_item' => __('View Page', 'funnelcockpit'),
			'search_items' => __('Search Pages', 'funnelcockpit'),
			'not_found' =>  __('Nothing found', 'funnelcockpit'),
			'not_found_in_trash' => __('Nothing found in Trash', 'funnelcockpit'),
			'parent_item_colon' => '',
			'all_items' =>  __('Pages', 'funnelcockpit'),
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array('slug' => ''),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => false,
			'menu_icon' => '',
		);

		register_post_type( 'funnelpage' , $args );
	}

	public function funnelpage_id_meta() {
		?>

		<noscript>
			<div class="error notice"><p><strong><?php _e('Please enable Javascript to edit this Funnel page!', 'funnelcockpit'); ?></strong></p></div>
		</noscript>

		<div class="wrap">
			<table class="form-table">

				<?php
				global $post;
				$custom = get_post_custom($post->ID);
				$apiKeyPrivate = get_option('funnelcockpit_apikey_private');
				$apiKeyPublic = get_option('funnelcockpit_apikey_public');
				$funnelId = $custom["funnel_id"][0];;
				$funnelPageId = $custom["funnelpage_id"][0];
				$fetchTime = get_transient('funnelpage_' . $funnelPageId . '_time');

				if (!empty($apiKeyPrivate))
				{
					$response = wp_remote_post( 'http://api.funnelcockpit.com/funnels', array( 'headers' => array( 'Authorization' => $apiKeyPrivate ) ) ); // TODO: https!
					if ($response['response']['code'] == 200 && isset($response['body']))
					{
						$funnels = json_decode($response['body']);
						if ($funnels && count($funnels) > 0)
						{
							if (empty($funnelId) || empty($funnelPageId))
							{
								echo '<div class="update-nag notice"><p>' . __('Please select a Funnel and a Page below to get started!', 'funnelcockpit') . '</p></div>';
							}
							?>

							<tr valign="top">
								<th scope="row"><?php _e('Select Funnel', 'funnelcockpit'); ?></th>
								<td>
									<select name="funnel_id">
									<?php
										echo '<option value="" disabled' . (empty($funnelId) ? ' selected' : '') . '>' . __('Please select...', 'funnelcockpit') . '</option>';
										foreach ( $funnels as $funnel ) {
											echo '<option value="' . $funnel->_id . '"' . ($funnelId == $funnel->_id ? ' selected' : '') . ' data-funnel-id="' . $funnel->_id . '">' . $funnel->name . '</option>';
										}
									?>
									</select>
								</td>
							</tr>

							<?php
						}
						else
						{
							echo '<div class="error notice"><p>' . __('No Funnels found. Please create one first at funnelcockpit.com!', 'funnelcockpit') . '</p></div>';
						}
					}
					else if (!empty($response['response'])) {
						echo '<div class="error notice"><p>API Error: ' . $response['response']['message'] . '</p></div>';
					}
					else if (!empty($response['errors']))
					{
						echo '<div class="error notice"><p>API Error: ' . $response['errors'][0] . '</p></div>';
					}

					$response = wp_remote_post( 'http://api.funnelcockpit.com/funnel-pages', array( 'headers' => array( 'Authorization' => $apiKeyPrivate ) ) ); // TODO: https!
					if ($response['response']['code'] == 200 && isset($response['body']))
					{
						$funnelPages = json_decode($response['body']);
						if ($funnelPages && count($funnelPages) > 0)
						{
						?>

							<tr valign="top">
								<th scope="row"><?php _e('Select Page', 'funnelcockpit'); ?></th>
								<td>
									<select name="funnelpage_id">
									<?php
										echo '<option value="" disabled' . (empty($funnelPageId) ? ' selected' : '') . '>' . __('Please select...', 'funnelcockpit') . '</option>';
										foreach ( $funnelPages as $funnelPage ) {
											echo '<option value="' . $funnelPage->_id . '"' . ($funnelPageId == $funnelPage->_id ? ' selected' : '') . ' data-funnel-id="' . $funnelPage->funnelId . '" data-title="' . $funnelPage->title . '">' . $funnelPage->title . '</option>';
										}
									?>
									</select>
								</td>
							</tr>

						<?php
						}
						else
						{
							echo '<div class="error notice"><p>' . _e('No funnel pages found! Please create one first at funnelcockpit.com!', 'funnelcockpit') . '</p></div>';
						}
					}
					else if (!empty($response['response'])) {
						echo '<div class="error notice"><p>API Error: ' . $response['response']['message'] . '</p></div>';
					}
					else if (!empty($response['errors']))
					{
						echo '<div class="error notice"><p>API Error: ' . $response['errors'][0] . '</p></div>';
					}
				}

				?>

				<tr valign="top">
					<th scope="row"><?php _e('Link', 'funnelcockpit'); ?></th>
					<td>
						<input type="text" name="post_name" value="<?php echo $post->post_name; ?>" class="regular-text" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e('Cache', 'funnelcockpit'); ?></th>
					<td>
						<p class="text-muted"><?php _e('Your page is fetched from our servers every 30 minutes.', 'funnelcockpit'); ?></p>
						<?php if (!empty($funnelPageId) && !empty($fetchTime)) { ?>
							<p>Last fetch: <strong><?php echo date('d.m.Y H:i', $fetchTime); ?></strong></p>
						<?php } ?>
						<br />
						<p><?php submit_button( __('Clear cache', 'funnelcockpit'), 'secondary', 'clear_cache', false ); ?></p>
					</td>
				</tr>

			</table>
		</div>

	  <?php
	}

	public function save_funnelpage_meta($post_id, $post) {
		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;

		if (isset($_POST['clear_cache']) && !empty($_POST['funnelpage_id'])) {
			delete_transient('funnelpage_' . $_POST['funnelpage_id'] . '_head');
			delete_transient('funnelpage_' . $_POST['funnelpage_id'] . '_body');
			delete_transient('funnelpage_' . $_POST['funnelpage_id'] . '_time');
		}

		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.

		$meta = array(
			'funnel_id' => $_POST['funnel_id'],
			'funnelpage_id' => $_POST['funnelpage_id'],
		);

		// Add values of $events_meta as custom fields

		foreach ($meta as $key => $value) { // Cycle through the $events_meta array!
			if ( $post->post_type == 'revision' ) return; // Don't store custom data twice
			$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
			if (get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
				update_post_meta($post->ID, $key, $value);
			} else { // If the custom field doesn't have a value
				add_post_meta($post->ID, $key, $value);
			}
			if (!$value) delete_post_meta($post->ID, $key); // Delete if blank
		}

		// prevent infinite loop
		remove_action('save_post', array( &$this, 'save_funnelpage_meta' ), 1 );

		if (!empty($_POST['post_title']) && empty($_POST['post_name'])) {
			$title = $_POST['post_title'];
			$new_slug = sanitize_title( $title );
			if ( $post->post_name != $new_slug )
			{
				wp_update_post(
					array (
						'ID'        => $post->ID,
						'post_name' => $new_slug
					)
				);
			}
		}

		// if (!empty($_POST['funnelpage_title']))
	}

	public function add_admin_menu() {
		add_submenu_page('edit.php?post_type=funnelpage', 'FunnelCockpit Settings', 'Settings', 'create_users', basename(__FILE__), array(&$this, 'funnelcockpit_options' ));
	}

	public function funnelcockpit_options() {
		$apiKeyPublic = get_option('funnelcockpit_apikey_public');
		$apiKeyPrivate = get_option('funnelcockpit_apikey_private');

		?>

		<div class="wrap">
			<img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/logo.png'; ?>" alt="FunnelCockpit" height="50">
			<p><?php sprintf(__('You need to have a %s account with an already set up funnel to use this plugin.', 'funnelcockpit'), '<a target="_blank" href="https://funnelcockpit.com/">FunnelCockpit</a>'); ?></p>
			<form method="post" action="options.php">
				<?php settings_fields( 'funnelcockpit_options' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Public API Key', 'funnelcockpit'); ?></th>
						<td><input type="text" name="funnelcockpit_apikey_public" value="<?php echo $apiKeyPublic; ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Private API Key', 'funnelcockpit'); ?></th>
						<td><input type="text" name="funnelcockpit_apikey_private" value="<?php echo $apiKeyPrivate; ?>" class="regular-text" /></td>
					</tr>
				</table>

				<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings', 'funnelcockpit') ?>" /></p>
			</form>
		</div>

		<?php
	}

}
