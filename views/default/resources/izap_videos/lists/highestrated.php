<?php

/**
 * Highest rated videos - world view only
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:highestrated');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'annotation_name' => 'fivestar',
	'annotation_sort_by_calculation' => 'avg',
	'order_by' => [
		new \Elgg\Database\Clauses\OrderByClause('annotation_calculation', 'DESC'),
	],
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:highestrated:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
