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

$options = $vars['options'];
$selectedTab = $vars['selected'];

$tabs_array = array();
foreach ($options as $addOption) {
	$tabs_array[] = array(
		'text' => elgg_echo('izap_videos:addEditForm:' . $addOption),
		'href' => "?option={$addOption}",
		'selected' => ($addOption == $selectedTab),
	);
}

echo elgg_view('navigation/tabs', array('tabs' => $tabs_array));
