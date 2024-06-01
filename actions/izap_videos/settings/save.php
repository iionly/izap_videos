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
$plugin = elgg_get_plugin_from_id('izap_videos');
if (!$plugin) {
	return elgg_error_response(elgg_echo('plugins:settings:save:fail', [$plugin_id]));
}
$plugin_name = $plugin->getDisplayName();

if (!$params['izapVideoOptions']) {
	return elgg_error_response(elgg_echo('izap_videos:error:videoOptionBlank'));
}

// limit izapMaxFileSize to php value upload_max_filesize at maximum
$php_max_file_upload = ini_get('upload_max_filesize');
if ($php_max_file_upload) {
	$php_max_file_upload = trim($php_max_file_upload);
	$last = strtolower($php_max_file_upload[strlen($php_max_file_upload) - 1]);
	$php_max_file_upload = (int) substr($php_max_file_upload, 0, -1);
	switch($last) {
		case 'g':
			$php_max_file_upload *= 1024;
		case 'm':
			$php_max_file_upload *= 1024;
		case 'k':
			$php_max_file_upload *= 1024;
	}

	$izap_max_file_upload = (int) $params['izapMaxFileSize'];
	$izap_max_file_upload = $izap_max_file_upload * 1024 * 1024;

	if ($izap_max_file_upload > $php_max_file_upload) {
		$php_max_file_upload = (int) ($php_max_file_upload / 1024 / 1024);
		$params['izapMaxFileSize'] = (string) $php_max_file_upload;
	}
}

if (!empty($params['izapKeepOriginal'])) {
	$params['izapKeepOriginal'] = 'YES';
} else {
	$params['izapKeepOriginal'] = 'NO';
}

if (!empty($params['izapExtendedSidebarMenu'])) {
	$params['izapExtendedSidebarMenu'] = 'YES';
} else {
	$params['izapExtendedSidebarMenu'] = 'NO';
}

foreach($params as $key => $values) {
	if (!\IzapFunctions::izapAdminSettings_izap_videos($key, $values, true)) {
		return elgg_error_response(elgg_echo('plugins:settings:save:fail', [$plugin_name]));
		exit;
	}
	if ($key == 'izapVideoOptions') {
		if (in_array('ONSERVER', $values)) {
			$queue_object = new IzapQueue();
		}
	}
}

return elgg_ok_response('', elgg_echo('izap_videos:success:adminSettingsSaved', [$plugin_name]));
