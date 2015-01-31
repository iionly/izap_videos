<?php

/**
 * Most viewed videos this month
 *
 */

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb(elgg_echo('izap_videos:mostviewedthismonth'));

$offset = (int)get_input('offset', 0);
$limit = (int)get_input('limit', 10);

$start = mktime(0,0,0, date("m"), 1, date("Y"));
$end = time();

$db_prefix = elgg_get_config('dbprefix');
$options = array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => $limit,
	'offset' => $offset,
	'order_by_metadata' =>  array('name' => 'views', 'direction' => DESC, 'as' => integer),
	'joins' => array("JOIN {$db_prefix}metadata mdi ON mdi.entity_guid = e.guid"),
	'wheres' => array("mdi.time_created BETWEEN {$start} AND {$end}"),
	'full_view' => false,
);
$options['metadata_name_value_pairs'] = array(array('name' => 'views', 'value' => 0,  'operand' => '>'));
$result = elgg_list_entities_from_metadata($options);

$title = elgg_echo('izap_videos:mostviewedthismonth');

elgg_register_title_button('videos');

if (!empty($result)) {
	$area2 = $result;
} else {
	$area2 = elgg_echo('izap_videos:mostviewedthismonth:nosuccess');
}
$body = elgg_view_layout('content', array(
	'filter_override' => '',
	'content' => $area2,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'all')),
));

// Draw it
echo elgg_view_page(elgg_echo('izap_videos:mostviewedthismonth'), $body);
