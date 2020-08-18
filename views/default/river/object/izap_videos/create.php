<?php
/**
 * Video added river view
 */

elgg_require_js('izap_videos/izapvidjs');

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->description);
$excerpt = elgg_get_excerpt($excerpt);

$attachments = '';
$size = izapAdminSettings_izap_videos('izap_river_thumbnails');
if ($size != 'none') {
	$attachments = elgg_view_entity_icon($object, $size, [
		'href' => 'ajax/view/izap_videos/playpopup?guid=' . $object->getGUID(),
		'title' => $object->title,
		'img_class' => 'elgg-photo izap-photo',
		'link_class' => 'izapvid-river-lightbox',
	]);
}

echo elgg_view('river/elements/layout', [
	'item' => $vars['item'],
	'attachments' => $attachments,
	'message' => $excerpt,
]);
