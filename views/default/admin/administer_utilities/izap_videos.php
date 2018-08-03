<?php

$tab = get_input('tab', 'queue_status');

echo elgg_view('izap_videos/tabs', [
	'tab' => $tab,
]);

if (elgg_view_exists("izap_videos/admin/{$tab}")) {
	echo elgg_view("izap_videos/admin/{$tab}");
}
