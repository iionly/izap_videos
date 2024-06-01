<?php

elgg_require_css('izap_videos_videojs/video-js.min');
elgg_require_js('izap_videos/videojs');

$video = elgg_extract('entity', $vars, false);

if (!($video instanceof \IzapVideos)) {
	return true;
}

$video->updateViews();

$owner_icon = elgg_view_entity_icon($video->getOwnerEntity(), 'tiny');

$params = [
	'entity' => $video,
	'title' => false,
];
$list_body = elgg_view('object/elements/summary', $params);

$summary = elgg_view_image_block($owner_icon, $list_body, ['class' => 'mbl']);

echo $summary;

// Display the video player to allow for the video to be played
echo elgg_format_element('div', ['class' => 'izap_responsive_video'], $video->getPlayer());

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
