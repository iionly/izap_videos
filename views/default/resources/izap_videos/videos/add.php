<?php

$owner = elgg_get_page_owner_entity();

gatekeeper();
group_gatekeeper();

$title = elgg_echo('izap_videos:add');

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
if (elgg_instanceof($owner, 'user')) {
	elgg_push_breadcrumb($owner->name, "videos/owner/$owner->username");
} else {
	elgg_push_breadcrumb($owner->name, "videos/group/$owner->guid");
}
elgg_push_breadcrumb($title);

$content = elgg_view('izap_videos/forms/_partial');

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'add')),
));

echo elgg_view_page($title, $body);
