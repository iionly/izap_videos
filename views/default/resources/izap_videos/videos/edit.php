<?php

$guid = (int) elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', 'izap_videos');

$video = get_entity($guid);
if (!$video->canEdit()) {
	throw new \Elgg\Exceptions\Http\EntityPermissionsException();
}

elgg_push_entity_breadcrumbs($video);
elgg_push_breadcrumb(elgg_echo('edit'));

$title = elgg_echo('edit:object:izap_videos');

$content = elgg_view('izap_videos/addedit_video', ['entity' => $video]);

$body = elgg_view_layout('default', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'edit']),
]);

echo elgg_view_page($title, $body);
