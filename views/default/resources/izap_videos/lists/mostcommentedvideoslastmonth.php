<?php

/**
 * Most commented videos last month
 *
 */

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb(elgg_echo('izap_videos:mostcommentedlastmonth'));

$offset = (int)get_input('offset', 0);
$limit = (int)get_input('limit', 10);

$start = strtotime("-1 months", mktime(0,0,0, date("m"), 1, date("Y")));
$end = mktime(0,0,0,date("m"), 0, date("Y"));

$db_prefix = elgg_get_config('dbprefix');
$options = array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => $limit,
	'offset' => $offset,
	'selects' => array("count( * ) AS views"),
	'joins' => array(
		"JOIN {$db_prefix}entities ce ON ce.container_guid = e.guid",
		"JOIN {$db_prefix}entity_subtypes cs ON ce.subtype = cs.id AND cs.subtype = 'comment'"),
	'wheres' => array("ce.time_created BETWEEN {$start} AND {$end}"),
	'group_by' => 'e.guid',
	'order_by' => "views DESC",
	'full_view' => false,
);

$result = elgg_list_entities($options);

$title = elgg_echo('izap_videos:mostcommentedlastmonth');

elgg_register_title_button('videos');

if (!empty($result)) {
	$area2 = $result;
} else {
	$area2 = elgg_echo('izap_videos:mostcommentedlastmonth:nosuccess');
}
$body = elgg_view_layout('content', array(
	'filter_override' => '',
	'content' => $area2,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'all')),
));

// Draw it
echo elgg_view_page(elgg_echo('izap_videos:mostcommentedlastmonth'), $body);
