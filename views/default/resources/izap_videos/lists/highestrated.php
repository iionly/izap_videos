<?php

/**
 * Highest rated videos - world view only
 *
 */

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb(elgg_echo('izap_videos:highestrated'));

$offset = (int)get_input('offset', 0);
$limit = (int)get_input('limit', 10);

$options = array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => $limit,
	'offset' => $offset,
	'annotation_name' => 'fivestar',
	'calculation' => 'avg',
	'order_by' => 'annotation_calculation desc',
	'full_view' => false,
);

$result = elgg_list_entities_from_annotation_calculation($options);

$title = elgg_echo('izap_videos:highestrated');

elgg_register_title_button('videos');

if (!empty($result)) {
	$area2 = $result;
} else {
	$area2 = elgg_echo('izap_videos:highestrated:nosuccess');
}
$body = elgg_view_layout('content', array(
	'filter_override' => '',
	'content' => $area2,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'all')),
));

// Draw it
echo elgg_view_page(elgg_echo('izap_videos:highestrated'), $body);
