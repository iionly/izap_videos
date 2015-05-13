<?php

$youtube_api_key = elgg_get_plugin_setting('youtube_api_key', 'izap_videos');

$form = "<div class='mbl'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:youtube_api_key') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'params[youtube_api_key]',
			'value' => $youtube_api_key,
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:youtube_api_key_description'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= elgg_view('input/submit', array('value' => elgg_echo('izap_videos:adminSettings:save')));

echo $form;
