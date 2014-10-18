<?php

echo "<div class='mtl mbl'>" . elgg_view("output/confirmlink",array(
	'href' => elgg_get_site_url() . "izap_videos/admin/resetSettings",
	'text' => elgg_echo('izap_videos:adminSettings:resetSettings'),
	'confirm' => elgg_echo('izap_videos:adminSettings:resetSettings_confirm'),
	'class' => 'elgg-button elgg-button-action',
)) . "</div>";

echo elgg_view_form('izap_videos/admin/settings');
