<?php

elgg_require_css('izap_videos_videojs/video-js.min');
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

$owner_icon = elgg_view_entity_icon($video->getOwnerEntity(), 'tiny');
$title = elgg_view_title($video->title);

$params = [
	'entity' => $video,
	'title' => false,
	'metadata' => '',
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