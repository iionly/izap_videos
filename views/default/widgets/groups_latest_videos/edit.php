<?php
/**
 * Groups page Latest Videos widget for Widget Manager plugin
 *
 */

/* @var $widget ElggWidget */
$widget = elgg_extract('entity', $vars);

$count = (int) $widget->latest_videos_count;
if ($count < 1) {
	$count = 4;
}

echo elgg_view_field([
	'#type' => 'number',
	'#label' => elgg_echo('izap_videos:numbertodisplay'),
	'name' => 'params[latest_videos_count]',
	'value' => $count,
	'min' => 1,
	'max' => 25,
	'step' => 1,
]);
