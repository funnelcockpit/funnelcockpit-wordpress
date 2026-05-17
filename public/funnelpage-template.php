<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;
$postid = $wp_query->post->ID;
$funnelPageId = get_post_meta($postid, 'funnelpage_id', true);
wp_reset_postdata();


$body = get_transient( 'funnelpage_' . $funnelPageId . '_body');
$head = get_transient( 'funnelpage_' . $funnelPageId . '_head');
$splitTestsEnabled = get_transient( 'funnelpage_' . $funnelPageId . '_splitTestsEnabled');

if ($splitTestsEnabled) {
	$cookies = array();
	foreach ($_COOKIE as $name => $value) {
		if (strpos($name, 'funnelPage') !== false) {
			$cookies[] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
		}
	}

	$response = wp_remote_get('https://api.funnelcockpit.com/funnel-page/' . $funnelPageId, [
		'timeout' => 10,
		'cookies' => $cookies
	]);
	if ($response['response']['code'] == 200 && isset($response['body'])) {
		$funnelPage = json_decode($response['body']);
		if (!empty($funnelPage) && isset($funnelPage->head) && isset($funnelPage->body)) {

			foreach ($response['cookies'] as $cookie) {
				if (strpos($cookie->name, 'funnelPage') !== false) {
					setcookie($cookie->name, $cookie->value, time() + (60 * 60 * 24 * 30));
				}
			}

			// Output trusted HTML content from FunnelCockpit service
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '<!DOCTYPE html><html>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $funnelPage->head;
			if (get_option('funnelcockpit_print_head') == 'on') {
				wp_head();
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo str_replace('</body>', '', $funnelPage->body);
			wp_footer();
			echo '</body></html>';
			die();
		}
	}
}

?>
<!DOCTYPE html>
<html>
<?php

if ($head !== false) {
    // Output trusted HTML head content from FunnelCockpit service
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $head;
}

if (get_option('funnelcockpit_print_head') == 'on') {
	wp_head();
}

?>

<?php
if ($body !== false) {
    // Output trusted HTML body content from FunnelCockpit service
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo str_replace('</body>', '', $body);
} else {
    echo '<body>';
}

wp_footer();

?>
</body>
</html>
