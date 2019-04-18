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

require_once(dirname(__FILE__) . '/lib/settings.php');
require_once(dirname(__FILE__) . '/lib/functions.php');
require_once(dirname(__FILE__) . '/lib/hooks.php');

elgg_register_event_handler('init', 'system', 'init_izap_videos');

/**
 * Main function that register everything
 */
function init_izap_videos() {

	// CSS
	elgg_extend_view('css/elgg', 'izap_videos/css');
	elgg_extend_view('css/admin', 'izap_videos/css');

	elgg_register_ajax_view('izap_videos/admin/getQueue');
	elgg_register_ajax_view('izap_videos/playpopup');

	// Register video.js stuff
	elgg_register_css('izap_videos_videojs_css', elgg_get_simplecache_url('izap_videos_videojs/video-js.min.css'));
	elgg_define_js('izap_videos_videojs_js', [
		'src' => elgg_get_simplecache_url('izap_videos_videojs/video.min.js'),
	]);

	// Set up the site menu
	elgg_register_menu_item('site', [
		'name' => 'videos',
		'icon' => 'video-camera',
		'href' => 'videos/all',
		'text' => elgg_echo('videos'),
	]);

	// Add admin menu item
	elgg_register_menu_item('page', [
			'name' => 'administer_utilities:izap_videos',
			'text' => elgg_echo('admin:administer_utilities:izap_videos'),
			'href' => 'admin/administer_utilities/izap_videos',
			'section' => 'administer',
			'parent_name' => 'administer_utilities',
			'context' => 'admin',
		]);
	// Add link to owner block
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'izap_videos_owner_block_menu');

	// Register for the entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'izap_videos_entity_menu_setup');

	// Register url handler
	elgg_register_plugin_hook_handler('entity:url', 'object', 'izap_videos_urlhandler');

	// Register a plugin hook to allow custom river view for comments made on videos
	elgg_register_plugin_hook_handler('view', 'river/object/comment/create', 'izap_videos_river_comment');

	// Register notification hook
	elgg_register_notification_event('object', 'izap_videos', ['create']);
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:izap_videos', 'izap_videos_notify_message');

	// Register cronjob that triggers on-site video conversion
	$period = izapAdminSettings_izap_videos('izap_cron_time');
	if ($period != 'none') {
		elgg_register_plugin_hook_handler('cron', $period, 'izap_queue_cron');
	}

	// Group videos
	elgg()->group_tools->register('izap_videos', [
		'default_on' => true,
		'label' => elgg_echo('izap_videos:group:enablevideo'),
	]);

	// Adding izap_videos widget
	elgg_register_widget_type('izap_videos', elgg_echo('izap_videos:videos'), elgg_echo('izap_videos:widget'));

	// Add index widget for Widget Manager plugin
	elgg_register_widget_type('index_latest_videos', elgg_echo("izap_videos:mostrecent"), elgg_echo('izap_videos:mostrecent:description'), ['index']);

	// Add groups widget for Widget Manager plugin
	elgg_register_widget_type('groups_latest_videos', elgg_echo("izap_videos:mostrecent"), elgg_echo('izap_videos:mostrecent:group:description'), ['groups']);

	// Register title urls for widgets
	elgg_register_plugin_hook_handler("entity:url", "object", "izap_videos_widget_urls");
	// Handle the availability of the iZAP Videos group widget
	elgg_register_plugin_hook_handler("group_tool_widgets", "widget_manager", "izap_videos_tool_widget_handler");

	// Allow liking of videos
	elgg_register_plugin_hook_handler('likes:is_likable', 'object:izap_videos', 'Elgg\Values::getTrue');

	// Register for search
	elgg_register_entity_type('object','izap_videos');

}
