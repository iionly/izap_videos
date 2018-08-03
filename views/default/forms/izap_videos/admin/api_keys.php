<?php

$youtube_api_key = elgg_get_plugin_setting('youtube_api_key', 'izap_videos');

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:adminSettings:youtube_api_key'),
	'#help' => elgg_echo('izap_videos:adminSettings:youtube_api_key_description'),
	'name' => 'params[youtube_api_key]',
	'value' => $youtube_api_key,
]);

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('izap_videos:adminSettings:save'),
]);

elgg_set_form_footer($footer);
