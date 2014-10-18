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

require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
ini_set('max_execution_time', 0);
ini_set('memory_limit', izapAdminSettings_izap_videos('izapMaxFileSize') + 100 . 'M');

// only works if started from command line
if($argc > 1 && $argv[1] == 'izap' && $argv[2] == 'web') {
	izapGetAccess_izap_videos(); // get the complete access to the system
	izapRunQueue_izap_videos();
	izapRemoveAccess_izap_videos(); // remove the access from the system
}
