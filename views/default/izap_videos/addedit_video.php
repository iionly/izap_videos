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

elgg_require_js('izap_videos/video_form');

// get page owner
$page_owner = elgg_get_page_owner_entity();

// get entity
$video = elgg_extract('entity', $vars, false);

// get the add options
$options = \IzapFunctions::izapGetVideoOptions_izap_videos();

// get the selected option
$selectedOption = get_input('option', '');
if (empty($selectedOption) || !in_array($selectedOption, $options)) {
	$selectedOption = $options[0];
}

// get values from session if any
$izapLoadedValues = new stdClass();
if (isset($_SESSION['izapVideos']) && !empty($_SESSION['izapVideos'])) {
	$izapLoadedValues = \IzapFunctions::izapArrayToObject_izap_videos($_SESSION['izapVideos']);
}
$izapLoadedValues->access_id = null;

if (!$video) {  // if it is a new video
	echo elgg_view('izap_videos/addedit_tabs', ['options' => $options, 'selected' => $selectedOption]);
}

$form_vars = [
	'action' => 'action/izap_videos/addEdit',
	'enctype' => 'multipart/form-data',
	'class' => 'elgg-form-settings',
	'id' => 'video_form',
];
$body_vars = [
	'video' => $video,
	'selectedOption' => $selectedOption,
	'options' => $options,
	'izapLoadedValues' => $izapLoadedValues,
	'page_owner' => $page_owner,
];
echo elgg_view_form('izap_videos/addedit', $form_vars, $body_vars);
