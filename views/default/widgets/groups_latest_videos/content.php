<?php
/**
 * Groups page Latest Videos widget for Widget Manager plugin
 *
 */

// get widget settings
$count = sanitise_int($vars["entity"]->latest_videos_count, false);
if(empty($count)){
	$count = 4;
}

$prev_context = elgg_get_context();
elgg_set_context('groups');
$videos_html = elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'container_guid' => elgg_get_page_owner_guid(),
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
));

elgg_set_context($prev_context);

if (elgg_is_logged_in()) {
	$group = get_entity(elgg_get_page_owner_guid());
	if ($group->isMember(elgg_get_logged_in_user_entity())) {
		$videos_html .= elgg_view('output/url', array(
			'href' => "videos/add/" . elgg_get_page_owner_guid(),
			'text' => elgg_echo('izap_videos:add'),
			'is_trusted' => true,
		));
	}
}

echo $videos_html;
