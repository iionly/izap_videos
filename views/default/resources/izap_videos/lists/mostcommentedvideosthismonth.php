<?php

/**
 * Most commented videos this month
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:mostcommentedthismonth');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$offset = (int) elgg_extract('offset', $vars);
$limit = (int) elgg_extract('limit', $vars);

$start = mktime(0, 0, 0, date("m"), 1, date("Y"));
$end = time();

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'wheres' => function(\Elgg\Database\QueryBuilder $qb, $alias) use($start, $end) {
		$qb->groupBy("$alias.guid");
		$qb->innerJoin($alias, 'entities', 'ce', "ce.container_guid = e.guid");
		$qb->addSelect("count( * ) as views");
		$qb->orderBy('views', 'DESC');
		return $qb->merge([
			$qb->compare('ce.subtype', '=', 'comment', ELGG_VALUE_STRING),
			$qb->between('ce.time_created', $start, $end, ELGG_VALUE_INTEGER),
		], 'AND');
	},
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:mostcommentedthismonth:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
