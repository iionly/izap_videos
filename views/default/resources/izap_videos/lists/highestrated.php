<?php

/**
 * Highest rated videos - world view only
 *
 */
if (!elgg_is_active_plugin('elggx_fivestar')) {
	return;
}

$title = elgg_echo('izap_videos:highestrated');

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($title);

$offset = (int) get_input('offset', 0);
$limit = (int) get_input('limit', 10);

$result = elgg_list_entities_from_annotation_calculation([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'annotation_name' => 'fivestar',
	'calculation' => 'avg',
	'order_by' => 'annotation_calculation desc',
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:highestrated:nosuccess'),
]);

elgg_register_title_button('videos');

$body = elgg_view_layout('content', [
	'filter_override' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
