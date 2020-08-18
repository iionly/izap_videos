<?php

/**
 * Fetch time_created value from views metadata and set it as value for the newly added last_viewed matadata
 */

set_time_limit(0);

elgg_call(ELGG_IGNORE_ACCESS | ELGG_SHOW_DISABLED_ENTITIES, function () {

	$batch = elgg_get_entities([
		'type' => 'object',
		'subtype' => 'izap_videos',
		'batch' => true,
		'limit' => false,
	]);

	foreach ($batch as $video) {
		if (!$video->last_viewed) {
			$views_metadata = elgg_get_metadata([
				'guid' => (int) $video->guid,
				'metadata_name' => 'views',
				'limit' => 1,
			]);

			$id = $views_metadata[0]->id;
			if ($id) {
				$video->last_viewed = $views_metadata[0]->time_created;
			}
		}
	}
});

elgg_flush_caches();
