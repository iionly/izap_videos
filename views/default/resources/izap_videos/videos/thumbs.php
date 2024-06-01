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

$guid = (int) get_input("id");

if (!$guid) {
	$guid = (int) current(explode('.', get_input("file")));
}

// if nothing found yet..
if (!$guid) {
	$guid = (int) elgg_extract('videoID', $vars);
}

$what = (string) elgg_extract('what', $vars);

$izap_videos = 0;
if ($guid) {
	$izap_videos = get_entity($guid);
}
$contents = '';
if ($izap_videos && ($izap_videos instanceof \IzapVideos)) {
	// check what is needed
	if ($what == 'image') {
		$filename = $izap_videos->imagesrc;
	} elseif (!isset($what) || empty($what) || $what == 'file') {
		$filename = $izap_videos->videofile;
	}

	// only works if there is some file name
	if ($filename != '') {
		$fileHandler = new \ElggFile();
		$fileHandler->owner_guid = $izap_videos->owner_guid;
		$fileHandler->setFilename($filename);
		
		if ($what == 'image') {
			$contents = elgg_get_inline_url($fileHandler);
		} elseif (!isset($what) || empty($what) || $what == 'file') {
			$contents = elgg_get_download_url($fileHandler);
		}
	}
}

if (!$contents) {
	$contents = elgg_get_simplecache_url("izap_videos/izapdesign_logo.gif");
}

$forward = new \Elgg\Exceptions\HttpException();
$forward->setRedirectUrl($contents);
throw $forward;
