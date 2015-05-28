<?php

global $IZAPSETTINGS;

// tabs array
$activated_options = izapGetVideoOptions_izap_videos();
$tab = get_input('tab', 'settings');
if(!in_array('ONSERVER', $activated_options)) {

	echo elgg_view('navigation/tabs', array(
		'tabs' => array(
			array(
				'text' => elgg_echo('settings'),
				'href' => '/admin/administer_utilities/izap_videos',
				'selected' => ($tab == 'settings'),
			),
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_api_keys'),
				'href' => '/admin/administer_utilities/izap_videos?tab=api_keys',
				'selected' => ($tab == 'api_keys'),
			),
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_server_analysis'),
				'href' => '/admin/administer_utilities/izap_videos?tab=server_analysis',
				'selected' => ($tab == 'server_analysis'),
			),
		)
	));

} else {

	$queue_object_status = new izapQueue();
	$count_queue = $queue_object_status->count();
	$count_trash = $queue_object_status->count_trash();

	echo elgg_view('navigation/tabs', array(
		'tabs' => array(
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_settings'),
				'href' => '/admin/administer_utilities/izap_videos',
				'selected' => ($tab == 'settings'),
			),
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_api_keys'),
				'href' => '/admin/administer_utilities/izap_videos?tab=api_keys',
				'selected' => ($tab == 'api_keys'),
			),
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_queue_status', array($count_queue)),
				'href' => '/admin/administer_utilities/izap_videos?tab=queue_status',
				'selected' => ($tab == 'queue_status'),
			),
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_recycle_bin', array($count_trash)),
				'href' => '/admin/administer_utilities/izap_videos?tab=recycle_bin',
				'selected' => ($tab == 'recycle_bin'),
			),
			array(
				'text' => elgg_echo('izap_videos:adminSettings:tabs_server_analysis'),
				'href' => '/admin/administer_utilities/izap_videos?tab=server_analysis',
				'selected' => ($tab == 'server_analysis'),
			),
		)
	));
}

switch ($tab) {
	case 'api_keys':
		echo elgg_view('admin/administer_utilities/izap_videos/api_keys');
		break;

	case 'queue_status':
		echo elgg_view('admin/administer_utilities/izap_videos/queue_status');
		break;

	case 'recycle_bin':
		echo elgg_view('admin/administer_utilities/izap_videos/recycle_bin');
		break;

	case 'server_analysis':
		echo elgg_view('admin/administer_utilities/izap_videos/server_analysis');
		break;

	default:
	case 'settings':
		echo elgg_view('admin/administer_utilities/izap_videos/settings');
		break;
}
