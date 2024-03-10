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

// get the posted data
$params = (array) get_input('params');
$_SESSION['izapVideos'] = $params;

if ($params['guid'] == 0) {
	$izap_videos = new IzapVideos();
	$izap_videos->views = 0;
	$izap_videos->last_viewed = (int) time();
} else {
	$izap_videos = get_entity($params['guid']);
}
$izap_videos->container_guid = $params['container_guid'];
$title = $params['title'];
if (!$title) {
	$title = '';
}
$izap_videos->title = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$izap_videos->description = $params['description'];
$izap_videos->access_id = $params['access_id'];
$tags = string_to_tag_array($params['tags']);
if (is_array($tags)) {
	$izap_videos->tags = $tags;
}

switch ($params['videoType']) {
	case 'OFFSERVER':
		// if url is not valid then send it back
		if (!filter_var($params['videoUrl'], FILTER_VALIDATE_URL)) {
			return elgg_error_response(elgg_echo('izap_videos:error:notValidUrl'), REFERER);
		}

		$videoValues = $izap_videos->input($params['videoUrl'], 'url');
		if (!is_object($videoValues)) {
			if (is_integer($videoValues)) {
				return elgg_error_response(elgg_echo('izap_videos:error:code:' . $videoValues), REFERER);
			} else {
				return elgg_error_response($videoValues, REFERER);
			}
		}

		if ($params['title'] == '') {
			$izap_videos->title = $videoValues->title;
		}
		if (empty($izap_videos->title)) {
			return elgg_error_response(elgg_echo('izap_videos:error:emptyTitle'), REFERER);
		}
		if ($params['description'] == '') {
			$izap_videos->description = $videoValues->description;
		}
		if ($params['tags'] == '') {
			if ($videoValues->videoTags != '') {
				$izap_videos->tags = string_to_tag_array($videoValues->videoTags);
			}
		}
		$izap_videos->videosrc = $videoValues->videoSrc;
		$izap_videos->videotype = $videoValues->type;
		$izap_videos->imagesrc = "izap_videos/" . $videoValues->type . "/" . $videoValues->fileName;
		$izap_videos->converted = 'yes';

		$izap_videos->setFilename($izap_videos->imagesrc);
		$izap_videos->open("write");
		$izap_videos->write($videoValues->fileContent);
		$izap_videos->close();
		elgg_save_resized_image($izap_videos->getFilenameOnFilestore(), $izap_videos->getFilenameOnFilestore(), ['w' => 120, 'h' => 90, 'square' => true, 'upscale' => true]);

		break;
	case 'ONSERVER':
		$izap_videos->access_id = ACCESS_PUBLIC;
		if (empty($izap_videos->title)) {
			return elgg_error_response(elgg_echo('izap_videos:error:emptyTitle'), REFERER);
		}

		$videoValues = $izap_videos->input(
			[
				'file' => $_FILE,
				'mainArray' => 'params',
				'fileName' => 'videoFile',
			],
			'file'
		);

		if (!is_object($videoValues)) {
			return elgg_error_response(elgg_echo('izap_videos:error:code:' . $videoValues), REFERER);
		}

		if (empty($videoValues->type) || (!file_exists($videoValues->tmpFile))) {
			return elgg_error_response(elgg_echo('izap_videos:error:notUploaded'), REFERER);
		}

		$izap_videos->videotype = $videoValues->type;
		if ($videoValues->thumb) {
			$izap_videos->imagesrc = $videoValues->thumb;
		} else {
			$izap_videos->imagesrc = elgg_get_simplecache_url('izap_videos/ajax_loader.gif');
		}

		// Defining new preview attribute to be saved with the video entity
		if ($videoValues->preview) {
			$izap_videos->preview = $videoValues->preview;
		}

		$izap_videos->converted = 'no';
		$izap_videos->videofile = 'nop';
		$izap_videos->orignalfile = 'nop';

		$tmpUploadedFile = $videoValues->tmpFile;

		break;
	case 'EMBED':
		if (empty($izap_videos->title)) {
			return elgg_error_response(elgg_echo('izap_videos:error:emptyTitle'), REFERER);
		}

		if (empty($params['videoEmbed'])) {
			return elgg_error_response(elgg_echo('izap_videos:error:emptyEmbedCode'), REFERER);
		}

		$videoValues = $izap_videos->input($params['videoEmbed'], 'embed');

		if (!is_object($videoValues)) {
			return elgg_error_response(elgg_echo('izap_videos:error:code:' . $videoValues), REFERER);
		}

		$izap_videos->videotype = $videoValues->type;
		$izap_videos->videosrc = $videoValues->videoSrc;
		$izap_videos->imagesrc = 'izap_videos/embed/' . time() . '.jpg';
		$izap_videos->converted = 'yes';

		break;
	default:
		break;
}

// if we have the optional image then replace all the previous values
if ($_FILES['params']['error']['videoImage'] == 0 && in_array(strtolower(end(explode('.', $_FILES['params']['name']['videoImage']))), ['jpg', 'gif', 'jpeg', 'png'])) {
	$izap_videos->setFilename($izap_videos->imagesrc);
	$izap_videos->open("write");
	$izap_videos->write(file_get_contents($_FILES['params']['tmp_name']['videoImage']));
	$izap_videos->close();
	elgg_save_resized_image($izap_videos->getFilenameOnFilestore(), $izap_videos->getFilenameOnFilestore(), ['w' => 120, 'h' => 90, 'square' => true, 'upscale' => true]);
}

if (!$izap_videos->save()) {
	return elgg_error_response(elgg_echo('izap_videos:error:save'), REFERER);
}

// save the file info for converting it later in queue
if ($params['videoType'] == 'ONSERVER' && $params['guid'] == 0) {
	$izap_videos->videosrc = elgg_get_site_url() . 'izap_videos_files/file/' . $izap_videos->guid . '/' . elgg_get_friendly_title($izap_videos->title) . '.mp4';
	\IzapFunctions::izapSaveFileInfoForConverting_izap_videos($tmpUploadedFile, $izap_videos, $params['access_id']);
}

if ($params['guid'] == 0) {
	elgg_create_river_item([
		'view' => 'river/object/izap_videos/create',
		'action_type' => 'create',
		'subject_guid' => $izap_videos->owner_guid,
		'object_guid' => $izap_videos->guid,
	]);
}

unset($_SESSION['izapVideos']);

return elgg_ok_response('', elgg_echo('izap_videos:success:save'), $izap_videos->getURL());
