<?php
/**
 * Groups page Latest Videos widget for Widget Manager plugin
 *
 */

// get widget settings
/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$limit = (int) $widget->latest_videos_count;
if ($limit < 1) {
	$limit = 4;
}

$container_guid = elgg_get_page_owner_guid();

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
]);
elgg_pop_context();

if (elgg_is_logged_in()) {
	$group = get_entity($container_guid);
	if ($group->isMember(elgg_get_logged_in_user_entity())) {
		echo elgg_view('output/url', [
			'href' => "videos/add/" . $container_guid,
			'text' => elgg_echo('izap_videos:add'),
			'is_trusted' => true,
		]);
	}
}
