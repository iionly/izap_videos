<?php

gatekeeper();

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

if (!$owner) {
	forward(REFERER);
}

elgg_push_breadcrumb(elgg_echo('videos'), 'videos/all');
elgg_push_breadcrumb($owner->name, "videos/friends/$owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

$title = elgg_echo('izap_videos:frnd');

$offset = (int)get_input('offset', 0);
$limit = (int)get_input('limit', 10);

if ($friends = $owner->getFriends(array('limit' => false))) {
	$friendguids = array();
	foreach ($friends as $friend) {
		$friendguids[] = $friend->getGUID();
	}
	$result = elgg_list_entities(array(
		'type' => 'object',
		'subtype' => 'izap_videos',
		'owner_guids' => $friendguids,
		'limit' => $limit,
		'offset' => $offset,
		'full_view' => false,
		'pagination' => true,
		'list_type_toggle' => false,
	));

	if (!empty($result)) {
		$area2 = $result;
	} else {
		$area2 = elgg_echo('izap_videos:notfound');
	}
} else {
	$area2 = elgg_echo("friends:none:you");
}

elgg_register_title_button('videos');

$body = elgg_view_layout('content', array(
	'filter_context' => 'friends',
	'filter_override' => elgg_view('izap_videos/nav', array('selected' => 'friends')),
	'content' => $area2,
	'title' => $title,
	'sidebar' => elgg_view('izap_videos/sidebar', array('page' => 'friends')),
));

echo elgg_view_page($title, $body);
