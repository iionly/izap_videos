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

function izapLoadLib_izap_videos() {
	$base_dir = elgg_get_plugins_path() . 'izap_videos/lib';

	$files = array('settings', 'convert', 'curl', 'getFeed', 'izap_api', 'izap_sqlite', 'izap_videos', 'video_feed');

	foreach($files as $file) {
		elgg_register_library("izap_videos:$file", "$base_dir/$file.php");
		elgg_load_library("izap_videos:$file");
	}
}


/**
 * Checks if the field is null or not
 *
 * @param variable $input any variable
 * @param array $exclude in case if we want to exclude some values
 * @return boolean true if null else false
 */
function izapIsNull_izap_videos($input, $exclude = array()) {
	if (!is_array($input)) {
		$input = array($input);
	}

	if (count($input) >= 1) {
		foreach ($input as $key => $value) {
			if (!in_array($key, $exclude)) {
				if(empty($value)) {
				return true;
				}
			}
		}
	} else {
		return true;
	}

	return false;
}

/**
 * This converts the array into object
 *
 * @param array $array
 * @return object
 */
function izapArrayToObject_izap_videos($array) {
	if(!is_array($array))
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
		if(!elgg_set_plugin_setting($settingName, $pluginValues, 'izap_videos')) {
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
 * This function will check if the given id is of an izap_videos entity
 *
 * @param int $videoId video id
 * @return video entity or false
 */
function izapVideoCheck_izap_videos($videoId, $canEditCheck = false) {
	$videoId = (int)$videoId;
	if ($videoId) {
		$video = get_entity($videoId);

		if ($canEditCheck && !$video->canEdit())
			forward();

		if($video instanceof IzapVideos) {
			return $video;
		}
	}
	forward();
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
	if(!file_exists($file) || !$video) {
		return false;
	}
	$queue = new izapQueue();
	$queue->put($video, $file, $defined_access_id);
	izapTrigger_izap_videos();
}

/**
 * This function triggers the queue
 *
 * @global <type> $CONFIG
 */
function izapTrigger_izap_videos() {
	$PHPpath = izapGetPhpPath_izap_videos();
	if (!izapIsQueueRunning_izap_videos()) {
		time();
		exec($PHPpath . ' ' . elgg_get_plugins_path() . 'izap_videos/izap_convert_video.php izap web > /dev/null 2>&1 &', $output);
	}
}

/**
 * This function gives the path of PHP
 *
 * @return string path
 */
function izapGetPhpPath_izap_videos() {
	$path = izapAdminSettings_izap_videos('izapPhpInterpreter');
	$path = html_entity_decode($path);
	if (!$path) {
		$path = '';
	}
	return $path;
}

/**
 * This function checks if the queue is running or not
 *
 * @return boolean true if yes or false if no
 */
function izapIsQueueRunning_izap_videos() {
	$queue_object = new izapQueue();

	$numberof_process = $queue_object->check_process();
	if ($numberof_process) {
		return true;
	}else{
		return false;
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
	izap_access_override(array('status' => true));
}

/**
 * Remove access
 *
 * @param string $functionName
 */
function izapRemoveAccess_izap_videos() {
	izap_access_override(array('status' => false));
}


function izap_access_override($params=array()) {
	if($params['status']) {
		$func="elgg_register_plugin_hook_handler";
	} else {
		$func="elgg_unregister_plugin_hook_handler";
	}

	$func_name="izapGetAccessForAll_izap_videos";

	$func("premissions_check","all",$func_name, 9999);
	$func("container_permissions_check","all",$func_name, 9999);
	$func("permissions_check:metadata","all",$func_name, 9999);
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

function izapRunQueue_izap_videos() {
	$queue_object = new izapQueue();
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

	$admin = elgg_get_entities_from_metadata(array(
		'type' => 'user',
		'metadata_name' => 'admin',
		'metadata_value' => 1,
		'limit' => 1
	));
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
 * This function gets all the videos of a user or all users
 *
 * @param int $ownerGuid id of the user to get videos for
 * @param boolean $count Do u want the total or videos ? :)
 * @return videos or false
 */
function izapGetAllVideos_izap_videos($ownerGuid = 0, $count = false, $izapVideoType = 'object', $izapSubtype = 'izap_videos') {
	$videos = elgg_get_entities(array(
		'type' => $izapVideoType,
		'subtype' => $izapSubtype,
		'owner_guid' => $ownerGuid,
		'limit' => false,
		'count' => $count
	));
	return $videos;
}

/**
 * Wraps the string to given number of words
 *
 * @param string $string string to wrap
 * @param integer $length max length of sting
 * @return sting $string wrapped sting
 */
function izapWordWrap_izap_videos($string, $length = 300, $addEnding = false) {
	if (strlen($string) <= $length) {
		$string = $string; //do nothing
	} else {
		$string = wordwrap(str_replace("\n", "", $string), $length);
		$string = substr($string, 0, strpos($string, "\n"));

		if ($addEnding) {
			$string .= '...';
			if (is_string($addEnding)) {
				$string .= '<a href="' . $addEnding.'">' . elgg_echo('izap_videos:readmore') . '</a>';
			}
		}
	}

	return $string;
}

/**
 * Manages the url for embedding the videos
 *
 * @param string $text all text
 * @return string
 */
function izapParseUrls_izap_videos($text) {
	return preg_replace_callback('/[^movie=](?<!=["\'])((ht|f)tps?:\/\/[^\s\r\n\t<>"\'\!\(\)]+)/i',
		create_function(
			'$matches',
			'$url = $matches[1];
			$urltext = str_replace("/", "/<wbr />", $url);
			return "<a href=\"$url\" style=\"text-decoration:underline;\">$urltext</a>";
			'
		), $text);
}

/**
 * Gets the videos that were not converted
 *
 * @return boolean or entites
 */
function izapGetNotConvertedVideos_izap_videos() {
	$not_converted_videos = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'izap_videos',
		'metadata_name' => 'converted',
		'metadata_value' => 'no',
		'limit' => false
	));
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
	$units = array('B', 'KB', 'MB', 'GB', 'TB');

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
	$queue_object = new izapQueue();
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
	if(!$query) {
		return false;
	}
	$query = 'UPDATE ' . $db_prefix . 'entities SET access_id = ' . $accessId . ' WHERE guid = ' . $entity_guid;
	update_data($query);
	return $query;
}


function izap_is_my_favorited($video) {
	$users = (array)$video->favorited_by;
	$key = array_search(elgg_get_logged_in_user_guid(), $users);
	if ($key !== false) {
		return true;
	}

	return false;
}

function izap_remove_favorited($video, $user_guid = 0) {
	$users = (array) $video->favorited_by;

	if(!$user_guid) {
		$user_guid = elgg_get_logged_in_user_guid();
	}

	$key = array_search($user_guid, $users);

	if($key !== false) {
		unset($users[$key]);
	}

	izapGetAccess_izap_videos();
	$video->favorited_by = array_unique($users);
	izapRemoveAccess_izap_videos();

	return true;
}
