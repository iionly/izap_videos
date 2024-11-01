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

class IzapFunctions {

	/**
	* This converts the array into object
	*
	* @param array $array
	* @return object
	*/
	public static function izapArrayToObject_izap_videos($array) {
		if (!is_array($array)) {
			return false;
		}

		$obj = new stdClass();
		foreach ($array as $key => $value) {
			if ($key != '' && $value != '') {
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
	public static function izapAdminSettings_izap_videos($settingName, $values = '', $override = false, $makeArray = false) {
		// get the old value
		$oldSetting = elgg_get_plugin_setting($settingName, 'izap_videos');

		if (isset($values) && is_array($values)) {
			$pluginValues = implode('|', $values);
		} else {
			$pluginValues = $values;
		}
		// if it is not set yet
		if (empty($oldSetting) || $override) {
			$plugin = elgg_get_plugin_from_id('izap_videos');
			if (!$plugin->setSetting($settingName, $pluginValues)) {
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
	public static function izapGetVideoOptions_izap_videos() {
		$videoOptions = self::izapAdminSettings_izap_videos('izapVideoOptions', '', false, true);
		return $videoOptions;
	}

	/**
	* This function saves the entry for futher processing
	* @param string $file main filepath
	* @param int $videoId video guid
	* @param int $ownerGuid owner guid
	* @param int $accessId access id to be used after completion of encoding of video
	*/
	public static function izapSaveFileInfoForConverting_izap_videos($file, $video, $defined_access_id = 2) {
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
	public static function izapIsQueueRunning_izap_videos() {
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
	public static function izapTrigger_izap_videos() {
		if (!(self::izapIsQueueRunning_izap_videos())) {
			ini_set('max_execution_time', 0);
			ini_set('memory_limit', self::izapAdminSettings_izap_videos('izapMaxFileSize') + 100 . 'M');

			elgg_call(ELGG_IGNORE_ACCESS, function() {
				self::izapRunQueue_izap_videos();	
			});
		}
	}

	/**
	* Resets queue
	*
	* @return boolean
	*/
	public static function izapResetQueue_izap_videos() {
	return self::izapAdminSettings_izap_videos('isQueueRunning', 'no', true);
	}

	/**
	* Clears queue and resets it
	*
	* @return boolean
	*/
	public static function izapEmptyQueue_izap_videos() {
		$pending_videos = self::izapGetNotConvertedVideos_izap_videos();
		if ($pending_videos) {
			foreach($pending_videos as $video) {
				$video->delete();
			}
		}

		return self::izapResetQueue_izap_videos();
	}

	/**
	* This function returns the FFmpeg video converting command
	*
	* @return string path
	*/
	public static function izapGetFfmpegVideoConvertCommand_izap_videos() {
		$path = self::izapAdminSettings_izap_videos('izapVideoCommand');
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
	public static function izapGetFfmpegVideoImageCommand_izap_videos() {
		$path = self::izapAdminSettings_izap_videos('izapVideoThumb');
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
	public static function izapConvertVideo_izap_videos($file, $videoId, $videoTitle, $videoUrl, $ownerGuid, $accessId = 2) {
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
				if (self::izapAdminSettings_izap_videos('izapKeepOriginal') == 'YES') {
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

		$adminGuid = self::izapGetSiteAdmin_izap_videos(true);
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

	public static function izapRunQueue_izap_videos() {
		$queue_object = new IzapQueue();
		$queue = $queue_object->fetch_videos();
		if (isset($queue) && is_array($queue)) {
			foreach($queue as $pending) {
				$converted = self::izapConvertVideo_izap_videos($pending['main_file'], $pending['guid'], $pending['title'], $pending['url'], $pending['owner_id']);
				if (!$converted) {
					$queue_object->move_to_trash($pending['guid']);
				}
				$queue_object->delete($pending['guid']);
				self::izap_update_all_defined_access_id($pending['guid'], $pending['access_id']);
			}
			// re-check if there are new videos in the queue
			if ($queue_object->count() > 0) {
				self::izapRunQueue_izap_videos();
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
	public static function izapGetSiteAdmin_izap_videos($guid = false) {
		$admin = elgg_get_admins([
			'limit' => 1,
		]);

		if ($guid) {
			return (int) $admin->guid;
		} else {
			return $admin;
		}

		return false;
	}

	/**
	* Gets the videos that were not converted
	*
	* @return boolean or entites
	*/
	public static function izapGetNotConvertedVideos_izap_videos() {
		$not_converted_videos = elgg_get_entities([
			'type' => 'object',
			'subtype' => \IzapVideos::SUBTYPE,
			'metadata_name' => 'converted',
			'metadata_value' => 'no',
			'limit' => false,
		]);
		if ($not_converted_videos) {
			return $not_converted_videos;
		}

		return false;
	}

	public static function izapReadableSize_izap_videos($inputSize) {
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
	public static function izapFormatBytes($bytes, $precision = 2) {
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
	public static function izap_count_queue() {
		$queue_object = new IzapQueue();
		return $queue_object->count();
	}

	public static function izap_get_video_name_prefix() {
		$domain = elgg_get_site_entity()->getDomain();
		$domain = preg_replace('/[^A-Za-z0-9]+/','_',$domain);

		return $domain . '_izap_videos_';
	}

	public static function izap_get_friendly_title(string $title): string {
		// titles are often stored HTML encoded
		$title = html_entity_decode($title ?? '', ENT_QUOTES, 'UTF-8');
	
		$title = \Elgg\Translit::urlize($title);

		return $title;
	}

	//Hack to correct the access id of the uploaded video.
	public static function izap_update_all_defined_access_id($guid, $accessId = ACCESS_PUBLIC) {
		// update entity
		$qb = \Elgg\Database\Update::table('entities');
		$qb->set("access_id", $qb->param($accessId, ELGG_VALUE_INTEGER))->where($qb->compare("guid", "=", $guid));
		return elgg()->db->updateData($qb);
	}

	public static function izap_is_my_favorited($video) {
		$users = (array) $video->favorited_by;
		$key = array_search(elgg_get_logged_in_user_guid(), $users);
		if ($key !== false) {
			return true;
		}

		return false;
	}

	public static function izap_remove_favorited($video, $user_guid = 0) {
		$users = (array) $video->favorited_by;

		if (!$user_guid) {
			$user_guid = elgg_get_logged_in_user_guid();
		}

		$key = array_search($user_guid, $users);

		if ($key !== false) {
			unset($users[$key]);
		}

		elgg_call(ELGG_IGNORE_ACCESS, function() use ($video, $users) {
			$video->favorited_by = array_unique($users);
		});

		return true;
	}

	/**
	* Returns the file name, that ffmpeg can operate
	*
	* @param string $fileName file name
	* @return string all formated file name
	*/
	public static function izapGetFriendlyFileName_izap_videos($fileName) {
		$new_name = self::izap_get_video_name_prefix();
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
	public static function izapSupportedVideos_izap_videos($videoFileName) {
		$supportedFormats = ['avi', 'flv', '3gp', 'mp4', 'wmv', 'mpg', 'mpeg'];
		$extension = self::izap_get_file_extension($videoFileName);
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
	public static function izapCheckFileSize_izap_videos($fileSize) {
		$maxFileSize = (int) self::izapAdminSettings_izap_videos('izapMaxFileSize');
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
	public static function izapGetReplacedHeightWidth_izap_videos($newHeight, $newWidth, $object) {
		$videodiv = preg_replace('/width=["\']\d+["\']/', 'width="' . $newWidth . '"', $object);
		$videodiv = preg_replace('/width:\d+/', 'width:'.$newWidth, $videodiv);
		$videodiv = preg_replace('/height=["\']\d+["\']/', 'height="' . $newHeight . '"', $videodiv);
		$videodiv = preg_replace('/height:\d+/', 'height:'.$newHeight, $videodiv);

		return $videodiv;
	}

	public static function izap_get_file_extension($filename) {
		if (empty($filename)) {
			return false;
		}

		return strtolower(end(explode('.', $filename)));
	}

	// public static function izap_videos_is_upgrade_available() {
	// 	require_once elgg_get_plugins_path() . "izap_videos/version.php";
 // 
	// 	$local_version = elgg_get_plugin_setting('local_version', 'izap_videos');
	// 	if ($local_version === false) {
	// 		$local_version = 0;
	// 	}
 // 
	// 	if ($local_version == $version) {
	// 		return false;
	// 	} else {
	// 		return true;
	// 	}
	// }
}
