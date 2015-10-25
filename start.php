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

elgg_register_event_handler('init', 'system', 'init_izap_videos');

/**
 * Main function that register everything
 */
function init_izap_videos() {

	elgg_extend_view('css/elgg', 'izap_videos/css');
	elgg_extend_view('css/admin', 'izap_videos/css');

	// Register the main lib
	$base_dir = elgg_get_plugins_path() . 'izap_videos/lib';
	elgg_register_library('izap_videos:core', "$base_dir/izapLib.php");
	elgg_load_library('izap_videos:core');

	// Load all the required libraries
	izapLoadLib_izap_videos();

	elgg_register_ajax_view('izap_videos/admin/getQueue');
	elgg_register_ajax_view('izap_videos/playpopup');

	// Set up the site menu
	elgg_register_menu_item('site', array(
		'name' => 'videos',
		'href' => 'videos/all',
		'text' => elgg_echo('videos'),
	));

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
	elgg_register_notification_event('object', 'izap_videos', array('create'));
	elgg_register_plugin_hook_handler('prepare', 'notification:create:object:izap_videos', 'izap_videos_notify_message');

	$period = izapAdminSettings_izap_videos('izap_cron_time');
	if($period != 'none') {
		elgg_register_plugin_hook_handler('cron', $period, 'izap_queue_cron');
	}

	// Group videos
	add_group_tool_option('izap_videos', elgg_echo('izap_videos:group:enablevideo'), true);
	elgg_extend_view('groups/tool_latest', 'izap_videos/group_module');

	// Adding izap_videos widget
	elgg_register_widget_type('izap_videos', elgg_echo('izap_videos:videos'), elgg_echo('izap_videos:widget'));

	if (elgg_is_active_plugin('widget_manager')) {
		//add index widget for Widget Manager plugin
		elgg_register_widget_type('index_latest_videos', elgg_echo("izap_videos:mostrecent"), elgg_echo('izap_videos:mostrecent:description'), array("index"));

		//add groups widget for Widget Manager plugin
		elgg_register_widget_type('groups_latest_videos', elgg_echo("izap_videos:mostrecent"), elgg_echo('izap_videos:mostrecent:group:description'), array("groups"));

		//register title urls for widgets
		elgg_register_plugin_hook_handler("entity:url", "object", "izap_videos_widget_urls");
	}

	// Allow liking of videos
	elgg_register_plugin_hook_handler('likes:is_likable', 'object:izap_videos', 'Elgg\Values::getTrue');

	// Register for search
	elgg_register_entity_type('object','izap_videos');

	// Register some actions
	$action_path = elgg_get_plugins_path() . 'izap_videos/actions/izap_videos';
	elgg_register_action('izap_videos/admin/settings', "$action_path/admin/settings.php", 'admin');
	elgg_register_action('izap_videos/admin/api_keys', "$action_path/admin/api_keys.php", 'admin');
	elgg_register_action('izap_videos/admin/resetSettings', "$action_path/admin/resetSettings.php", 'admin');
	elgg_register_action('izap_videos/admin/recycle', "$action_path/admin/recycle.php", 'admin');
	elgg_register_action('izap_videos/admin/recycle_delete', "$action_path/admin/recycle_delete.php", 'admin');
	elgg_register_action('izap_videos/admin/reset', "$action_path/admin/reset.php", 'admin');
	elgg_register_action('izap_videos/admin/upgrade', "$action_path/admin/upgrade.php", 'admin');
	elgg_register_action('izap_videos/addEdit', "$action_path/addEdit.php", 'logged_in');
	elgg_register_action('izap_videos/delete', "$action_path/delete.php", 'logged_in');
	elgg_register_action('izap_videos/favorite_video', "$action_path/favorite_video.php", 'logged_in');
}


/**
 * Includes the required file based on the url parameters
 *
 * @param array $page url components
 * @return boolean
 */
function izap_videos_pagehandler($page) {
	$page_type = elgg_extract(0, $page, 'all');

	$resource_vars = array();
	switch ($page[0]) {
		case "all":
			echo elgg_view_resource('izap_videos/videos/all');
			break;
		case "owner":
			if(!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif(!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/owner', $resource_vars);
			break;
		case "group":
			if(!empty($page[1]) && is_numeric($page[1])) {
				$resource_vars['guid'] = (int)$page[1];
			}
			echo elgg_view_resource('izap_videos/videos/owner', $resource_vars);
			break;
		case "friends":
			if(!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif(!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/friends', $resource_vars);
			break;
		case "favorites":
			if(!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif(!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/favorites', $resource_vars);
			break;
		case "play":
			if(!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif(!empty($page[1]) && is_string($page[1])) {
				$resource_vars['username'] = $page[1];
			}
			echo elgg_view_resource('izap_videos/videos/play', $resource_vars);
			break;
		case "add":
			echo elgg_view_resource('izap_videos/videos/add');
			break;
		case "edit":
			if(!empty($page[2]) && is_numeric($page[2])) {
				$resource_vars['username'] = $page[1];
				$resource_vars['guid'] = (int)$page[2];
			} elseif(!empty($page[1]) && is_string($page[1])) {
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
			if(elgg_is_active_plugin('elggx_fivestar')) {
				echo elgg_view_resource('izap_videos/lists/recentvotes');
				break;
			} else {
				return false;
			}
		case "highestrated":
			if(elgg_is_active_plugin('elggx_fivestar')) {
				echo elgg_view_resource('izap_videos/lists/highestrated');
				break;
			} else {
				return false;
			}
		case "highestvotecount":
			if(elgg_is_active_plugin('elggx_fivestar')) {
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


/**
 * Returns the url for the video to play
 *
 */
function izap_videos_urlhandler($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'izap_videos')) {
		if (!$entity->getOwnerEntity()) {
			// default to a standard view if no owner.
			return false;
		}
		return elgg_get_site_url() . 'videos/play/' . get_entity($entity->container_guid)->username . '/' . $entity->guid . '/' . elgg_get_friendly_title($entity->title);
	}
}


/**
 * Add a menu item to an ownerblock
 */
function izap_videos_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "videos/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('izap_videos', elgg_echo('videos'), $url);
		$return[] = $item;
	} else {
		if ($params['entity']->izap_videos_enable != "no") {
			$url = "videos/group/{$params['entity']->guid}";
			$item = new ElggMenuItem('izap_videos', elgg_echo('izap_videos:groupvideos'), $url);
			$return[] = $item;
		}
	}

	return $return;
}


/**
 * Add entries to entity menu
 */
function izap_videos_entity_menu_setup($hook, $type, $menu, $params) {
	if (elgg_in_context('widgets')) {
		return $menu;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'videos') {
		return $menu;
	}

	if (elgg_instanceof($entity, 'object', 'izap_videos')) {

		foreach ($menu as $key => $item) {
			switch ($item->getName()) {
				case 'delete':
					$item->setHref(elgg_get_site_url() . 'action/izap_videos/delete?guid=' . $entity->getGUID());
					break;
				case 'edit':
					if ($entity->converted == 'yes') {
						$item->setHref(elgg_get_site_url() . 'videos/edit/' . get_entity($entity->container_guid)->username . '/' . $entity->getGUID());
					} else {
						unset($menu[$key]);
					}
					break;
			}
		}

		if ($entity->converted == 'yes') {
			if (izap_is_my_favorited($entity)) {
				$url = elgg_get_site_url() . 'action/izap_videos/favorite_video?guid=' . $entity->guid . '&izap_action=remove';

				$params = array(
					'href' => $url,
					'text' => '<img src="' . elgg_get_simplecache_url('izap_videos/favorite_remove.png') . '" alt="' . elgg_echo('izap_videos:remove_favorite') . '"/>',
					'title' => elgg_echo('izap_videos:remove_favorite'),
					'is_action' => true,
					'is_trusted' => true,
				);
				$text = elgg_view('output/url', $params);

				$options = array(
					'name' => 'remove_favorite',
					'text' => $text,
					'priority' => 80,
				);
				$menu[] = ElggMenuItem::factory($options);

			} else {
				$url = elgg_get_site_url() . 'action/izap_videos/favorite_video?guid=' . $entity->guid;

				$params = array(
					'href' => $url,
					'text' => '<img src="' . elgg_get_simplecache_url('izap_videos/favorite_add.png') . '" alt="' . elgg_echo('izap_videos:save_favorite') . '"/>',
					'title' => elgg_echo('izap_videos:save_favorite'),
					'is_action' => true,
					'is_trusted' => true,
				);
				$text = elgg_view('output/url', $params);

				$options = array(
					'name' => 'make_favorite',
					'text' => $text,
					'priority' => 80,
				);
				$menu[] = ElggMenuItem::factory($options);
			}
		}

		$view_info = $entity->getViews();
		$view_info = (!$view_info) ? 0 : $view_info;
		$text = elgg_echo('izap_videos:views', array((int)$view_info));
		$options = array(
			'name' => 'views',
			'text' => "<span>$text</span>",
			'href' => false,
			'priority' => 90,
		);
		$menu[] = ElggMenuItem::factory($options);
	}

	return $menu;
}


function izap_videos_widget_urls($hook_name, $entity_type, $return_value, $params){
	$result = $return_value;
	$widget = $params["entity"];

	if(empty($result) && ($widget instanceof ElggWidget)) {
		$owner = $widget->getOwnerEntity();
		switch($widget->handler) {
			case "izap_videos":
				$result = "videos/owner/{$owner->username}";
				break;
			case "index_latest_videos":
				$result = "/videos/all";
				break;
			case "groups_latest_videos":
				if($owner instanceof ElggGroup){
					$result = "videos/group/{$owner->guid}";
				} else {
					$result = "videos/owner/{$owner->username}";
				}
				break;
		}
	}
	return $result;
}


/**
 *
 * Prepare a notification message about a new video added to the site
 *
 * @param string                          $hook         Hook name
 * @param string                          $type         Hook type
 * @param Elgg_Notifications_Notification $notification The notification to prepare
 * @param array                           $params       Hook parameters
 * @return Elgg_Notifications_Notification
 */
function izap_videos_notify_message($hook, $type, $notification, $params) {

	$entity = $params['event']->getObject();

	if (elgg_instanceof($entity, 'object', 'izap_videos')) {

		$owner = $params['event']->getActor();
		$recipient = $params['recipient'];
		$language = $params['language'];
		$method = $params['method'];

		$descr = $entity->description;
		$title = $entity->title;

		$notification->subject = elgg_echo('izap_videos:notify:subject_newvideo', array($title), $language);
		$notification->body = elgg_echo('izap_videos:notify:body_newvideo', array($owner->name, $title, $entity->getURL()), $language);
		$notification->summary = elgg_echo('izap_videos:notify:summary_newvideo', array($title), $language);

		return $notification;
	}
}


function izap_videos_river_comment($hook_name, $entity_type, $return_value, $params) {
	$view = $params["view"];

	if ($view == 'river/object/comment/create') {
		$entity = $params['vars']['item']->getTargetEntity();
		if (elgg_instanceof($entity, 'object', 'izap_videos')) {
			$return_value = elgg_view('river/object/comment/izap_videos', $params['vars']);
		}
	}
	return $return_value;
}

function izap_queue_cron($hook, $entity_type, $returnvalue, $params) {
	izapTrigger_izap_videos();
}
