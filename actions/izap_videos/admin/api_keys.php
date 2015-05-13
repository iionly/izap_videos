<?php

$plugin = elgg_get_plugin_from_id('izap_videos');

$params = get_input('params');
foreach ($params as $k => $v) {
	if (!$plugin->setSetting($k, $v)) {
		register_error(elgg_echo('plugins:settings:save:fail', array('izap_videos')));
		forward(REFERER);
	}
}

system_message(elgg_echo('izap_videos:success:adminSettingsSaved'));
forward(REFERER);
