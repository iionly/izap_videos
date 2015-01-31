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

$videoValues = $izap_videos->input(
	array(
		'file' => $_FILE,
		'mainArray' => 'izap',
		'fileName' => 'videoFile',
	),
	'file'
);

if (!is_object($videoValues)) {
	register_error(elgg_echo('izap_videos:error:code:' . $videoValues));
	forward(REFERER);
}

if (empty($videoValues->type) || ($videoValues->is_flv !='yes' && !file_exists($videoValues->tmpFile))) {
	register_error(elgg_echo('izap_videos:error:notUploaded'));
	forward(REFERER);
}

$izap_videos->videotype = $videoValues->type;
if ($videoValues->thumb) {
	$izap_videos->imagesrc = $videoValues->thumb;
} else {
	$izap_videos->imagesrc = elgg_get_site_url() . '_graphics/ajax_loader.gif';
}

// Defining new preview attribute to be saved with the video entity
if ($videoValues->preview){
	$izap_videos->preview = $videoValues->preview;
}

if($videoValues->is_flv != 'yes') {
	$izap_videos->converted = 'no';
	$izap_videos->videofile = 'nop';
	$izap_videos->orignalfile = 'nop';
}
$tmpUploadedFile = $videoValues->tmpFile;
