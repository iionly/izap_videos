<?php
/**
 * Video added river view
 */

elgg_require_js('izap_videos/izapvidjs');

$item = elgg_extract('item', $vars);
if (!$item instanceof ElggRiverItem) {
	return;
}

$video = $item->getObjectEntity();
if (!$video instanceof IzapVideos) {
	return;
}

$excerpt = strip_tags($video->description);
$excerpt = elgg_get_excerpt($excerpt);
$vars['message'] = $excerpt;

$size = izapAdminSettings_izap_videos('izap_river_thumbnails');
if ($size != 'none') {
	$attachments = elgg_view_entity_icon($video, $size, [
		'href' => "ajax/view/izap_videos/playpopup?guid={$video->getGUID()}",
		'title' => $video->title,
		'img_class' => 'elgg-photo izap-photo',
		'link_class' => 'izapvid-river-lightbox',
	]);
$vars['attachments'] = $attachments;
}

echo elgg_view('river/elements/layout', $vars);
