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

$params = (array) get_input('params');

foreach($params as $key => $value) {
	if (is_integer($key)) {
		$guid = $key;
		break;
	}
}

$queue_object = new IzapQueue();
$video_to_be_deleted = $queue_object->get_from_trash($guid);

//Send posted comment to user who uploaded this video
if ($params['send_message_' . $guid] == 'yes') {
	notify_user($video_to_be_deleted[0]['owner_id'],
		elgg_get_site_entity()->getGUID(),
		elgg_echo('izap_videos:notifySub:video_deleted'),
		$params['user_message_' . $guid]
	);
}

// delete data from trash
if (!get_entity($guid)->delete()) {
	return elgg_error_response(elgg_echo('izap_videos:adminSettings:deleted_from_trash_error'));
}

\IzapFunctions::izapTrigger_izap_videos();

return elgg_ok_response('', elgg_echo('izap_videos:adminSettings:deleted_from_trash'), REFERRER);
