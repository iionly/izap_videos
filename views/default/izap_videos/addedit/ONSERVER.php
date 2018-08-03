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

$maxFileSize = (int) izapAdminSettings_izap_videos('izapMaxFileSize');

echo elgg_view_field([
	'#type' => 'file',
	'#label' => elgg_echo('izap_videos:addEditForm:videoFile') . ' ' . elgg_echo('izap_videos:addEditForm:maxFilesize', [$maxFileSize]),
	'#help' => elgg_echo('izap_videos:ONSERVER:supported_formats'),
	'name' => 'params[videoFile]',
	'value' => $vars['loaded_data']->videoFile,
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'params[videoType]',
	'value' => 'ONSERVER',
]);
