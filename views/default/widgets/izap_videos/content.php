<?php
/**
 * Display the latest videos of the user
 *
 */

$widget = elgg_extract('entity', $vars);

$limit = (int) $widget->num_display ?: 4;

$content = elgg_list_entities([
	'type' => 'object',
	'subtype' => IzapVideos::SUBTYPE,
	'container_guid' => $widget->owner_guid,
	'limit' => $limit,
	'pagination' => false,
	'distinct' => false,
]);

if (empty($content)) {
	echo elgg_echo('izap_videos:notfound');
	return;
}

echo $content;

$more_link = elgg_view('output/url', [
	'href' => elgg_generate_url('collection:object:izap_videos:owner', [
		'username' => $widget->getOwnerEntity()->username,
	]),
	'text' => elgg_echo('izap_videos:morevideos'),
	'is_trusted' => true,
]);
echo "<div class=\"elgg-widget-more\">$more_link</div>";
