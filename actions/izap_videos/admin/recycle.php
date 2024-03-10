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

$guid = (int) get_input('guid');
$queue_object = new IzapQueue();

if (!($queue_object->restore($guid))) {
	return elgg_error_response(elgg_echo('izap_videos:adminSettings:restore_video_error'));
}

\IzapFunctions::izapTrigger_izap_videos();

return elgg_ok_response('', elgg_echo('izap_videos:adminSettings:restore_video'), REFERER);
