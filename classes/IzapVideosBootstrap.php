<?php

use Elgg\DefaultPluginBootstrap;

class IzapVideosBootstrap extends DefaultPluginBootstrap {

	public function init() {
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
			'text' => elgg_echo('collection:object:izap_videos'),
			'href' => elgg_generate_url('collection:object:izap_videos:all'),
		]);

		// Add admin menu item
		elgg_register_menu_item('page', [
			'name' => 'administer_utilities:izap_videos',
			'href' => 'admin/administer_utilities/izap_videos',
			'text' => elgg_echo('admin:administer_utilities:izap_videos'),
			'context' => 'admin',
			'parent_name' => 'administer_utilities',
			'section' => 'administer'
		]);

		// Add link to owner block
		elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'izap_videos_owner_block_menu');

		// Register for the entity menu
		elgg_register_plugin_hook_handler('register', 'menu:entity', 'izap_videos_entity_menu_setup');

		// Register for the social menu
		elgg_register_plugin_hook_handler('register', 'menu:social', 'izap_videos_social_menu_setup');

		// Add favorites tab to /videos/all /videos/mine /videos/friends /videos/favorites pages
		elgg_register_plugin_hook_handler('register', 'menu:filter:izap_videos_tabs', 'izap_videos_setup_tabs');

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
			'default_on' => false,
			'label' => elgg_echo('izap_videos:group:enablevideo'),
		]);

		// Register title urls for widgets
		elgg_register_plugin_hook_handler("entity:url", "object", "izap_videos_widget_urls");
		// Handle the availability of the iZAP Videos group widget
		elgg_register_plugin_hook_handler("group_tool_widgets", "widget_manager", "izap_videos_tool_widget_handler");

		// Allow liking of videos
		elgg_register_plugin_hook_handler('likes:is_likable', 'object:izap_videos', 'Elgg\Values::getTrue');
	}

	public function activate() {
		// save current version number
		$old_version_izap_videos = elgg_get_plugin_setting('version_izap_videos', 'izap_videos');
		$new_version_izap_videos = '3.0.0';
		if (version_compare($new_version_izap_videos, $old_version_izap_videos, '!=')) {
			// Set new version
			elgg_set_plugin_setting('version_izap_videos', $new_version_izap_videos, 'izap_videos');
		}	
	}
}
