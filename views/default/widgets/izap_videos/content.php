<?php
/**
 * Display the latest videos of the user
 *
 */

$owner = elgg_get_page_owner_entity();

$num = ($vars['entity']->num_display) ? $vars['entity']->num_display : 4;

$content = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => $num,
	'owner_guid' => $owner->guid,
	'full_view' => false,
	'list_type_toggle' => false,
	'pagination' => false,
));

echo $content;

if ($content) {
	$more_link = elgg_view('output/url', array(
		'href' => "/videos/owner/" . $owner->username,
		'text' => elgg_echo('link:view:all'),
		'is_trusted' => true,
	));
	echo "<span class=\"elgg-widget-more\">$more_link</span>";
} else {
	echo elgg_echo('izap_videos:notfound');
}
