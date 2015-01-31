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

$postedArray = get_input('izap');

$plugin = elgg_get_plugin_from_id('izap_videos');

$videoOptions = filter_tags($_POST['izap']['izapVideoOptions']);
if (empty($videoOptions)) {
	register_error(elgg_echo('izap_videos:error:videoOptionBlank'));
	forward(REFERER);
}
$postedArray['izapVideoOptions'] = $videoOptions;

if (!empty($postedArray['izapKeepOriginal'])) {
	$postedArray['izapKeepOriginal'] = 'YES';
} else {
	$postedArray['izapKeepOriginal'] = 'NO';
}

foreach($postedArray as $key => $values) {
	izapAdminSettings_izap_videos($key, $values, true);
	if($key == 'izapVideoOptions') {
		if(in_array('ONSERVER', $values)) {
			$queue_object = new izapQueue();
		}
	}
}

system_message(elgg_echo('izap_videos:success:adminSettingsSaved'));
forward(REFERER);
