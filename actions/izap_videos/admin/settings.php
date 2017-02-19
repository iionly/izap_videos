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

$videoOptions = filter_tags($_POST['izap']['izapVideoOptions']);
if (empty($videoOptions)) {
	register_error(elgg_echo('izap_videos:error:videoOptionBlank'));
	forward(REFERER);
}
$postedArray['izapVideoOptions'] = $videoOptions;

// limit izapMaxFileSize to php value upload_max_filesize at maximum
$php_max_file_upload = ini_get('upload_max_filesize');
if ($php_max_file_upload) {
	$php_max_file_upload = trim($php_max_file_upload);
	$last = strtolower($php_max_file_upload[strlen($php_max_file_upload)-1]);
	switch($last) {
		case 'g':
			$php_max_file_upload *= 1024;
		case 'm':
			$php_max_file_upload *= 1024;
		case 'k':
			$php_max_file_upload *= 1024;
	}

	$izap_max_file_upload = (int)$postedArray['izapMaxFileSize'];
	$izap_max_file_upload = $izap_max_file_upload * 1024 * 1024;

	if ($izap_max_file_upload > $php_max_file_upload) {
		$php_max_file_upload = (int)($php_max_file_upload / 1024 / 1024);
		$postedArray['izapMaxFileSize'] = (string)$php_max_file_upload;
	}
}

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
