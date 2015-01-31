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

$posted_array = get_input('izap');
if (is_array($posted_array)) {
	foreach($posted_array  as $key => $value) {
		if(is_integer($key)) {
			$guid = $key;
			break;
		}
	}
}

$queue_object = new izapQueue();
$video_to_be_deleted = $queue_object->get_from_trash($guid);

//Send posted comment to user who uploaded this video
if ($posted_array['send_message_'.$guid] == 'yes') {
	notify_user($video_to_be_deleted[0]['owner_id'],
		elgg_get_site_entity()->getGUID(),
		elgg_echo('izap_videos:notifySub:video_deleted'),
		$posted_array['user_message_'.$guid]
	);
}

// delete data from trash
if (get_entity($guid)->delete()) {
	system_message(elgg_echo('izap_videos:adminSettings:deleted_from_trash'));
	izapTrigger_izap_videos();
}

forward(REFERER);
