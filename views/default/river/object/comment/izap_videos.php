<?php
/**
 * Post comment on videos river view
 */

elgg_load_js('lightbox');
elgg_load_css('lightbox');

$item = $vars['item'];

$subject = $item->getSubjectEntity();
$comment = $item->getObjectEntity();
$target = $item->getTargetEntity();

$subject_link = elgg_view('output/url', array(
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
));

$target_link = elgg_view('output/url', array(
	'href' => $target->getURL(),
	'text' => $target->title,
	'class' => 'elgg-river-target',
	'is_trusted' => true,
));

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
	'summary' => elgg_echo('river:comment:object:izap_videos', array($subject_link, $target_link)),
	'message' => elgg_get_excerpt($comment->description),
));
