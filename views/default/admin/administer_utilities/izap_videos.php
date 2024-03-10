<?php

$activated_options = \IzapFunctions::izapGetVideoOptions_izap_videos();
if (in_array('ONSERVER', $activated_options)) {
	$default_tab = 'queue_status';
} else {
	$default_tab = 'server_analysis';
}

$tab = get_input('tab', $default_tab);

echo elgg_view('izap_videos/tabs', [
	'tab' => $tab,
]);

if (elgg_view_exists("izap_videos/admin/{$tab}")) {
	echo elgg_view("izap_videos/admin/{$tab}");
}
