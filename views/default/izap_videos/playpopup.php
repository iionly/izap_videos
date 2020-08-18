<?php

elgg_load_css('izap_videos_videojs_css');
elgg_require_js('izap_videos/videojs');

$video_guid = get_input('guid', false);

if (!$video_guid) {
	return true;
}

$video = get_entity($video_guid);

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
$subtitle = "$author_text $date $comments_link";
$title = elgg_view_title($video->title);

$params = [
	'entity' => $video,
	'title' => false,
	'metadata' => '',
	'subtitle' => $subtitle,
];
$list_body = elgg_view('object/elements/summary', $params);

$summary = elgg_view_image_block($owner_icon, $list_body, $params);

$content = elgg_format_element('div', ['style' => 'word-wrap:break-word;'], $title);
$content .= elgg_format_element('div', ['class' => ''], $summary);
$content .= elgg_format_element('div', ['class' => 'izapPlayer'], elgg_format_element('div', ['class' => 'izap_responsive_video'], $video->getPlayer()));
$content .= elgg_format_element('div', ['align' => 'center', 'class' => 'mts mbs'], elgg_view('output/url', [
	'href' => $video->getURL() . '#comments',
	'text' => elgg_echo('generic_comments:add'),
	'is_trusted' => true,
	'class' => 'elgg-button elgg-button-action',
]));
$content = elgg_format_element('div', ['class' => 'izapPopup'], $content);
echo $content;