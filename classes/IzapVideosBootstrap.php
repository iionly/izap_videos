<?php

use Elgg\DefaultPluginBootstrap;

class IzapVideosBootstrap extends DefaultPluginBootstrap {

	public function init() {
		elgg_register_ajax_view('izap_videos/admin/getQueue');
		elgg_register_ajax_view('izap_videos/playpopup');

		// Register video.js stuff
		elgg_define_js('izap_videos_videojs_js', [
			'src' => elgg_get_simplecache_url('izap_videos_videojs/video.min.js'),
		]);
	}

	public function activate() {
		// save current version number
		$old_version_izap_videos = elgg_get_plugin_setting('version_izap_videos', 'izap_videos');
		$new_version_izap_videos = '4.0.0';
		if (version_compare($new_version_izap_videos, $old_version_izap_videos, '!=')) {
			// Set new version
			$plugin = elgg_get_plugin_from_id('izap_videos');
			$plugin->setSetting('version_izap_videos', $new_version_izap_videos);
		}

		// sets $version based on code
		require_once elgg_get_plugins_path() . "izap_videos/version.php";

		$local_version = elgg_get_plugin_setting('local_version', 'izap_videos');
		if ($local_version === null) {
			// set initial version for new install
			$plugin = elgg_get_plugin_from_id('izap_videos');
			$plugin->setSetting('local_version', $local_version);
		}
	}
}
