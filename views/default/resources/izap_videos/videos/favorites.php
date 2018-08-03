<?php

elgg_gatekeeper();

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
	forward('', '404');
}

$title = elgg_echo('izap_videos:user_favorites', [$owner->name]);

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name, "videos/favorites/$owner->username");
elgg_push_breadcrumb(elgg_echo('izap_videos:favorites'));

$offset = (int) get_input('offset', 0);
$limit = (int) get_input('limit', 10);

$result = elgg_list_entities_from_metadata([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'metadata_name' => 'favorited_by',
	'metadata_value' => $owner->guid,
	'limit' =>  $limit,
	'offset' => $offset,
	'full_view' => false,
	'list_type_toggle' => false,
	'no_results' => elgg_echo('izap_videos:no_favorites'),
]);

elgg_register_title_button('videos');

$body = elgg_view_layout('content', [
	'filter_context' => 'favorites',
	'filter_override' => elgg_view('izap_videos/nav', ['selected' => 'favorites']),
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'favorites']),
]);

echo elgg_view_page($title, $body);
