<?php
/**
 * iZAP Videos upgrade action
 */

$plugins_path = elgg_get_plugins_path();

require_once "{$plugins_path}izap_videos/version.php";

$local_version = elgg_get_plugin_setting('local_version', 'izap_videos');

if ($version <= $local_version) {
	register_error('No upgrade required');
	forward(REFERER);
}

set_time_limit(0);

$base_dir = "{$plugins_path}izap_videos/upgrades";

// taken from engine/lib/version.php
if ($handle = opendir($base_dir)) {
	$upgrades = array();

	while ($updatefile = readdir($handle)) {
		// Look for upgrades and add to upgrades list
		if (!is_dir("$base_dir/$updatefile")) {
			if (preg_match('/^([0-9]{10})\.(php)$/', $updatefile, $matches)) {
				$plugin_version = (int) $matches[1];
				if ($plugin_version > $local_version) {
					$upgrades[] = "$base_dir/$updatefile";
				}
			}
		}
	}

	// Sort and execute
	asort($upgrades);

	if (sizeof($upgrades) > 0) {
		foreach ($upgrades as $upgrade) {
			include($upgrade);
		}
	}
}

elgg_set_plugin_setting('local_version', $version, 'izap_videos');

system_message(elgg_echo('izap_videos:upgrade:success'));
forward(REFERER);
