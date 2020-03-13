<?php

global $wp_query;
$postid = $wp_query->post->ID;
$funnelPageId = get_post_meta($postid, 'funnelpage_id', true);
wp_reset_query();


$body = get_transient( 'funnelpage_' . $funnelPageId . '_body');
$head = get_transient( 'funnelpage_' . $funnelPageId . '_head');
$splitTestsEnabled = get_transient( 'funnelpage_' . $funnelPageId . '_splitTestsEnabled');

if ($splitTestsEnabled === true) {
	$cookies = array();
	foreach ($_COOKIE as $name => $value) {
		if (strpos($name, 'funnelPage') !== false) {
			$cookies[] = new WP_Http_Cookie( array( 'name' => $name, 'value' => $value ) );
		}
	}

	$response = wp_remote_get('https://page.funnelcockpit.com/' . $funnelPageId, [
		'timeout' => 10,
		'cookies' => $cookies
	]);
	if ($response['response']['code'] == 200 && isset($response['body'])) {
		foreach ($response['cookies'] as $cookie) {
			if (strpos($cookie->name, 'funnelPage') !== false) {
				setcookie($cookie->name, $cookie->value, time() + (60 * 60 * 24 * 30));
			}
		}

		echo $response['body'];
		die();
	}
}

?>
<!DOCTYPE html>
<html>
<?php

if ($head !== false) {
    echo $head;
}

if (get_option('funnelcockpit_print_head') == 'on') {
	wp_head();
}

?>

<?php
if ($body !== false) {
    echo str_replace('</body>', '', $body);
} else {
    echo '<body>';
}

wp_footer();

?>
</body>
</html>
