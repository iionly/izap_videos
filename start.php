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
		'href' => 'videos/all',
		'text' => elgg_echo('videos'),
	]);

	// Add admin menu item
	elgg_register_admin_menu_item('administer', 'izap_videos', 'administer_utilities');

	// Add link to owner block
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'izap_videos_owner_block_menu');

	// Register for the entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'izap_videos_entity_menu_setup');

	// Register pagehandler
	elgg_register_page_handler('videos', 'izap_videos_pagehandler');
	elgg_register_page_handler('izap_videos_files', 'izap_videos_files_pagehandler');

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
	add_group_tool_option('izap_videos', elgg_echo('izap_videos:group:enablevideo'), true);
	elgg_extend_view('groups/tool_latest', 'izap_videos/group_module');

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

	// Register some actions
	elgg_register_action("izap_videos/settings/save", dirname(__FILE__) . '/actions/izap_videos/settings/save.php');
	elgg_register_action('izap_videos/admin/api_keys', dirname(__FILE__) . "/actions/izap_videos/admin/api_keys.php", 'admin');
	elgg_register_action('izap_videos/admin/resetSettings', dirname(__FILE__) . "/actions/izap_videos/admin/resetSettings.php", 'admin');
	elgg_register_action('izap_videos/admin/recycle', dirname(__FILE__) . "/actions/izap_videos/admin/recycle.php", 'admin');
	elgg_register_action('izap_videos/admin/recycle_delete', dirname(__FILE__) . "/actions/izap_videos/admin/recycle_delete.php", 'admin');
	elgg_register_action('izap_videos/admin/reset', dirname(__FILE__) . "/actions/izap_videos/admin/reset.php", 'admin');
	// Upgrade action not needed for the time being (old upgrade stuff removed with version 2.3.4)
	//elgg_register_action('izap_videos/admin/upgrade', dirname(__FILE__) . "/actions/izap_videos/admin/upgrade.php", 'admin');
	elgg_register_action('izap_videos/addEdit', dirname(__FILE__) . "/actions/izap_videos/addEdit.php", 'logged_in');
	elgg_register_action('izap_videos/delete', dirname(__FILE__) . "/actions/izap_videos/delete.php", 'logged_in');
	elgg_register_action('izap_videos/favorite_video', dirname(__FILE__) . "/actions/izap_videos/favorite_video.php", 'logged_in');
}

/**
 * Includes the required file based on the url parameters
 *
 * @param array $page url components
 * @return boolean
 */
function izap_videos_pagehandler($page) {
	$page_type = elgg_extract(0, $page, 'all');

	$resource_vars = [];
	switch ($page[0]) {
		case "all":
			echo elgg_view_resource('izap_videos/videos/all');
			break;
		case "owner":
			if (!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif (!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/owner', $resource_vars);
			break;
		case "group":
			if (!empty($page[1]) && is_numeric($page[1])) {
				$resource_vars['guid'] = (int)$page[1];
			}
			echo elgg_view_resource('izap_videos/videos/owner', $resource_vars);
			break;
		case "friends":
			if (!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif (!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/friends', $resource_vars);
			break;
		case "favorites":
			if (!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif (!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/favorites', $resource_vars);
			break;
		case "play":
			if (!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif (!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/play', $resource_vars);
			break;
		case "add":
			echo elgg_view_resource('izap_videos/videos/add');
			break;
		case "edit":
			if (!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif (!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/edit', $resource_vars);
			break;
		case "thumbs":
			echo elgg_view_resource('izap_videos/videos/thumbs');
			break;
		case "mostviewed":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostviewedvideos', $resource_vars);
			break;
		case "mostviewedtoday":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostviewedvideostoday', $resource_vars);
			break;
		case "mostviewedthismonth":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostviewedvideosthismonth', $resource_vars);
			break;
		case "mostviewedlastmonth":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostviewedvideoslastmonth', $resource_vars);
			break;
		case "mostviewedthisyear":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostviewedvideosthisyear', $resource_vars);
			break;
		case "mostcommented":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostcommentedvideos', $resource_vars);
			break;
		case "mostcommentedtoday":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostcommentedvideostoday', $resource_vars);
			break;
		case "mostcommentedthismonth":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostcommentedvideosthismonth', $resource_vars);
			break;
		case "mostcommentedlastmonth":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostcommentedvideoslastmonth', $resource_vars);
			break;
		case "mostcommentedthisyear":
			if (isset($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/lists/mostcommentedvideosthisyear', $resource_vars);
			break;
		case "recentlyviewed":
			echo elgg_view_resource('izap_videos/lists/recentlyviewed');
			break;
		case "recentlycommented":
			echo elgg_view_resource('izap_videos/lists/recentlycommented');
			break;
		case "recentvotes":
			if (elgg_is_active_plugin('elggx_fivestar')) {
				echo elgg_view_resource('izap_videos/lists/recentvotes');
				break;
			} else {
				return false;
			}
		case "highestrated":
			if (elgg_is_active_plugin('elggx_fivestar')) {
				echo elgg_view_resource('izap_videos/lists/highestrated');
				break;
			} else {
				return false;
			}
		case "highestvotecount":
			if (elgg_is_active_plugin('elggx_fivestar')) {
				echo elgg_view_resource('izap_videos/lists/highestvotecount');
				break;
			} else {
				return false;
			}
		default:
			return false;
	}

	return true;
}

/**
 * Sets page handler for the thumbs and video
 *
 * @param array $page
 */
function izap_videos_files_pagehandler($page) {
	$resource_vars['what'] = $page[0];
	$resource_vars['videoID'] = $page[1];
	echo elgg_view_resource('izap_videos/videos/thumbs', $resource_vars);
}
