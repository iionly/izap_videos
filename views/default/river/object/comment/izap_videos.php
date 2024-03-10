<?php
/**
 * Post comment on videos river view
 */

elgg_require_js('izap_videos/izapvidjs');

$item = elgg_extract('item', $vars);
if (!$item instanceof ElggRiverItem) {
	return;
}

$comment = $item->getObjectEntity();
if (!$comment instanceof ElggComment) {
	return;
}

$video = $item->getTargetEntity();
if (!$video instanceof IzapVideos) {
	return;
}

$subject = $item->getSubjectEntity();
if (!$subject instanceof ElggUser) {
	return;
}

$vars['message'] = elgg_get_excerpt($comment->description);

$subject_link = elgg_view('output/url', [
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
]);

$target_link = elgg_view('output/url', [
	'href' => $video->getURL(),
	'text' => $video->title,
	'class' => 'elgg-river-target',
	'is_trusted' => true,
]);

$vars['summary'] = elgg_echo('river:object:izap_videos:comment', [$subject_link, $target_link]);

$size = \IzapFunctions::izapAdminSettings_izap_videos('izap_river_thumbnails');
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
