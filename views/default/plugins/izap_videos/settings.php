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

// show navigation tabs
echo elgg_view('izap_videos/tabs', ['tab' => 'settings']);

echo elgg_format_element('div', [], elgg_autop(elgg_view('output/url', [
	'text' => elgg_echo('izap_videos:adminSettings:resetSettings'),
	'confirm' => elgg_echo('izap_videos:adminSettings:resetSettings_confirm'),
	'class' => 'mtm elgg-button elgg-button-action',
	'is_action' => true,
])));

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('izap_videos:adminSettings:izapVideoOptions'),
	'name' => 'params[izapVideoOptions]',
	'options' => [
		elgg_echo('izap_videos:adminSettings:offServerVideos') => 'OFFSERVER',
		(extension_loaded('pdo_sqlite') ? elgg_echo('izap_videos:adminSettings:onServerVideos_okay') : elgg_echo('izap_videos:adminSettings:onServerVideos_notokay')) => 'ONSERVER',
		elgg_echo('izap_videos:adminSettings:embedCode') => 'EMBED',
	],
	'value' => izapAdminSettings_izap_videos('izapVideoOptions', ['OFFSERVER']),
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:adminSettings:izapPhpInterpreter'),
	'name' => 'params[izapPhpInterpreter]',
	'value' => izapAdminSettings_izap_videos('izapPhpInterpreter', '/usr/bin/php'),
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:adminSettings:izapVideoCommand'),
	'#help' => elgg_echo('izap_videos:adminSettings:info:convert-command'),
	'name' => 'params[izapVideoCommand]',
	'value' => izapAdminSettings_izap_videos('izapVideoCommand', '/usr/bin/ffmpeg -y -i [inputVideoPath] [outputVideoPath]'),
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:adminSettings:izapVideoThumb'),
	'name' => 'params[izapVideoThumb]',
	'value' => izapAdminSettings_izap_videos('izapVideoThumb', '/usr/bin/ffmpeg -y -i [inputVideoPath] -vframes 1 -ss 00:00:10 -an -vcodec png -f rawvideo -s 320x240 [outputImage]'),
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('izap_videos:adminSettings:izap_cron_time'),
	'#help' => elgg_echo('izap_videos:adminSettings:info:izap_cron_time'),
	'name' => 'params[izap_cron_time]',
	'options_values' => [
		'minute' => elgg_echo('izap_videos:adminSettings:minute'),
		'fiveminute' => elgg_echo('izap_videos:adminSettings:fiveminute'),
		'fifteenmin' => elgg_echo('izap_videos:adminSettings:fifteenmin'),
		'halfhour' => elgg_echo('izap_videos:adminSettings:halfhour'),
		'hourly' => elgg_echo('izap_videos:adminSettings:hourly'),
		'none' => elgg_echo('izap_videos:adminSettings:cron_off'),
	],
	'value' => izapAdminSettings_izap_videos('izap_cron_time', 'minute', false),
]);

echo elgg_view_field([
	'#type' => 'text',
	'#label' => elgg_echo('izap_videos:adminSettings:izapMaxFileSize'),
	'#help' => elgg_echo('izap_videos:adminSettings:info:izapMaxFileSize'),
	'name' => 'params[izapMaxFileSize]',
	'value' => izapAdminSettings_izap_videos('izapMaxFileSize', '5'),
]);

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('izap_videos:adminSettings:izapKeepOriginal'),
	'#help' => elgg_echo('izap_videos:adminSettings:info:izapKeepOriginal'),
	'name' => 'params[izapKeepOriginal]',
	'options' => [
		elgg_echo('option:yes') => 'YES',
	],
	'value' => izapAdminSettings_izap_videos('izapKeepOriginal', 'YES', false, true),
]);

echo elgg_view_field([
	'#type' => 'checkboxes',
	'#label' => elgg_echo('izap_videos:adminSettings:extended_sidebar_menu'),
	'#help' => elgg_echo('izap_videos:adminSettings:info:extended_sidebar_menu'),
	'name' => 'params[izapExtendedSidebarMenu]',
	'options' => [
		elgg_echo('option:yes') => 'YES',
	],
	'value' => izapAdminSettings_izap_videos('izapExtendedSidebarMenu', 'NO', false, true),
]);

echo elgg_view_field([
	'#type' => 'select',
	'#label' => elgg_echo('izap_videos:adminSettings:izap_river_thumbnails'),
	'name' => 'params[izap_river_thumbnails]',
	'options_values' => [
		'small' => elgg_echo('izap_videos:adminSettings:thumbnails_small'),
		'medium' => elgg_echo('izap_videos:adminSettings:thumbnails_medium'),
		'large' => elgg_echo('izap_videos:adminSettings:thumbnails_large'),
		'none' => elgg_echo('izap_videos:adminSettings:thumbnails_none'),
	],
	'value' => izapAdminSettings_izap_videos('izap_river_thumbnails', 'medium', false),
]);
