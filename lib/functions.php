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

/**
 * This converts the array into object
 *
 * @param array $array
 * @return object
 */
function izapArrayToObject_izap_videos($array) {
	if (!is_array($array))
		return false;

	$obj = new stdClass();
	foreach ($array as $key => $value) {
		if($key != '' && $value != '') {
			$obj->$key = $value;
		}
	}

	return $obj;
}

/**
 * Sets or gets the private settings for the izap_videos
 *
 * @param string $settingName setting name
 * @param mix $values sting or array of value
 * @param boolean $override if we want to force override the value
 * @param boolean $makeArray if we want the return value in the array
 * @return value array or string
 */
function izapAdminSettings_izap_videos($settingName, $values = '', $override = false, $makeArray = false) {
	// get the old value
	$oldSetting = elgg_get_plugin_setting($settingName, 'izap_videos');

	if (is_array($values)) {
		$pluginValues = implode('|', $values);
	} else {
		$pluginValues = $values;
	}
	// if it is not set yet
	if (empty($oldSetting) || $override) {
		if (!elgg_set_plugin_setting($settingName, $pluginValues, 'izap_videos')) {
			return false;
		}
	}

	if ($oldSetting) {
		$oldArray = explode('|', $oldSetting);
		if (count($oldArray) > 1) {
			$returnVal = $oldArray;
		} else {
			$returnVal = $oldSetting;
		}
	} else {
		$returnVal = $values;
	}

	if (!is_array($returnVal) && $makeArray) {
		$newReturnVal[] = $returnVal;
		$returnVal = $newReturnVal;
	}
	return $returnVal;
}

/**
 * Gets the video add options from the admin settings
 *
 * @return array
 */
function izapGetVideoOptions_izap_videos() {
	$videoOptions = izapAdminSettings_izap_videos('izapVideoOptions', '', false, true);
	return $videoOptions;
}

/**
 * This function saves the entry for futher processing
 * @param string $file main filepath
 * @param int $videoId video guid
 * @param int $ownerGuid owner guid
 * @param int $accessId access id to be used after completion of encoding of video
 */
function izapSaveFileInfoForConverting_izap_videos($file, $video, $defined_access_id = 2) {
	// This will not let save anything if there is no file to convert
	if (!file_exists($file) || !$video) {
		return false;
	}
	$queue = new IzapQueue();
	$queue->put($video, $file, $defined_access_id);
}

/**
 * This function checks if the queue is running or not
 *
 * @return boolean true if yes or false if no
 */
function izapIsQueueRunning_izap_videos() {
	$queue_object = new IzapQueue();

	$numberof_process = $queue_object->check_process();
	if ($numberof_process) {
		return true;
	} else {
		return false;
	}
}

/**
 * This function triggers the queue
 *
 * @global <type> $CONFIG
 */
function izapTrigger_izap_videos() {
	if (!izapIsQueueRunning_izap_videos()) {
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', izapAdminSettings_izap_videos('izapMaxFileSize') + 100 . 'M');

		izapGetAccess_izap_videos(); // get the complete access to the system
		izapRunQueue_izap_videos();
		izapRemoveAccess_izap_videos(); // remove the access from the system
	}
}

/**
 * Resets queue
 *
 * @return boolean
 */
function izapResetQueue_izap_videos() {
  return izapAdminSettings_izap_videos('isQueueRunning', 'no', true);
}

/**
 * Clears queue and resets it
 *
 * @return boolean
 */
function izapEmptyQueue_izap_videos() {
	$pending_videos = izapGetNotConvertedVideos_izap_videos();
	if ($pending_videos) {
		foreach($pending_videos as $video) {
			$video->delete();
		}
	}

	return izapResetQueue_izap_videos();
}

/**
 * Grants the access
 *
 * @param <type> $functionName
 */
function izapGetAccess_izap_videos() {
	izap_access_override(['status' => true]);
}

/**
 * Remove access
 *
 * @param string $functionName
 */
function izapRemoveAccess_izap_videos() {
	izap_access_override(['status' => false]);
}


function izap_access_override($params = []) {
	if ($params['status']) {
		$func = "elgg_register_plugin_hook_handler";
	} else {
		$func = "elgg_unregister_plugin_hook_handler";
	}

	$func_name = "izapGetAccessForAll_izap_videos";

	$func("permissions_check", "all", $func_name, 9999);
	$func("container_permissions_check", "all", $func_name, 9999);
	$func("permissions_check:metadata", "all", $func_name, 9999);
}

/**
 * Elgg hook to override permission check of entities (izap_videos, izapVideoQueue, izap_recycle_bin)
 *
 * @param <type> $hook
 * @param <type> $entity_type
 * @param <type> $returnvalue
 * @param <type> $params
 * @return <type>
 */
function izapGetAccessForAll_izap_videos($hook, $entity_type, $returnvalue, $params) {
	return true;
}

/**
 * This function returns the FFmpeg video converting command
 *
 * @return string path
 */
function izapGetFfmpegVideoConvertCommand_izap_videos() {
	$path = izapAdminSettings_izap_videos('izapVideoCommand');
	$path = html_entity_decode($path);
	if (!$path) {
		$path = '';
	}
	return $path;
}


/**
 * This function returns the FFmpeg video image command
 *
 * @return string path
 */
function izapGetFfmpegVideoImageCommand_izap_videos() {
	$path = izapAdminSettings_izap_videos('izapVideoThumb');
	$path = html_entity_decode($path);
	if (!$path) {
		$path = '';
	}
	return $path;
}

/**
 * This function actually converts the video
 * @param string $file file loacation
 * @param int $videoId video guid
 * @param int $ownerGuid video owner guid
 * @param int $accessId access id
 * @return boolean
 */
function izapConvertVideo_izap_videos($file, $videoId, $videoTitle, $videoUrl, $ownerGuid, $accessId = 2) {
	$return = false;

	// Works only if we have the input file
	if (file_exists($file)) {
		// Need to set flag for the file going in the conversion
		$queue_object = new IzapQueue;
		$queue_object->change_conversion_flag($videoId);

		$video = new IzapConvert($file);
		$videofile = $video->izap_video_convert();

		// Check if everything is ok
		if (!is_array($videofile)) {
			// If everything is ok then get back values to save
			$file_values = $video->getValues();
			$izap_videofile = 'izap_videos/uploaded/' . $file_values['filename'];
			$izap_origfile = 'izap_videos/uploaded/' . $file_values['origname'];
			$izap_videos = get_entity($videoId);
			$izap_videos->setFilename($izap_videofile);
			$izap_videos->open("write");
			$izap_videos->write($file_values['filecontent']);

			// Check if we have to keep original file
			if (izapAdminSettings_izap_videos('izapKeepOriginal') == 'YES') {
				$izap_videos->setFilename($izap_origfile);
				$izap_videos->open("write");
				$izap_videos->write($file_values['origcontent']);
			}

			$izap_videos->converted = 'yes';
			$izap_videos->videofile = $izap_videofile;
			$izap_videos->orignalfile = $izap_origfile;

			notify_user($ownerGuid,
				elgg_get_site_entity()->getGUID(),
				elgg_echo('izap_videos:notifySub:videoConverted'),
				elgg_echo('izap_videos:notifyMsg:videoConverted', [$videoUrl])
			);
			return true;
		} else {
			$errorReason = $videofile['message'];
		}
	} else {
		$errorReason = elgg_echo('izap_videos:fileNotFound');
	}

	$adminGuid = izapGetSiteAdmin_izap_videos(true);
	// notify admin
	notify_user($adminGuid,
		elgg_get_site_entity()->getGUID(),
		elgg_echo('izap_videos:notifySub:videoNotConverted'),
		elgg_echo('izap_videos:notifyAdminMsg:videoNotConverted', [$errorReason])
	);

	if (!empty($errorReason)) {
		$return = ['error' => true, 'reason' => $errorReason];
	}
	return $return;
}

function izapRunQueue_izap_videos() {
	$queue_object = new IzapQueue();
	$queue = $queue_object->fetch_videos();
	if (is_array($queue)) {
		foreach($queue as $pending) {
			$converted = izapConvertVideo_izap_videos($pending['main_file'], $pending['guid'], $pending['title'], $pending['url'], $pending['owner_id']);
			if (!$converted) {
				$queue_object->move_to_trash($pending['guid']);
			}
			$queue_object->delete($pending['guid']);
			izap_update_all_defined_access_id($pending['guid'], $pending['access_id']);
		}
		// re-check if there are new videos in the queue
		if ($queue_object->count() > 0) {
			izapRunQueue_izap_videos();
		}
	}
	return true;
}

/**
 * This function gets the site admin
 *
 * @param boolean $guid if only guid is required
 * @return mix depends on the input and result
 */
function izapGetSiteAdmin_izap_videos($guid = false) {
	$admin = elgg_get_entities_from_metadata([
		'type' => 'user',
		'metadata_name' => 'admin',
		'metadata_value' => 1,
		'limit' => 1,
	]);
	if ($admin[0]->admin || $admin[0]->siteadmin) {
		if($guid) {
			return $admin[0]->getGUID();
		} else {
			return $admin[0];
		}
	}
	return false;
}

/**
 * Gets the videos that were not converted
 *
 * @return boolean or entites
 */
function izapGetNotConvertedVideos_izap_videos() {
	$not_converted_videos = elgg_get_entities_from_metadata([
		'type' => 'object',
		'subtype' => IzapVideos::SUBTYPE,
		'metadata_name' => 'converted',
		'metadata_value' => 'no',
		'limit' => false,
	]);
	if ($not_converted_videos) {
		return $not_converted_videos;
	}

	return false;
}

function izapReadableSize_izap_videos($inputSize) {
	if (strpos($inputSize, 'M')) {
		return $inputSize . 'B';
	}

	$outputSize = $inputSize / 1024;
	if ($outputSize < 1024) {
		$outputSize = number_format($outputSize, 2);
		$outputSize .= ' KB';
	} else {
		$outputSize = $outputSize / 1024;
		if ($outputSize < 1024) {
			$outputSize = number_format($outputSize, 2);
			$outputSize .= ' MB';
		} else {
			$outputSize = $outputSize / 1024;
			$outputSize = number_format($outputSize, 2);
			$outputSize .= ' GB';
		}
	}
	return $outputSize;
}

/**
 * A quick way to convert bytes to a more readable format
 * http://in3.php.net/manual/en/function.filesize.php#91477
 *
 * @param integer $bytes size in bytes
 * @param integer $precision
 * @return string
 */
function izapFormatBytes($bytes, $precision = 2) {
	$units = ['B', 'KB', 'MB', 'GB', 'TB'];

	$bytes = max($bytes, 0);
	$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
	$pow = min($pow, count($units) - 1);

	$bytes /= pow(1024, $pow);

	return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Counts the queued videos
 * @return integer
 */
function izap_count_queue() {
	$queue_object = new IzapQueue();
	return $queue_object->count();
}

function izap_get_video_name_prefix() {
	$domain = elgg_get_site_entity()->getDomain();
	$domain = preg_replace('/[^A-Za-z0-9]+/','_',$domain);

	return $domain . '_izap_videos_';
}

//Hack to correct the access id of the uploaded video.
function izap_update_all_defined_access_id($entity_guid, $accessId = ACCESS_PUBLIC) {
	$db_prefix = elgg_get_config('dbprefix');
	// update metadata
	$query = 'UPDATE ' . $db_prefix . 'metadata SET access_id = ' . $accessId . ' WHERE entity_guid = ' . $entity_guid;
	$query = update_data($query);
	if (!$query) {
		return false;
	}
	$query = 'UPDATE ' . $db_prefix . 'entities SET access_id = ' . $accessId . ' WHERE guid = ' . $entity_guid;
	update_data($query);
	return $query;
}


function izap_is_my_favorited($video) {
	$users = (array) $video->favorited_by;
	$key = array_search(elgg_get_logged_in_user_guid(), $users);
	if ($key !== false) {
		return true;
	}

	return false;
}

function izap_remove_favorited($video, $user_guid = 0) {
	$users = (array) $video->favorited_by;

	if (!$user_guid) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$key = array_search($user_guid, $users);

	if ($key !== false) {
		unset($users[$key]);
	}

	izapGetAccess_izap_videos();
	$video->favorited_by = array_unique($users);
	izapRemoveAccess_izap_videos();

	return true;
}

/**
 * Returns the file name, that ffmpeg can operate
 *
 * @param string $fileName file name
 * @return string all formated file name
 */
function izapGetFriendlyFileName_izap_videos($fileName) {
	$new_name .= izap_get_video_name_prefix();
	$new_name .= time() . '_';
	$new_name .= preg_replace('/[^A-Za-z0-9\.]+/','_',$fileName);
	return $new_name;
}

/**
 * This function checks the supported videos
 *
 * @param string $videoFileName video name with extension
 * @return boolean TRUE if supported else FALSE
 */
function izapSupportedVideos_izap_videos($videoFileName) {
	global $IZAPSETTINGS;
	$supportedFormats = $IZAPSETTINGS->allowedExtensions;
	$extension = izap_get_file_extension($videoFileName);
	if (in_array($extension, $supportedFormats)) {
		return true;
	}

	return false;
}

/**
 * This function checks the max upload limit for files
 *
 * @param integer $fileSize in Mb
 * @return boolean true if everything is ok else false
 */
function izapCheckFileSize_izap_videos($fileSize) {
	$maxFileSize = (int) izapAdminSettings_izap_videos('izapMaxFileSize');
	$maxSizeInBytes = $maxFileSize * 1024 * 1024;

	if ($fileSize > $maxSizeInBytes) {
		return false;
	}

	return true;
}

/**
 * Changes the height and width of the video player
 *
 * @param integer $newHeight height
 * @param integer $newWidth width
 * @param string $object video player
 * @return HTML video player
 */
function izapGetReplacedHeightWidth_izap_videos($newHeight, $newWidth, $object) {
	$videodiv = preg_replace('/width=["\']\d+["\']/', 'width="' . $newWidth . '"', $object);
	$videodiv = preg_replace('/width:\d+/', 'width:'.$newWidth, $videodiv);
	$videodiv = preg_replace('/height=["\']\d+["\']/', 'height="' . $newHeight . '"', $videodiv);
	$videodiv = preg_replace('/height:\d+/', 'height:'.$newHeight, $videodiv);

	return $videodiv;
}

function izap_get_file_extension($filename) {
	if (empty($filename)) {
		return false;
	}

	return strtolower(end(explode('.', $filename)));
}

function izap_videos_is_upgrade_available() {
	require_once elgg_get_plugins_path() . "izap_videos/version.php";

	$local_version = elgg_get_plugin_setting('local_version', 'izap_videos');
	if ($local_version === false) {
		$local_version = 0;
	}

	if ($local_version == $version) {
		return false;
	} else {
		return true;
	}
}
