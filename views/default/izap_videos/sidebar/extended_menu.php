<?php
/**
 * Extended sidebar menu entries
 */

$base = elgg_get_site_url() . 'videos/';

elgg_register_menu_item('page', [
	'name' => 'A20_izap_videos_recentlyviewed',
	'text' => elgg_echo('collection:object:izap_videos:recentlyviewed'),
	'href' => $base . 'recentlyviewed',
	'section' => 'A',
]);
elgg_register_menu_item('page', [
	'name' => 'A30_izap_videos_recentlycommented',
	'text' => elgg_echo('collection:object:izap_videos:recentlycommented'),
	'href' => $base . 'recentlycommented',
	'section' => 'A',
]);

elgg_register_menu_item('page', [
	'name' => 'B10_izap_videos_mostviewed',
	'text' => elgg_echo('collection:object:izap_videos:mostviewed'),
	'href' => $base . 'mostviewed',
	'section' => 'B',
]);
elgg_register_menu_item('page', [
	'name' => 'B20_izap_videos_mostviewedtoday',
	'text' => elgg_echo('collection:object:izap_videos:mostviewedtoday'),
	'href' => $base . 'mostviewedtoday',
	'section' => 'B',
]);
elgg_register_menu_item('page', [
	'name' => 'B30_izap_videos_mostviewedthismonth',
	'text' => elgg_echo('collection:object:izap_videos:mostviewedthismonth'),
	'href' => $base . 'mostviewedthismonth',
	'section' => 'B',
]);
elgg_register_menu_item('page', [
	'name' => 'B40_izap_videos_mostviewedlastmonth',
	'text' => elgg_echo('collection:object:izap_videos:mostviewedlastmonth'),
	'href' => $base . 'mostviewedlastmonth',
	'section' => 'B',
]);
elgg_register_menu_item('page', [
	'name' => 'B50_izap_videos_mostviewedthisyear',
	'text' => elgg_echo('collection:object:izap_videos:mostviewedthisyear'),
	'href' => $base . 'mostviewedthisyear',
	'section' => 'B',
]);

elgg_register_menu_item('page', [
	'name' => 'C10_izap_videos_mostcommented',
	'text' => elgg_echo('collection:object:izap_videos:mostcommented'),
	'href' => $base . 'mostcommented',
	'section' => 'C',
]);
elgg_register_menu_item('page', [
	'name' => 'C20_izap_videos_mostcommentedtoday',
	'text' => elgg_echo('collection:object:izap_videos:mostcommentedtoday'),
	'href' => $base . 'mostcommentedtoday',
	'section' => 'C',
]);
elgg_register_menu_item('page', [
	'name' => 'C30_izap_videos_mostcommentedthismonth',
	'text' => elgg_echo('collection:object:izap_videos:mostcommentedthismonth'),
	'href' => $base . 'mostcommentedthismonth',
	'section' => 'C',
]);
elgg_register_menu_item('page', [
	'name' => 'C40_izap_videos_mostcommentedlastmonth',
	'text' => elgg_echo('collection:object:izap_videos:mostcommentedlastmonth'),
	'href' => $base . 'mostcommentedlastmonth',
	'section' => 'C',
]);
elgg_register_menu_item('page', [
	'name' => 'C50_izap_videos_mostcommentedthisyear',
	'text' => elgg_echo('collection:object:izap_videos:mostcommentedthisyear'),
	'href' => $base . 'mostcommentedthisyear',
	'section' => 'C',
]);

if (elgg_is_active_plugin('elggx_fivestar')) {
	elgg_register_menu_item('page', [
		'name' => 'D10_izap_videos_highestrated',
		'text' => elgg_echo('collection:object:izap_videos:highestrated'),
		'href' => $base . 'highestrated',
		'section' => 'D',
	]);
	elgg_register_menu_item('page', [
		'name' => 'D20_izap_videos_highestvotecount',
		'text' => elgg_echo('collection:object:izap_videos:highestvotecount'),
		'href' => $base . 'highestvotecount',
		'section' => 'D',
	]);
	elgg_register_menu_item('page', [
		'name' => 'D30_izap_videos_recentvotes',
		'text' => elgg_echo('collection:object:izap_videos:recentlyvoted'),
		'href' => $base . 'recentvotes',
		'section' => 'D',
	]);
}
