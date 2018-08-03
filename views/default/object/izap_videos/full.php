<?php

elgg_load_css('izap_videos_videojs_css');
elgg_require_js('izap_videos/videojs');

$video = elgg_extract('entity', $vars, false);

if (!($video instanceof IzapVideos)) {
	return true;
}

$video->updateViews();

$owner_link = elgg_view('output/url', [
	'href' => "videos/owner/" . $video->getOwnerEntity()->username,
	'text' => $video->getOwnerEntity()->name,
]);
$author_text = elgg_echo('byline', [$owner_link]);
$date = elgg_view_friendly_time($video->time_created);
$categories = elgg_view('output/categories', $vars);

$comments_count = $video->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', [
		'href' => $video->getURL() . '#comments',
		'text' => $text,
		'is_trusted' => true,
	]);
} else {
	$comments_link = '';
}

$owner_icon = elgg_view_entity_icon($video->getOwnerEntity(), 'tiny');

$metadata = elgg_view_menu('entity', [
	'entity' => $video,
	'handler' => 'videos',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
]);

$subtitle = "$author_text $date $comments_link $categories";

$params = [
	'entity' => $video,
	'title' => false,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
];
$list_body = elgg_view('object/elements/summary', $params);

$summary = elgg_view_image_block($owner_icon, $list_body, ['class' => 'mbl']);

echo $summary;

// Display the video player to allow for the video to be played
echo elgg_format_element('div', ['align' => 'center'], $video->getPlayer());

if ($video->description) {
	echo elgg_view('output/longtext', [
		'value' => $video->description,
		'class' => 'mbl',
	]);
}

// Optional view for other plugins to extend (not used by izap_videos itself)
echo elgg_view('izap_videos/extendedPlay', $vars);

if ($video->converted == 'yes') {
	echo elgg_view_comments($video);
}
