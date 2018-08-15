<?php

elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');

$offset = (int) get_input('offset', 0);
$limit = (int) get_input('limit', 10);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'offset' => $offset,
	'full_view' => false,
	'list_type_toggle' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);

$title = elgg_echo('videos');

elgg_register_title_button('videos');

$params = [
	'filter_context' => 'all',
	'filter_override' => elgg_view('izap_videos/nav', ['selected' => 'all']),
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'all']),
];

if (!elgg_is_logged_in()) {
	$params['filter_context'] = '';
	$params['filter_override'] = '';
}

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
