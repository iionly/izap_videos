<?php
/**
 * Video added river view
 */

elgg_load_js('lightbox');
elgg_load_css('lightbox');

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->description);
$excerpt = elgg_get_excerpt($excerpt);

$attachments = '';
$size = izapAdminSettings_izap_videos('izap_river_thumbnails');
if($size != 'none') {
	$attachments = elgg_view_entity_icon($object, $size, array(
		'href' => 'ajax/view/izap_videos/playpopup?guid=' . $object->getGUID(),
		'title' => $object->title,
		'img_class' => 'screenshot',
		'link_class' => 'elgg-lightbox',
	));
}

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'attachments' => $attachments,
	'message' => $excerpt,
));
