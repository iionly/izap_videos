<?php

gatekeeper();

$owner = elgg_get_page_owner_entity();

if (!$owner) {
	$guid = get_input('guid');
	$owner = get_user($guid);
}

if (!$owner) {
	$username = get_input('username');
	$owner = get_user_by_username($username);
}

if (!$owner) {
	$owner = elgg_get_logged_in_user_entity();
}

if (!$owner) {
	forward(REFERER);
}

$title = elgg_echo('izap_videos:user_favorites', array($owner->name));

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name, "videos/favorites/$owner->username");
elgg_push_breadcrumb(elgg_echo('izap_videos:favorites'));

$offset = (int)get_input('offset', 0);
$limit = (int)get_input('limit', 10);

$content = elgg_list_entities_from_metadata(array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'metadata_name' => 'favorited_by',
	'metadata_value' => $owner->guid,
	'limit' =>  $limit,
	'offset' => $offset,
	'full_view' => false,
	'list_type_toggle' => false,
));

if (!$content) {
	$content = elgg_echo('izap_videos:no_favorites');
}

elgg_register_title_button('videos');

$params = array(
	'filter_context' => 'favorites',
	'filter_override' => elgg_view('izap_videos/nav', array('selected' => 'favorites')),
	'content' => $content,
	'title' => $title,
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
