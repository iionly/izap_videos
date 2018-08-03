<?php

/**
 * Recently viewed videos
 *
 */

$title = elgg_echo('izap_videos:recentlyviewed');

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($title);

$offset = (int) get_input('offset', 0);
$limit = (int) get_input('limit', 10);

$result = elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'metadata_name_value_pairs' => [
		[
			'name' => 'views',
			'value' => 0,
			'operand' => '>',
		],
	],
	'order_by' => "n_table1.time_created DESC",
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:recentlyviewed:nosuccess'),
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
