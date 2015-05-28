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

global $IZAPSETTINGS;

$guid = get_input("id");

if(!$guid) {
	$guid = current(explode('.', get_input("file")));
}

// if nothing found yet..
if (!$guid) {
	$guid = get_input('videoID');
}

$what = get_input("what");
$izap_videos = izapVideoCheck_izap_videos($guid);

if ($izap_videos) {
	// check what is needed
	if ($what == 'image') {
		$filename = $izap_videos->imagesrc;
	} elseif (!isset($what) || empty($what) || $what == 'file') {
		$filename = $izap_videos->videofile;
	}

	// only works if there is some file name
	if ($filename != '') {
		$fileHandler = new ElggFile();
		$fileHandler->owner_guid = $izap_videos->owner_guid;
		$fileHandler->setFilename($filename);
		if (file_exists($fileHandler->getFilenameOnFilestore())) {
			$contents = $fileHandler->grabFile();
		}
	}

	if ($contents == '') {
		$contents = file_get_contents(elgg_get_plugins_path() . 'izap_videos/_graphics/izapdesign_logo.gif');
	}

	if ($what == 'image') {
		$type = 'image/jpeg';
	} elseif (!isset($what) || empty($what) || $what == 'file') {
		$type = 'application/x-flv';
	}

	header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', strtotime("+10 days")), true);
	header("Pragma: public");
	header("Cache-Control: public");
	header("Content-Length: " . strlen($contents));
    header("Content-Type: {$type}");

	echo $contents;
}