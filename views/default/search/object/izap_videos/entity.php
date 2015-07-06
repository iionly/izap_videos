<?php

$video = elgg_extract('entity', $vars, false);

if (!$video) {
	return true;
}

$owner = $video->getOwnerEntity();
$categories = elgg_view('output/categories', $vars);
$excerpt = elgg_get_excerpt($video->description);

$owner_link = elgg_view('output/url', array(
	'href' => "videos/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));

$video_icon = elgg_view_entity_icon($video, 'medium', array(
		'href' => $video->getURL(),
		'title' => $video->title,
		'is_trusted' => true,
		'img_class' => 'screenshot',
));

$date = elgg_view_friendly_time($video->time_created);

$comments_count = $video->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $video->getURL() . '#comments',
		'text' => $text,
		'is_trusted' => true,
	));
} else {
	$comments_link = '';
}

$subtitle = "$author_text $date $comments_link $categories";

$metadata = elgg_view_menu('entity', array(
	'entity' => $video,
	'handler' => 'videos',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

$params = array(
	'entity' => $video,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'content' => $excerpt,
);
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

echo elgg_view_image_block($video_icon, $list_body);
