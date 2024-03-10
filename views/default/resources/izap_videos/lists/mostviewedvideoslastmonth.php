<?php

/**
 * Most viewed videos last month
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:mostviewedlastmonth');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$start = strtotime("-1 months", mktime(0, 0, 0, date("m"), 1, date("Y")));
$end = mktime(0, 0, 0, date("m"), 0, date("Y"));

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
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
	'sort_by' => [
		'property' => 'views',
		'direction' => 'DESC',
		'signed' => true,
		'property_type' => 'metadata',
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
