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
$postedArray = get_input('izap');
$_SESSION['izapVideos'] = $postedArray;

if ($postedArray['guid'] == 0) {
	$izap_videos = new IzapVideos();
} else {
	$izap_videos = get_entity($postedArray['guid']);
}
$izap_videos->container_guid = $postedArray['container_guid'];
$izap_videos->title = $postedArray['title'];
$izap_videos->description = $postedArray['description'];
$izap_videos->access_id = $postedArray['access_id'];
$tags = string_to_tag_array($postedArray['tags']);
if(is_array($tags)) {
	$izap_videos->tags = $tags;
}
$izap_videos->video_views = 1;

switch ($postedArray['videoType']) {
	case 'OFFSERVER':
		// if url is not valid then send it back
		if (!filter_var($postedArray['videoUrl'], FILTER_VALIDATE_URL)) {
			register_error(elgg_echo('izap_videos:error:notValidUrl'));
			forward(REFERER);
		}
		include_once(dirname(__FILE__) . '/OFFSERVER.php');
		break;
	case 'ONSERVER':
		$izap_videos->access_id = ACCESS_PUBLIC;
		if (empty($izap_videos->title)) {
			register_error(elgg_echo('izap_videos:error:emptyTitle'));
			forward(REFERER);
		}
		include_once(dirname(__FILE__) . '/ONSERVER.php');
		break;
	case 'EMBED':
		if (empty($izap_videos->title)) {
			register_error(elgg_echo('izap_videos:error:emptyTitle'));
			forward(REFERER);
		}

		if (empty($postedArray['videoEmbed'])) {
			register_error(elgg_echo('izap_videos:error:emptyEmbedCode'));
			forward(REFERER);
		}
		include_once (dirname(__FILE__) . '/EMBED.php');
		break;
	default:
		break;
}

// if we have the optional image then replace all the previous values
if ($_FILES['izap']['error']['videoImage'] == 0 && in_array(strtolower(end(explode('.', $_FILES['izap']['name']['videoImage']))), array('jpg', 'gif', 'jpeg', 'png'))) {
	$izap_videos->setFilename($izap_videos->imagesrc);
	$izap_videos->open("write");
	$izap_videos->write(file_get_contents($_FILES['izap']['tmp_name']['videoImage']));

	$thumb = get_resized_image_from_existing_file($izap_videos->getFilenameOnFilestore(), 120, 90, true);

	$izap_videos->setFilename($izap_videos->imagesrc);
	$izap_videos->open("write");
	$izap_videos->write($thumb);
}

if (!$izap_videos->save()) {
	register_error(elgg_echo('izap_videos:error:save'));
	forward(REFERER);
}

// save the file info for converting it later in queue
if ($postedArray['videoType'] == 'ONSERVER' && $postedArray['guid'] == 0) {
	$izap_videos->videosrc = elgg_get_site_url() . 'izap_videos_files/file/' . $izap_videos->guid . '/' . elgg_get_friendly_title($izap_videos->title) . '.flv';
	if (izap_get_file_extension($tmpUploadedFile) != 'flv') { // will only send to queue if it is not flv
		izapSaveFileInfoForConverting_izap_videos($tmpUploadedFile, $izap_videos, $postedArray['access_id']);
	}
}

if ($postedArray['guid'] == 0) {
	elgg_create_river_item(array(
		'view' => 'river/object/izap_videos/create',
		'action_type' => 'create',
		'subject_guid' => $izap_videos->owner_guid,
		'object_guid' => $izap_videos->guid,
	));

}

system_message(elgg_echo('izap_videos:success:save'));
unset($_SESSION['izapVideos']);
forward($izap_videos->getURL());
