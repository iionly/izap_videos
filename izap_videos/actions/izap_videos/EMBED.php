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

$videoValues = $izap_videos->input($postedArray['videoEmbed'], 'embed');

if (!is_object($videoValues)) {
	register_error(elgg_echo('izap_videos:error:code:' . $videoValues));
	forward(REFERER);
}

$izap_videos->videotype = $videoValues->type;
$izap_videos->videosrc = $videoValues->videoSrc;
$izap_videos->imagesrc = 'izap_videos/embed/' . time() . '.jpg';
$izap_videos->converted = 'yes';
