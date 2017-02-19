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

$form = "<div class='mbl'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapVideoOptions') . "</label><br>";
$form .=  elgg_view('input/checkboxes', array(
			'name' => 'izap[izapVideoOptions]',
			'options' => array(
				elgg_echo('izap_videos:adminSettings:offServerVideos') => 'OFFSERVER',
				(extension_loaded('pdo_sqlite') ? elgg_echo('izap_videos:adminSettings:onServerVideos_okay') : elgg_echo('izap_videos:adminSettings:onServerVideos_notokay')) => 'ONSERVER',
				elgg_echo('izap_videos:adminSettings:embedCode') => 'EMBED',
			),
			'value' => izapAdminSettings_izap_videos('izapVideoOptions', array('OFFSERVER')),
));
$form .= "</div>";

$form .= "<div>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapPhpInterpreter') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapPhpInterpreter]',
			'value' => izapAdminSettings_izap_videos('izapPhpInterpreter', '/usr/bin/php'),
));
$form .= "</div>";

$form .= "<div>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapVideoCommand') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapVideoCommand]',
			'value' => izapAdminSettings_izap_videos('izapVideoCommand', '/usr/bin/ffmpeg -y -i [inputVideoPath] [outputVideoPath]'),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:convert-command'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div class='mbl'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapVideoThumb') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapVideoThumb]',
			'value' => izapAdminSettings_izap_videos('izapVideoThumb', '/usr/bin/ffmpeg -y -i [inputVideoPath] -vframes 1 -ss 00:00:10 -an -vcodec png -f rawvideo -s 320x240 [outputImage]'),
));
$form .= "</div>";

$form .= "<div>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapBorderColor1') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapBorderColor1]',
			'value' => izapAdminSettings_izap_videos('izapBorderColor1', '4787b8'),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:bg-color'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapBorderColor2') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapBorderColor2]',
			'value' => izapAdminSettings_izap_videos('izapBorderColor2', 'FFFFFF'),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:bg-color'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div class='mbl'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapBorderColor3') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapBorderColor3]',
			'value' => izapAdminSettings_izap_videos('izapBorderColor3', 'FFFFFF'),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:bg-color'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div class='mbm'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izap_cron_time') . "</label><br>";
$form .= elgg_view('input/select', array(
			'name' => 'izap[izap_cron_time]',
			'options_values' => array(
				'minute' => elgg_echo('izap_videos:adminSettings:minute'),
				'fiveminute' => elgg_echo('izap_videos:adminSettings:fiveminute'),
				'fifteenmin' => elgg_echo('izap_videos:adminSettings:fifteenmin'),
				'halfhour' => elgg_echo('izap_videos:adminSettings:halfhour'),
				'hourly' => elgg_echo('izap_videos:adminSettings:hourly'),
				'none' => elgg_echo('izap_videos:adminSettings:cron_off'),
			),
			'value' => izapAdminSettings_izap_videos('izap_cron_time', 'minute', false),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:izap_cron_time'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div class='mbm'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapMaxFileSize') . "</label><br>";
$form .= elgg_view('input/text', array(
			'name' => 'izap[izapMaxFileSize]',
			'value' => izapAdminSettings_izap_videos('izapMaxFileSize', '5'),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:izapMaxFileSize'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div class='mbm'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izapKeepOriginal') . "</label><br>";
$form .= elgg_view('input/checkboxes', array(
			'name' => 'izap[izapKeepOriginal]',
			'options' => array(
				elgg_echo('izap_videos:adminSettings:keep-original') => 'YES',
			),
			'value' => izapAdminSettings_izap_videos('izapKeepOriginal', 'YES', false, true),
));
$form .= elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:info:izapKeepOriginal'), 'class' => 'elgg-subtext'));
$form .= "</div>";

$form .= "<div class='mbl'>";
$form .= "<label>" . elgg_echo('izap_videos:adminSettings:izap_river_thumbnails') . "</label><br>";
$form .= elgg_view('input/select', array(
			'name' => 'izap[izap_river_thumbnails]',
			'options_values' => array(
				'small' => elgg_echo('izap_videos:adminSettings:thumbnails_small'),
				'medium' => elgg_echo('izap_videos:adminSettings:thumbnails_medium'),
				'large' => elgg_echo('izap_videos:adminSettings:thumbnails_large'),
				'none' => elgg_echo('izap_videos:adminSettings:thumbnails_none'),
			),
			'value' => izapAdminSettings_izap_videos('izap_river_thumbnails', 'medium', false),
));
$form .= "</div>";


$form .= elgg_view('input/submit', array('value' => elgg_echo('izap_videos:adminSettings:save')));

echo $form;
