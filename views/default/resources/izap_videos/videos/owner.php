<?php

$username = elgg_extract('username', $vars);

$owner = get_user_by_username($username);
if (!$owner) {
	$owner = elgg_get_logged_in_user_entity();
}
if (!$owner) {
	throw new \Elgg\EntityNotFoundException();
}

elgg_register_title_button('videos', 'add', 'object', 'izap_videos');

elgg_push_collection_breadcrumbs('object', 'izap_videos', $owner);

if ($owner->guid === elgg_get_logged_in_user_guid()) {
	$title = elgg_echo('collection:object:izap_videos');
} else {
	$title = elgg_echo('collection:object:izap_videos:owner', [$owner->getDisplayName()]);
}

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'container_guid' => $owner->getGUID(),
	'full_view' => false,
	'list_type_toggle' => false,
	'preload_owners' => false,
	'distinct' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);

$params = [
	'filter_id' => 'izap_videos_tabs',
	'filter_value' => 'mine',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'owner']),
];

if ($owner->guid != elgg_get_logged_in_user_guid()) {
	$params['filter_value'] = '';
	$params['filter'] = '';
}

$body = elgg_view_layout('default', $params);

echo elgg_view_page($title, $body);
