<?php

elgg_gatekeeper();

$owner = elgg_get_page_owner_entity();

$owner_link = '';
if ($owner instanceof ElggUser) {
	$owner_link = "videos/owner/$owner->username";
} else if ($owner instanceof ElggGroup) {
	$owner_link = "videos/group/$owner->guid";
} else {
	forward('', '404');
}

$title = elgg_echo('izap_videos:add');

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name, $owner_link);
elgg_push_breadcrumb($title);

$content = elgg_view('izap_videos/addedit_video');

$body = elgg_view_layout('content', [
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'add']),
]);

echo elgg_view_page($title, $body);
