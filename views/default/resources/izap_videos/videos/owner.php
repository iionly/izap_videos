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

$title = elgg_echo('izap_videos:user', [$owner->name]);

// set up breadcrumbs
elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name);

$offset = (int) get_input('offset', 0);
$limit = (int) get_input('limit', 10);

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'container_guid' => $owner->getGUID(),
	'limit' => $limit,
	'offset' => $offset,
	'full_view' => false,
	'list_type_toggle' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);

if (elgg_is_logged_in()) {
	elgg_register_menu_item('title', [
		'name' => 'add',
		'href' => 'videos/add/' . $owner->getGUID(),
		'text' => elgg_echo("videos:add"),
		'link_class' => 'elgg-button elgg-button-action',
	]);
}

$params = [
	'filter_context' => 'mine',
	'filter_override' => elgg_view('izap_videos/nav', ['selected' => 'mine']),
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'owner']),
];

// don't show filter if out of filter context
if ($owner instanceof ElggGroup) {
	$params['filter'] = false;
	$params['filter_override'] = '';
} else if ($owner->getGUID() != elgg_get_logged_in_user_guid()) {
	$params['filter_override'] = elgg_view('izap_videos/nav', ['selected' => '']);
	$params['filter_context'] = '';
}

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
