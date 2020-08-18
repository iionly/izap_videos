<?php

if (izap_videos_is_upgrade_available()) {
	echo elgg_format_element('div', ['class' => 'elgg-admin-notices'], elgg_autop(elgg_view('output/url', [
		'text' => elgg_echo('izap_videos:upgrade'),
		'href' => 'action/izap_videos/admin/upgrade',
		'is_action' => true,
	])));
}

$selected_tab = elgg_extract('tab', $vars);

$base_url = 'admin/administer_utilities/izap_videos';

$tabs = [
	'settings' => [
		'href' => 'admin/plugin_settings/izap_videos',
	],
	'api_keys' => [],
	'server_analysis' => [],
];

$activated_options = izapGetVideoOptions_izap_videos();
$count_queue = $count_trash = 0;
if (in_array('ONSERVER', $activated_options)) {
	$tabs['queue_status'] = [];
	$tabs['recycle_bin'] = [];

	$queue_object_status = new IzapQueue();
	$count_queue = $queue_object_status->count();
	$count_trash = $queue_object_status->count_trash();
} else if (in_array($selected_tab, ['queue_status', 'recycle_bin'])) {
	$selected_tab = 'server_analysis';
}

$params = [
	'tabs' => [],
];

foreach ($tabs as $tab => $tab_settings) {

	$href = elgg_extract('href', $tab_settings);
	if (empty($href)) {
		$href = elgg_http_add_url_query_elements($base_url, [
			'tab' => $tab,
		]);
	}

	$args = [];
	if ($tab == 'queue_status') {
		$args = [$count_queue];
	} else if ($tab == 'recycle_bin') {
		$args = [$count_trash];
	}

	$params['tabs'][] = [
		'text' => elgg_echo("izap_videos:adminSettings:tabs_{$tab}", $args),
		'href' => $href,
		'selected' => ($tab === $selected_tab),
	];
}

echo elgg_view('navigation/tabs', $params);
