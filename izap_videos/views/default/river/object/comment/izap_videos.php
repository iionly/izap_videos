<?php
/**
 * Post comment on videos river view
 */

elgg_load_js('lightbox');
elgg_load_css('lightbox');

$item = $vars['item'];

$comment = $item->getObjectEntity();
$target = $item->getTargetEntity();

$attachments = '';
$size = izapAdminSettings_izap_videos('izap_river_thumbnails');
if($size != 'none') {
	$attachments = elgg_view_entity_icon($target, $size, array(
		'href' => 'ajax/view/izap_videos/playpopup?guid=' . $target->getGUID(),
		'title' => $target->title,
		'img_class' => 'screenshot',
		'link_class' => 'elgg-lightbox',
	));
}

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'attachments' => $attachments,
	'message' => elgg_get_excerpt($comment->description),
));
