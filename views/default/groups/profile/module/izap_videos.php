<?php
/**
 * Group videos module
 */

$group = elgg_get_page_owner_entity();

if ($group->izap_videos_enable == "no") {
	return true;
}

$all_link = elgg_view('output/url', [
	'href' => "videos/group/{$group->guid}",
	'text' => elgg_echo('link:view:all'),
	'is_trusted' => true,
]);

elgg_push_context('widgets');
$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'container_guid' => elgg_get_page_owner_guid(),
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
	'no_results' => elgg_echo('izap_videos:notfound'),
]);
elgg_pop_context();

$new_link = elgg_view('output/url', [
	'href' => "videos/add/$group->guid",
	'text' => elgg_echo('videos:add'),
	'is_trusted' => true,
]);

echo elgg_view('groups/profile/module', [
	'title' => elgg_echo('izap_videos:groupvideos'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
]);
