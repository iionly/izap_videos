<?php

$plugin = elgg_get_plugin_from_id('izap_videos');

$params = (array) get_input('params');
foreach ($params as $k => $v) {
	if (!$plugin->setSetting($k, $v)) {
		return elgg_error_response(elgg_echo('plugins:settings:save:fail', ['izap_videos']));
	}
}

return elgg_ok_response('', elgg_echo('izap_videos:success:adminSettingsSaved'), REFERER);
