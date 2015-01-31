<?php

if (izap_videos_is_upgrade_available()) {
	echo '<div class="mtl elgg-admin-notices">';
	echo '<p>';
	echo elgg_view('output/url', array(
		'text' => elgg_echo('izap_videos:upgrade'),
		'href' => 'action/izap_videos/admin/upgrade',
		'is_action' => true,
	));
	echo '</p>';
	echo '</div>';
}

echo "<div class='mtl mbl'>" . elgg_view("output/confirmlink",array(
	'href' => elgg_get_site_url() . "izap_videos/admin/resetSettings",
	'text' => elgg_echo('izap_videos:adminSettings:resetSettings'),
	'confirm' => elgg_echo('izap_videos:adminSettings:resetSettings_confirm'),
	'class' => 'elgg-button elgg-button-action',
)) . "</div>";

echo elgg_view_form('izap_videos/admin/settings');
