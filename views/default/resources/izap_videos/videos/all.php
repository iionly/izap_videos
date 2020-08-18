<?php

elgg_register_title_button('izap_videos', 'add', 'object', 'izap_videos');

elgg_push_collection_breadcrumbs('object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:all');

$offset = (int) elgg_extract('offset', $vars);
$limit = (int) elgg_extract('limit', $vars);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'distinct' => false,
	'full_view' => false,
	'list_type_toggle' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);

$params = [
	'filter_id' => 'izap_videos_tabs',
	'filter_value' => 'all',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
];

if (!elgg_is_logged_in()) {
	$params['filter_value'] = '';
	$params['filter'] = '';
}

$body = elgg_view_layout('default', $params);

echo elgg_view_page($title, $body);
