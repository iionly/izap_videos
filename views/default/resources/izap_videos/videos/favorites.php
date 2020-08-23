<?php

$owner = elgg_get_page_owner_entity();

if (!$owner) {
	$guid = elgg_extract('guid', $vars);
	$owner = get_user($guid);
}

if (!$owner) {
	$username = elgg_extract('username', $vars);
	$owner = get_user_by_username($username);
}

if (!$owner) {
	$owner = elgg_get_logged_in_user_entity();
}

if (!($owner instanceof ElggUser)) {
	throw new \Elgg\EntityNotFoundException();
}

elgg_push_collection_breadcrumbs('object', 'izap_videos', $owner);
elgg_push_breadcrumb(elgg_echo('izap_videos:favorites_short'));

elgg_register_title_button('videos', 'add', 'object', 'izap_videos');

$title = elgg_echo('collection:object:izap_videos:favorites', [$owner->name]);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'metadata_name' => 'favorited_by',
	'metadata_value' => $owner->guid,
	'distinct' => false,
	'full_view' => false,
	'list_type_toggle' => false,
	'no_results' => elgg_echo('izap_videos:no_favorites'),
]);

$body = elgg_view_layout('default', [
	'filter_id' => 'izap_videos_tabs',
	'filter_value' => 'favorites',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'favorites']),
]);

echo elgg_view_page($title, $body);
