<?php

/**
 * Recently viewed videos
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:recentlyviewed');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$offset = (int) elgg_extract('offset', $vars);
$limit = (int) elgg_extract('limit', $vars);

$result = elgg_list_entities([
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
		[
			'name' => 'last_viewed',
			'value' => 0,
			'operand' => '>',
		],
		
	],
	'order_by_metadata' => [
		'name' => 'last_viewed',
		'direction' => 'DESC',
		'as' => 'integer',
	],
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:recentlyviewed:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
