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

$guid = (int)get_input('guid');
$izap_videos = izapVideoCheck_izap_videos($guid, true);
$owner = get_entity($izap_videos->container_guid);

if($izap_videos->delete_izap_video()) {
	system_message(elgg_echo('izap_videos:deleted'));
	izapTrigger_izap_videos();
} else {
	register_error(elgg_echo('izap_videos:notdeleted'));
}

forward('videos/owner/' . $owner->username);
