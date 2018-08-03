<?php
/**
 * Widget settings for latest videos
 *
 */

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$count = (int) $widget->num_display;
if ($count < 1) {
	$count = 4;
}

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('izap_videos:numbertodisplay'),
	'name' => 'params[num_display]',
	'value' => $count,
	'min' => 1,
	'max' => 25,
	'step' => 1,
]);
