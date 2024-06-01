<?php
/**
 * iZAP Videos Group module
 */

$group = elgg_extract('entity', $vars);
if (!($group instanceof \ElggGroup)) {
	return;
}

if (!$group->isToolEnabled('izap_videos')) {
	return;
}

$all_link = elgg_view('output/url', [
	'href' => elgg_generate_url('collection:object:izap_videos:group', [
		'guid' => $group->guid,
	]),
	'text' => elgg_echo('link:view:all'),
	'is_trusted' => true,
]);

elgg_push_context('widgets');
$options = [
	'type' => 'object',
	'subtype' => \IzapVideos::SUBTYPE,
	'container_guid' => elgg_get_page_owner_guid(),
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
	'distinct' => false,
];
$content = elgg_list_entities($options);
elgg_pop_context();

$new_link = null;
if ($group->canWriteToContainer(0, 'object', 'izap_videos')) {
	$new_link = elgg_view('output/url', [
		'href' => elgg_generate_url('add:object:izap_videos', [
			'guid' => $group->guid,
		]),
		'text' => elgg_echo('izap_videos:add'),
		'is_trusted' => true,
	]);
}

echo elgg_view('groups/profile/module', [
	'title' => elgg_echo('collection:object:izap_videos:group'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
]);
