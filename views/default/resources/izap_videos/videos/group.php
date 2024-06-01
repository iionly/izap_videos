<?php

$group_guid = (int) elgg_extract('guid', $vars);

elgg_entity_gatekeeper($group_guid, 'group');

elgg_group_tool_gatekeeper('izap_videos', $group_guid);

$group = get_entity($group_guid);

elgg_register_title_button('videos', 'add', 'object', 'izap_videos');

elgg_push_collection_breadcrumbs('object', 'izap_videos', $group);

$title = elgg_echo('collection:object:izap_videos:group');

$result = elgg_list_entities([
	'type' => 'object',
	'subtype' => \IzapVideos::SUBTYPE,
	'container_guid' => (int) $group->guid,
	'full_view' => false,
	'list_type_toggle' => false,
	'preload_containers' => false,
	'distinct' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);

$params = [
	'filter' => '',
	'content' => $result,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', ['page' => 'owner']),
];

$body = elgg_view_layout('default', $params);

echo elgg_view_page($title, $body);
