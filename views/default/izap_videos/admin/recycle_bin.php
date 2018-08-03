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

$buggy_videos_object = new IzapQueue();
$buggy_videos = $buggy_videos_object->get_from_trash();

$content = elgg_format_element('h3', ['align' => 'center'], elgg_echo('izap_videos:error_videos'));

if ($buggy_videos) {
	$form_vars = [
		'action' => 'action/izap_videos/admin/recycle_delete',
	];
	$body_vars = [
		'buggy_videos' => $buggy_videos,
	];
	$content .= elgg_view_form('izap_videos/admin/recycle_delete', $form_vars, $body_vars);
} else {
	$content .= elgg_format_element('div', ['align' => 'center'], elgg_view('output/longtext', ['value' => elgg_echo('izap_videos:error_videos:none'), 'class' => 'mtm']));
}

echo elgg_format_element('div', [], $content);
