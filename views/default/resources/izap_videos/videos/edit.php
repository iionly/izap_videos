<?php

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'object', 'izap_videos');

$video = get_entity($guid);

if (!($video instanceof IzapVideos)) {
	// @todo either deleted or do not have access
	forward('videos/all');
}

if (!$video->canEdit()) {
	// @todo cannot change it
	forward('videos/all');
}

elgg_set_page_owner_guid($video->getContainerGUID());
$owner = elgg_get_page_owner_entity();

$owner_link = '';
if ($owner instanceof ElggUser) {
	$owner_link = "videos/owner/$owner->username";
} else if ($owner instanceof ElggGroup) {
	$owner_link = "videos/group/$owner->guid";
} else {
	forward('', '404');
}

$title = $video->title;

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name, $owner_link);
elgg_push_breadcrumb($title, $video->getURL());
elgg_push_breadcrumb(elgg_echo('izap_videos:editVideo'));

$content = elgg_view('izap_videos/addedit_video', ['entity' => $video]);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'edit']),
]);

echo elgg_view_page($title, $body);
