<?php
/**
 * Index page Latest Videos widget for Widget Manager plugin
 *
 */

// get widget settings
$count = sanitise_int($vars["entity"]->latest_videos_count, false);
if(empty($count)){
	$count = 4;
}

$prev_context = elgg_get_context();
elgg_set_context('front');
$videos_html = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => $count,
	'full_view' => false,
	'list_type_toggle' => false,
	'pagination' => false,
));
elgg_set_context($prev_context);
echo $videos_html;
