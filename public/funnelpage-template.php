<?php

global $wp_query;
$postid = $wp_query->post->ID;
$funnelPageId = get_post_meta($postid, 'funnelpage_id', true);
wp_reset_query();


$body = get_transient( 'funnelpage_' . $funnelPageId . '_body');
$head = get_transient( 'funnelpage_' . $funnelPageId . '_head');

?>
<!DOCTYPE html>
<html>
<?php

if ($head !== false) {
    echo $head;
} else {
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
