<?php

/**
 * Most recently voted videos - world view only
 *
 */

elgg_register_title_button('add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:recentlyvoted');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => \IzapVideos::SUBTYPE,
	'annotation_name' => 'fivestar',
	'order_by' => [
		new \Elgg\Database\Clauses\OrderByClause('n_table.time_created', 'DESC'),
	],
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:recentlyvoted:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
