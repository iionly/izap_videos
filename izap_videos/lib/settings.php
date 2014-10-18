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

$IZAPSETTINGS = new stdClass();

$IZAPSETTINGS->filesPath = elgg_get_site_url() . 'izap_videos_files/';
$IZAPSETTINGS->playerPath = elgg_get_site_url() . 'mod/izap_videos/player/izap_player.swf';
$IZAPSETTINGS->allowedExtensions = array('avi', 'flv', '3gp', 'mp4', 'wmv', 'mpg', 'mpeg');
