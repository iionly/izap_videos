<?php

$video = $vars['entity'];

$video->updateViews();

$owner_link = elgg_view('output/url', array(
	'href' => "videos/owner/" . $video->getOwnerEntity()->username,
	'text' => $video->getOwnerEntity()->name,
));
$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($video->time_created);
$categories = elgg_view('output/categories', $vars);

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

$owner_icon = elgg_view_entity_icon($video->getOwnerEntity(), 'tiny');

$metadata = elgg_view_menu('entity', array(
	'entity' => $video,
	'handler' => 'videos',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date $comments_link $categories";

$params = array(
	'entity' => $video,
	'title' => false,
	'metadata' => $metadata,
	'subtitle' => $subtitle,
	'tags' => $tags,
);
$list_body = elgg_view('object/elements/summary', $params);

$params = array('class' => 'mbl');
$summary = elgg_view_image_block($owner_icon, $list_body, $params);

echo $summary;

// Display the video player to allow for the video to be played
echo '<div align="center" class="izapPlayer">';
echo $video->getPlayer();
echo '</div>';

if ($video->description) {
	echo elgg_view('output/longtext', array(
		'value' => $video->description,
		'class' => 'mbl',
	));
}

// Optional view for other plugins to extend (not used by izap_videos itself)
echo elgg_view('izap_videos/extendedPlay', $vars);

if ($video->converted == 'yes') {
	echo elgg_view_comments($video);
}
