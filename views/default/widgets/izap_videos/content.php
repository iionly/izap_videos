<?php
/**
 * Display the latest videos of the user
 *
 */

// get widget settings
/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$limit = (int) $widget->num_display;
if ($limit < 1) {
	$limit = 4;
}

$owner = elgg_get_page_owner_entity();

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'limit' => $limit,
	'owner_guid' => $owner->guid,
	'full_view' => false,
	'list_type_toggle' => false,
	'pagination' => false,
]);

if (!$content) {
	echo elgg_echo('izap_videos:notfound');
} else {
	echo $content;

	$more_link = elgg_view('output/url', [
		'href' => "/videos/owner/" . $owner->username,
		'text' => elgg_echo('link:view:all'),
		'is_trusted' => true,
	]);
	echo elgg_format_element('span', ['class' => 'elgg-widget-more'], $more_link);
}
