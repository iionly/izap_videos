<?php

$video_guid = get_input('guid', false);

if (!$video_guid) {
	return true;
}

$video = get_entity($video_guid);

if (!elgg_instanceof($video, 'object', 'izap_videos')) {
	return true;
}

$video->updateViews();

$owner_link = elgg_view('output/url', array(
	'href' => "videos/owner/" . $video->getOwnerEntity()->username,
	'text' => $video->getOwnerEntity()->name,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($video->time_created);
$categories = elgg_view('output/categories', $vars);

$comments_count = $video->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $video->getURL() . '#comments',
		'text' => $text,
		'is_trusted' => true,
	));
} else {
	$comments_link = '';
}

if (elgg_is_active_plugin('elggx_fivestar')) {
	$fivestar = elgg_view("elggx_fivestar/voting", array('entity' => $video, 'subclass' => 'mts mbs'));
} else {
	$fivestar = '';
}

$owner_icon = elgg_view_entity_icon($video->getOwnerEntity(), 'tiny');

$metadata = elgg_view_menu('entity', array(
	'entity' => $video,
	'handler' => 'videos',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date $comments_link $categories $fivestar";

$params = array(
	'entity' => $video,
	'title' => false,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'tags' => $tags,
);
$list_body = elgg_view('object/elements/summary', $params);

$params = array('class' => 'mbs');
$summary = elgg_view_image_block($owner_icon, $list_body, $params);

echo $summary;

// Display the video player to allow for the video to be played
echo '<div align="center" class="izapPlayer">';
echo '<div class="mbm">' . $video->getPlayer() . '</div>';
echo '<div class="mbm">' . elgg_view('output/url', array(
	'href' => $video->getURL() . '#comments',
	'text' => elgg_echo('generic_comments:add'),
	'is_trusted' => true,
	'class' => 'elgg-button elgg-button-action'
)) . '</div>';
echo '</div>';
