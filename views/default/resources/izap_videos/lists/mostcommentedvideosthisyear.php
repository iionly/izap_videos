<?php

/**
 * Most commented videos this year
 *
 */

$title = elgg_echo('izap_videos:mostcommentedthisyear');

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($title);

$offset = (int) get_input('offset', 0);
$limit = (int) get_input('limit', 10);

$start = mktime(0, 0, 0, 1, 1, date("Y"));
$end = time();

$db_prefix = elgg_get_config('dbprefix');
$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'selects' => ["count( * ) AS views"],
	'joins' => [
		"JOIN {$db_prefix}entities ce ON ce.container_guid = e.guid",
		"JOIN {$db_prefix}entity_subtypes cs ON ce.subtype = cs.id AND cs.subtype = 'comment'"
	],
	'wheres' => ["ce.time_created BETWEEN {$start} AND {$end}"],
	'group_by' => 'e.guid',
	'order_by' => "views DESC",
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:mostcommentedthisyear:nosuccess'),
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
