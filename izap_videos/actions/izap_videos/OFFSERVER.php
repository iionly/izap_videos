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

$videoValues = $izap_videos->input($postedArray['videoUrl'], 'url');

if (!is_object($videoValues)) {
	if (is_integer($videoValues)) {
		register_error(elgg_echo('izap_videos:error:code:' . $videoValues));
	} else {
		register_error($videoValues);
	}
	forward(REFERER);
}

if ($postedArray['title'] == '') {
	$izap_videos->title = $videoValues->title;
}

if ($postedArray['description'] == '') {
	$izap_videos->description = $videoValues->description;
}

if ($postedArray['tags'] == '') {
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
$thumb = get_resized_image_from_existing_file($izap_videos->getFilenameOnFilestore(),120,90, true);
$izap_videos->setFilename($izap_videos->imagesrc);
$izap_videos->open("write");
$izap_videos->write($thumb);
