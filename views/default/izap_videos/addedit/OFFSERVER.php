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

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:addEditForm:videoUrl'),
	'#help' => elgg_echo('izap_videos:OFFSERVER:supported_sites'),
	'name' => 'params[videoUrl]',
	'value' => isset($vars['loaded_data']->videoUrl) ? $vars['loaded_data']->videoUrl : '',
	'required' => true,
]);

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'params[videoType]',
	'value' => 'OFFSERVER',
]);
