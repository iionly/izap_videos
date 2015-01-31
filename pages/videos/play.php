<?php

// get the video id as input
$video = (int)get_input('guid');
$izap_videos = izapVideoCheck_izap_videos($video);

elgg_set_page_owner_guid($izap_videos->getContainerGUID());
$owner = elgg_get_page_owner_entity();

$title = $izap_videos->title;

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
if (elgg_instanceof($owner, 'user')) {
	elgg_push_breadcrumb($owner->name, "videos/owner/$owner->username");
} else {
	elgg_push_breadcrumb($owner->name, "videos/group/$owner->guid");
}
elgg_push_breadcrumb($title);

if (elgg_is_logged_in()) {
	elgg_register_menu_item('title', array(
		'name' => 'add',
		'href' => 'videos/add/' . elgg_get_logged_in_user_guid(),
		'text' => elgg_echo("videos:add"),
		'link_class' => 'elgg-button elgg-button-action',
	));
}

$content = elgg_view_entity($izap_videos, array('full_view' => true));

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'play')),
));

echo elgg_view_page($title, $body);
