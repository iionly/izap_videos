<?php

if (get_subtype_id('object', IzapVideos::SUBTYPE)) {
	update_subtype('object', IzapVideos::SUBTYPE, 'IzapVideos');
} else {
	add_subtype('object', IzapVideos::SUBTYPE, 'IzapVideos');
}

// With version 2.3.4 the old upgrade stuff was removed taking as given that anyone
// upgrading from an older version of iZAP Videos has already got it running before
// because it was there already since the beginning (v1.8.0 of my fork) and upgrading
// to version 2.3.4 directly from a pre-Elgg 1.8 version is supposed to be highly
// unlikely if not even impossible.
// For possible future upgrade scripts getting added the version info is still
// updated as before just to be ready.
require_once(dirname(__FILE__) . '/version.php');
elgg_set_plugin_setting('local_version', $version, 'izap_videos');

// save current version number
$new_version = '2.3.5';
elgg_set_plugin_setting('version_izap_videos', $new_version, 'izap_videos');
