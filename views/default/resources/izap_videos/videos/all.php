<?php

elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');

$offset = (int)get_input('offset', 0);
$limit = (int)get_input('limit', 10);

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => $limit,
	'offset' => $offset,
	'full_view' => false,
	'list_type_toggle' => false,
));
if (!$content) {
	$content = elgg_echo('izap_videos:notfound');
}

$title = elgg_echo('videos');

elgg_register_title_button('videos');

$body = elgg_view_layout('content', array(
	'filter_context' => 'all',
	'filter_override' => elgg_view('izap_videos/nav', array('selected' => 'all')),
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'all')),
));

echo elgg_view_page($title, $body);
