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

$izap_videos = get_entity($guid);
if (!($izap_videos instanceof IzapVideos)) {
	// unable to get Elgg entity
	return elgg_error_response(elgg_echo('izap_videos:notdeleted'), REFERER);
}

if (!$izap_videos->canEdit()) {
	// user doesn't have permissions
	return elgg_error_response(elgg_echo('izap_videos:notdeleted'), REFERER);
}

$owner = get_entity($izap_videos->container_guid);

$forward_url = REFERER;
if ($owner instanceof ElggUser) {
	$forward_url = elgg_generate_url('collection:object:izap_videos:owner', ['username' => $owner->username]);
} else if ($owner instanceof ElggGroup) {
	$forward_url = elgg_generate_url('collection:object:izap_videos:group', ['guid' => $owner->guid]);
}

$uploaded = $izap_videos->videotype == 'uploaded';

if (!$izap_videos->delete()) {
	return elgg_error_response(elgg_echo('izap_videos:notdeleted'), REFERER);
}

if ($uploaded) {
	\IzapFunctions::izapTrigger_izap_videos();
}

return elgg_ok_response('', elgg_echo('izap_videos:deleted'), $forward_url);
