<?php

// get the video id
$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', 'izap_videos');

$izap_videos = get_entity($guid);

elgg_push_entity_breadcrumbs($izap_videos, false);

$title = $izap_videos->title;

if (elgg_is_logged_in()) {
	elgg_register_title_button('add', 'object', 'izap_videos');
}

$content = elgg_view_entity($izap_videos, ['full_view' => true]);

$body = elgg_view_layout('default', [
	'content' => $content,
	'entity' => $izap_videos,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'play']),
]);

echo elgg_view_page($title, $body);
