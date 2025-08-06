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
		register_setting('funnelcockpit_options', 'funnelcockpit_apikey_private', array(
			'sanitize_callback' => 'sanitize_text_field'
		));
		register_setting('funnelcockpit_options', 'funnelcockpit_apikey_public', array(
			'sanitize_callback' => 'sanitize_text_field'
		));
		register_setting('funnelcockpit_options', 'funnelcockpit_funnel_id', array(
			'sanitize_callback' => 'sanitize_text_field'
		));
		register_setting('funnelcockpit_options', 'funnelcockpit_print_head', array(
			'sanitize_callback' => array( $this, 'sanitize_checkbox' )
		));

		if (get_option('permalink_structure') !== '/%postname%/') {
            add_action( 'admin_notices', array( $this, 'permalink_structure_notice' ) );
        }
	}

	/**
	 * Sanitize checkbox input
	 *
	 * @param mixed $input The input value
	 * @return string 'on' if checked, empty string if not
	 */
	public function sanitize_checkbox( $input ) {
		return ( isset( $input ) && $input == 'on' ) ? 'on' : '';
	}

	public function permalink_structure_notice() {
	    ?>
        <div class="notice error" >
            <p><?php esc_html_e( 'Die WordPress Permalinks Einstellung sollte auf "Beitragsname" bzw "Post name" stehen, damit deine FunnelCockpit Seiten erreichbar sind.', 'funnelcockpit' ); ?></p>
            <p><strong><a href="/wp-admin/options-permalink.php">Hier beheben</a></strong></p>
        </div>
        <?php
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

    function add_funnelpages_to_dropdown( $select )
    {
        if ( FALSE === strpos( $select, '<select name=\'page_on_front\'' ) && FALSE === strpos( $select, '<select name="page_on_front"' ) )
        {
            return $select;
        }

        $funnelpages = get_posts( array( 'post_type' => 'funnelpage' ) );

        if ( ! $funnelpages )
        {
            return $select;
        }

        $funnelpage_options = walk_page_dropdown_tree($funnelpages, 0,
            array(
                'depth' => 0,
                'child_of' => 0,
                'selected' => get_option( 'page_on_front' ),
                'echo' => 0,
                'name' => 'page_on_front',
                'id' => '',
                'show_option_none' => '',
                'show_option_no_change' => '',
                'option_none_value' => ''
            )
        );

        return str_replace( '</select>', $funnelpage_options . '</select>', $select );
    }

	public function funnelpage_id_meta() {
		?>

		<noscript>
			<div class="error notice"><p><strong><?php esc_html_e('Please enable Javascript to edit this Funnel page!', 'funnelcockpit'); ?></strong></p></div>
		</noscript>

		<div class="wrap">
			<?php wp_nonce_field( 'funnelcockpit_save_meta', 'funnelcockpit_meta_nonce' ); ?>
			<table class="form-table">

				<?php
				global $post;
				$custom = get_post_custom($post->ID);
				$apiKeyPrivate = get_option('funnelcockpit_apikey_private');
				$apiKeyPublic = get_option('funnelcockpit_apikey_public');
				$funnelId = !empty($custom["funnel_id"]) ? $custom["funnel_id"][0] : '';
				$funnelPageId = !empty($custom["funnelpage_id"]) ? $custom["funnelpage_id"][0] : '';
				$fetchTime = get_transient('funnelpage_' . $funnelPageId . '_time');

				if (!empty($apiKeyPrivate))
				{
					$response = wp_remote_post( 'https://api.funnelcockpit.com/funnels', array( 'headers' => array( 'Authorization' => $apiKeyPrivate ) ) );
					if (is_wp_error($response)) {
						?>
						<div class="error notice">
							<p><?php echo esc_html($response->get_error_message()); ?></p>
						</div>
						<?php
					}
					else if ($response['response']['code'] == 200 && isset($response['body']))
					{
						$funnels = json_decode($response['body']);
						if ($funnels && count($funnels) > 0)
						{
							if (empty($funnelId) || empty($funnelPageId))
							{
								echo '<div class="update-nag notice"><p>' . esc_html(__('Please select a Funnel and a Page below to get started!', 'funnelcockpit')) . '</p></div>';
							}
							?>

							<tr valign="top">
								<th scope="row"><?php esc_html_e('Select Funnel', 'funnelcockpit'); ?></th>
								<td>
									<select name="funnel_id">
									<?php
										echo '<option value="" disabled' . (empty($funnelId) ? ' selected' : '') . '>' . esc_html(__('Please select...', 'funnelcockpit')) . '</option>';
										foreach ( $funnels as $funnel ) {
											echo '<option value="' . esc_attr($funnel->_id) . '"' . ($funnelId == $funnel->_id ? ' selected' : '') . ' data-funnel-id="' . esc_attr($funnel->_id) . '">' . esc_html($funnel->name) . '</option>';
										}
									?>
									</select>
								</td>
							</tr>

							<?php
						}
						else
						{
							echo '<div class="error notice"><p>' . esc_html(__('No Funnels found. Please create one first at funnelcockpit.com!', 'funnelcockpit')) . '</p></div>';
						}
					}
					else if (!empty($response['response'])) {
						echo '<div class="error notice"><p>API Error: ' . esc_html($response['response']['message']) . '</p></div>';
					}

					$response = wp_remote_post( 'https://api.funnelcockpit.com/funnel-pages', array( 'headers' => array( 'Authorization' => $apiKeyPrivate ) ) );
					if (is_wp_error($response)) {
						?>
						<div class="error notice">
							<p><?php echo esc_html($response->get_error_message()); ?></p>
						</div>
						<?php
					}
					else if ($response['response']['code'] == 200 && isset($response['body']))
					{
						$funnelPages = json_decode($response['body']);
						if ($funnelPages && count($funnelPages) > 0)
						{
						?>

							<tr valign="top">
								<th scope="row"><?php esc_html_e('Select Page', 'funnelcockpit'); ?></th>
								<td>
									<select name="funnelpage_id">
									<?php
										echo '<option value="" disabled' . (empty($funnelPageId) ? ' selected' : '') . '>' . esc_html(__('Please select...', 'funnelcockpit')) . '</option>';
										foreach ( $funnelPages as $funnelPage ) {
                      if (!empty($funnelPage->membersAreaId)) {
                        continue;
                      }
                      if (!empty($funnelPage->splitTestVariant)) {
                        continue;
                      }
                      $name = !empty($funnelPage->name) ? $funnelPage->name : $funnelPage->title;
											echo '<option value="' . esc_attr($funnelPage->_id) . '"' . ($funnelPageId == $funnelPage->_id ? ' selected' : '') . ' data-funnel-id="' . esc_attr($funnelPage->funnelId) . '" data-title="' . esc_attr($name) . '">' . esc_html($name) . '</option>';
										}
									?>
									</select>
								</td>
							</tr>

						<?php
						}
						else
						{
							echo '<div class="error notice"><p>' . esc_html(__('No funnel pages found! Please create one first at funnelcockpit.com!', 'funnelcockpit')) . '</p></div>';
						}
					}
					else if (!empty($response['response'])) {
						echo '<div class="error notice"><p>API Error: ' . esc_html($response['response']['message']) . '</p></div>';
					}
				}

				?>

				<tr valign="top">
					<th scope="row"><?php esc_html_e('Link', 'funnelcockpit'); ?></th>
					<td>
						<input type="text" name="post_name" value="<?php echo esc_attr($post->post_name); ?>" class="regular-text" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php esc_html_e('Cache', 'funnelcockpit'); ?></th>
					<td>
						<p class="text-muted"><?php esc_html_e('Your page is fetched from our servers every 30 minutes.', 'funnelcockpit'); ?></p>
											<?php if (!empty($funnelPageId) && !empty($fetchTime)) { ?>
						<p>Last fetch: <strong><?php echo esc_html(gmdate('d.m.Y H:i', $fetchTime)); ?></strong></p>
					<?php } ?>
						<br />
						<p><?php submit_button( __('Clear cache', 'funnelcockpit'), 'primary', 'clear_cache', false ); ?></p>
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

		// Verify nonce for security
		if ( ! isset( $_POST['funnelcockpit_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['funnelcockpit_meta_nonce'] ) ), 'funnelcockpit_save_meta' ) ) {
			return $post->ID;
		}

		if (isset($_POST['clear_cache']) && !empty($_POST['funnelpage_id'])) {
			$funnelpage_id = sanitize_text_field( wp_unslash( $_POST['funnelpage_id'] ) );
			delete_transient('funnelpage_' . $funnelpage_id . '_head');
			delete_transient('funnelpage_' . $funnelpage_id . '_body');
			delete_transient('funnelpage_' . $funnelpage_id . '_time');
		}

		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.

		$meta = array(
			'funnel_id' => !empty($_POST['funnel_id']) ? sanitize_text_field( wp_unslash( $_POST['funnel_id'] ) ) : '',
			'funnelpage_id' => !empty($_POST['funnelpage_id']) ? sanitize_text_field( wp_unslash( $_POST['funnelpage_id'] ) ) : '',
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

		$new_slug = !empty($_POST['post_name']) ? sanitize_text_field( wp_unslash( $_POST['post_name'] ) ) : '';

		if (!empty($_POST['post_title']) && empty($new_slug)) {
			$title = sanitize_text_field( wp_unslash( $_POST['post_title'] ) );
			$new_slug = sanitize_title( $title );
			if ( $post->post_name != $new_slug )
			{
				wp_update_post(
					array (
						'ID'        => $post->ID,
						'post_name' => $new_slug,
            'post_title' => sanitize_text_field( wp_unslash( $_POST['post_title'] ) )
					)
				);
			}
		}

        $apiKeyPrivate = get_option('funnelcockpit_apikey_private');
		if (!empty($apiKeyPrivate) && !empty($_POST['funnel_id']) && !empty($_POST['funnelpage_id'])) {
			$funnel_id = sanitize_text_field( wp_unslash( $_POST['funnel_id'] ) );
			$funnelpage_id = sanitize_text_field( wp_unslash( $_POST['funnelpage_id'] ) );
            $res = wp_remote_post( 'https://api.funnelcockpit.com/funnel/' . $funnel_id . '/page/' . $funnelpage_id, array(
                'headers' => array( 'Content-Type' => 'application/json; charset=utf-8', 'Authorization' => $apiKeyPrivate ),
                'body'        => json_encode(array( 'slug' => $new_slug )),
                'method'      => 'POST',
                'data_format' => 'body',
            ) );
        }
	}

	public function add_admin_menu() {
		add_submenu_page('edit.php?post_type=funnelpage', 'FunnelCockpit Settings', 'Settings', 'create_users', 'funnelcockpit-settings', array(&$this, 'funnelcockpit_options' ));
	}

	public function funnelcockpit_options() {
		$settings_updated = false;
		
		// Check for nonce and process form data
		if ( isset( $_POST['funnelcockpit_save_settings'] ) && isset( $_POST['funnelcockpit_options_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['funnelcockpit_options_nonce'] ) ), 'funnelcockpit_options_update' ) ) {
			// Save the settings
			if ( isset( $_POST['funnelcockpit_apikey_public'] ) ) {
				update_option( 'funnelcockpit_apikey_public', sanitize_text_field( wp_unslash( $_POST['funnelcockpit_apikey_public'] ) ) );
			}
			if ( isset( $_POST['funnelcockpit_apikey_private'] ) ) {
				update_option( 'funnelcockpit_apikey_private', sanitize_text_field( wp_unslash( $_POST['funnelcockpit_apikey_private'] ) ) );
			}
			if ( isset( $_POST['funnelcockpit_print_head'] ) ) {
				update_option( 'funnelcockpit_print_head', $this->sanitize_checkbox( sanitize_text_field( wp_unslash( $_POST['funnelcockpit_print_head'] ) ) ) );
			} else {
				update_option( 'funnelcockpit_print_head', '' );
			}

			$settings_updated = true;
		}
		
		// Get fresh values after potential save
		$apiKeyPublic = get_option('funnelcockpit_apikey_public');
		$apiKeyPrivate = get_option('funnelcockpit_apikey_private');
		$printHead = get_option('funnelcockpit_print_head');

		?>

		<div class="wrap">
			<img src="<?php echo esc_url(plugin_dir_url( __FILE__ ) . 'images/logo.png'); ?>" alt="FunnelCockpit" height="50">
			<p><?php 
				echo wp_kses(
					sprintf(
						/* translators: %s: FunnelCockpit link */
						__('You need to have a %s account with an already set up funnel to use this plugin.', 'funnelcockpit'), 
						'<a target="_blank" href="' . esc_url('https://funnelcockpit.com/') . '">FunnelCockpit</a>'
					),
					array(
						'a' => array(
							'href' => array(),
							'target' => array(),
						),
					)
				); 
			?></p>
			<form method="post" action="">
				<?php wp_nonce_field( 'funnelcockpit_options_update', 'funnelcockpit_options_nonce' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Public API Key', 'funnelcockpit'); ?></th>
						<td><input type="text" name="funnelcockpit_apikey_public" value="<?php echo esc_attr($apiKeyPublic); ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Private API Key', 'funnelcockpit'); ?></th>
						<td><input type="text" name="funnelcockpit_apikey_private" value="<?php echo esc_attr($apiKeyPrivate); ?>" class="regular-text" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">WordPress Header nicht entfernen<br /><small>(für erfahrene Nutzer)</small></th>
						<td><input type="checkbox" name="funnelcockpit_print_head" <?php echo $printHead == 'on' ? 'checked' : ''; ?> /></td>
					</tr>
				</table>

				<?php if ($settings_updated) { ?>
					<div class="notice notice-success is-dismissible">
						<p><?php esc_html_e('Settings saved.', 'funnelcockpit'); ?></p>
					</div>
				<?php } ?>

				<p class="submit"><input type="submit" name="funnelcockpit_save_settings" class="button-primary" value="<?php esc_attr_e('Save Settings', 'funnelcockpit') ?>" /></p>

			</form>
		</div>

		<?php
	}

}
