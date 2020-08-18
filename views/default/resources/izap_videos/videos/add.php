<?php

$guid = elgg_extract('guid', $vars);
if (!$guid) {
	$guid = elgg_get_logged_in_user_guid();
}

elgg_entity_gatekeeper($guid);

$container = get_entity($guid);

if (!$container->canWriteToContainer(0, 'object', 'izap_videos')) {
	throw new \Elgg\EntityPermissionsException();
}

elgg_push_collection_breadcrumbs('object', 'izap_videos', $container);
elgg_push_breadcrumb(elgg_echo('add:object:izap_videos'));

$title = elgg_echo('add:object:izap_videos');

$content = elgg_view('izap_videos/addedit_video');

$body = elgg_view_layout('default', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'add']),
]);

echo elgg_view_page($title, $body);
