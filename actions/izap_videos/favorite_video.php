<?php
/**
 * iZAP Videos plugin by iionly
 * (based on version 3.71b of the original izap_videos plugin for Elgg 1.7)
 * Contact: iionly@gmx.de
 * https://github.com/iionly
 *
 * Original developer of the iZAP Videos plugin:
 * @package Elgg videotizer, by iZAP Web Solutions
 * @license GNU Public License version 2
 * @Contact iZAP Team "<support@izap.in>"
 * @Founder Tarun Jangra "<tarun@izap.in>"
 * @link http://www.izap.in/
 *
 */

$video_guid = (int) get_input('guid', 0);
$video = get_entity($video_guid);

if (!($video instanceof IzapVideos)) {
	return elgg_error_response(elgg_echo('izap_videos:favorite_error'));
}

$izap_action = get_input('izap_action', false);

// Removing from favorite list
if ($izap_action == 'remove') {
	izap_remove_favorited($video);
	return elgg_ok_response('', elgg_echo('izap_videos:favorite_removed'), REFERER);
}

// Adding to favorite list
elgg_call(ELGG_IGNORE_ACCESS, function() use ($video) {
	$old_array = $video->favorited_by;
	$new_array = array_merge((array) $old_array, (array) elgg_get_logged_in_user_guid());
	$video->favorited_by = array_unique($new_array);
});

return elgg_ok_response('', elgg_echo('izap_videos:favorite_saved'), REFERER);
