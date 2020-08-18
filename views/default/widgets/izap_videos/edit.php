<?php
/**
 * Widget settings for latest videos
 *
 */

$widget = elgg_extract('entity', $vars);

echo elgg_view('object/widget/edit/num_display', [
	'entity' => $widget,
	'label' => elgg_echo('izap_videos:numbertodisplay'),
	'max' => 25,
	'default' => 4,
]);
