<?php

/**
 * Update "videosrc" and "converted" metadata values for existing izap_videos entities if not yet set
 */

set_time_limit(0);

// Ignore access to make sure all items get updated
$ia = elgg_set_ignore_access(true);

elgg_register_plugin_hook_handler('permissions_check', 'all', 'elgg_override_permissions');
elgg_register_plugin_hook_handler('container_permissions_check', 'all', 'elgg_override_permissions');

// Make sure that entries for disabled entities also get upgraded
$access_status = access_get_show_hidden_status();
access_show_hidden_entities(true);

$batch = new ElggBatch('elgg_get_entities', array(
	'type' => 'object',
	'subtype' => 'izap_videos',
	'limit' => false
));
foreach ($batch as $video) {
	if (empty($video->videosrc)) {
		$video->videosrc = $video->IZAPSETTINGS->filesPath . 'file/' . $video->guid . '/' . elgg_get_friendly_title($video->title) . '.flv';
	}

	if (empty($video->converted)) {
		$video->converted = 'yes';
	}
}

$db_prefix = elgg_get_config('dbprefix');
$query = "
	DELETE FROM {$db_prefix}metadata WHERE
	(
		(entity_guid NOT IN (SELECT guid FROM {$db_prefix}entities)) AND
		(name_id IN (SELECT id FROM {$db_prefix}metastrings WHERE string='videosrc'))
	)";
delete_data($query);

$query = "
	DELETE FROM {$db_prefix}metadata WHERE
	(
		(entity_guid NOT IN (SELECT guid FROM {$db_prefix}entities)) AND
		(name_id IN (SELECT id FROM {$db_prefix}metastrings WHERE string='converted'))
	)";
delete_data($query);

elgg_invalidate_simplecache();
elgg_reset_system_cache();
_elgg_services()->autoloadManager->deleteCache();

elgg_set_ignore_access($ia);
access_show_hidden_entities($access_status);
