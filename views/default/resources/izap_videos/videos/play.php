<?php

// get the video id as input
$guid = (int) elgg_extract('guid', $vars);

$izap_videos = get_entity($guid);
if (!($izap_videos instanceof IzapVideos)) {
	forward('', '404');
}

elgg_set_page_owner_guid($izap_videos->getContainerGUID());
$owner = elgg_get_page_owner_entity();

$owner_link = '';
if ($owner instanceof ElggUser) {
	$owner_link = "videos/owner/$owner->username";
} else if ($owner instanceof ElggGroup) {
	$owner_link = "videos/group/$owner->guid";
} else {
	forward('', '404');
}

$title = $izap_videos->title;

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name, $owner_link);
elgg_push_breadcrumb($title);

if (elgg_is_logged_in()) {
	elgg_register_menu_item('title', [
		'name' => 'add',
		'href' => 'videos/add/' . elgg_get_logged_in_user_guid(),
		'text' => elgg_echo("videos:add"),
		'link_class' => 'elgg-button elgg-button-action',
	]);
}

$content = elgg_view_entity($izap_videos, ['full_view' => true]);

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'play']),
]);

echo elgg_view_page($title, $body);
