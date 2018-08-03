<?php
/**
 * Index page Latest Videos widget for Widget Manager plugin
 *
 */

// get widget settings
/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$limit = (int) $widget->latest_videos_count;
if ($limit < 1) {
	$limit = 4;
}

elgg_push_context('front');
echo elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'full_view' => false,
	'list_type_toggle' => false,
	'pagination' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);
elgg_pop_context();
