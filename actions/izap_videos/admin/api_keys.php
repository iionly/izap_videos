<?php

$plugin = elgg_get_plugin_from_id('izap_videos');

$plugin_name = $plugin->getDisplayName();

$params = (array) get_input('params');
foreach ($params as $k => $v) {
	$result = $plugin->setSetting($k, $v);
	if (!$result) {
		return elgg_error_response(elgg_echo('plugins:settings:save:fail', [$plugin_name]));
	}
}

return elgg_ok_response('', elgg_echo('izap_videos:success:adminSettingsSaved'), REFERER);
