<?php
/**
 * Groups page Latest Videos widget for Widget Manager plugin
 *
 */

$widget = elgg_extract('entity', $vars);

$limit = (int) $widget->latest_videos_count ?: 4;

$container_guid = elgg_get_page_owner_guid();
$group = get_entity($container_guid);

elgg_push_context('groups');
echo elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'container_guid' => $container_guid,
	'limit' => $limit,
	'full_view' => false,
	'list_type_toggle' => false,
	'pagination' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
	'distinct' => false,
]);
elgg_pop_context();

if ($group->canWriteToContainer(0, 'object', 'izap_videos')) {
	echo elgg_view('output/url', [
		'href' => elgg_generate_url('add:object:izap_videos', [
			'guid' => $group->guid,
		]),
		'text' => elgg_echo('izap_videos:add'),
		'is_trusted' => true,
	]);
}
