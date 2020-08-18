<?php
/**
 * Index page Latest Videos widget for Widget Manager plugin
 *
 */

$widget = elgg_extract('entity', $vars);

echo elgg_view('object/widget/edit/num_display', [
	'entity' => $widget,
	'name' => 'latest_videos_count',
	'label' => elgg_echo('izap_videos:numbertodisplay'),
	'max' => 25,
	'default' => 4,
]);
