<?php

if (get_subtype_id('object', 'izap_videos')) {
	update_subtype('object', 'izap_videos', 'IzapVideos');
} else {
	add_subtype('object', 'izap_videos', 'IzapVideos');
}

$new_version = '1.10.9';
$old_version = elgg_get_plugin_setting('version_izap_videos', 'izap_videos');

if (!$old_version) {

	// if old version < 3.55 of original izap_videos plugin has previously been installed some data changes in the database are necessary
	if ((real)datalist_get('izap_videos_version') < 3.55) {
		// clears the old plugin settings
		elgg_unset_all_plugin_settings('izap_videos');

		$db_prefix = elgg_get_config('dbprefix');
		$del_entity_query = "DELETE FROM {$db_prefix}entities
								WHERE subtype IN (SELECT id FROM {$db_prefix}entity_subtypes
								WHERE subtype='izapVideoQueue')";
		delete_data($del_entity_query);
		$del_queue_object_query = "DELETE FROM {$db_prefix}entity_subtypes where subtype='izapVideoQueue'";
		delete_data($del_queue_object_query);
	}
	elgg_set_plugin_setting('version_izap_videos', $new_version, 'izap_videos');
} elseif (version_compare($new_version, $old_version, '!=')) {
	elgg_set_plugin_setting('version_izap_videos', $new_version, 'izap_videos');
}
