<?php

/**
 * Most viewed videos last month
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:mostviewedlastmonth');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$offset = (int) elgg_extract('offset', $vars);
$limit = (int) elgg_extract('limit', $vars);

$start = strtotime("-1 months", mktime(0, 0, 0, date("m"), 1, date("Y")));
$end = mktime(0, 0, 0, date("m"), 0, date("Y"));

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
			'value' => $start,
			'operand' => '>=',
		],
		[
			'name' => 'last_viewed',
			'value' => $end,
			'operand' => '<=',
		],
	],
	'order_by_metadata' => [
		'name' => 'views',
		'direction' => 'DESC',
		'as' => 'integer',
	],
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:mostviewedlastmonth:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
