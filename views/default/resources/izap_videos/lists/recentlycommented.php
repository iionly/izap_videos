<?php

/**
  * Videos recently commented on - world view only
 *
 */

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:recentlycommented');

elgg_push_collection_breadcrumbs('object', 'izap_videos');
elgg_push_breadcrumb($title);

$offset = (int) elgg_extract('offset', $vars);
$limit = (int) elgg_extract('limit', $vars);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'wheres' => function(\Elgg\Database\QueryBuilder $qb, $alias) {
		$qb->innerJoin($alias, 'entities', 'ce', "ce.container_guid = e.guid");
		$qb->orderBy('ce.time_created', 'DESC');
		return $qb->compare('ce.subtype', '=', 'comment', ELGG_VALUE_STRING);
	},
	'full_view' => false,
	'no_results' => elgg_echo('izap_videos:recentlycommented:nosuccess'),
]);

$body = elgg_view_layout('default', [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
]);

// Draw it
echo elgg_view_page($title, $body);
