<?php

/**
 * Add a menu item to an ownerblock
 */
function izap_videos_owner_block_menu($hook, $type, $return, $params) {
	if ($params['entity'] instanceof ElggUser) {
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

	if ($entity instanceof IzapVideos) {

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

				$params = [
					'href' => $url,
					'text' => elgg_format_element('img', ['src' => elgg_get_simplecache_url('izap_videos/favorite_remove.png'), 'alt' => elgg_echo('izap_videos:remove_favorite')], ''),
					'title' => elgg_echo('izap_videos:remove_favorite'),
					'is_action' => true,
					'is_trusted' => true,
				];
				$text = elgg_view('output/url', $params);

				$options = [
					'name' => 'remove_favorite',
					'text' => $text,
					'priority' => 80,
				];
				$menu[] = ElggMenuItem::factory($options);

			} else {
				$url = elgg_get_site_url() . 'action/izap_videos/favorite_video?guid=' . $entity->guid;

				$params = [
					'href' => $url,
					'text' => elgg_format_element('img', ['src' => elgg_get_simplecache_url('izap_videos/favorite_add.png'), 'alt' => elgg_echo('izap_videos:save_favorite')], ''),
					'title' => elgg_echo('izap_videos:save_favorite'),
					'is_action' => true,
					'is_trusted' => true,
				];
				$text = elgg_view('output/url', $params);

				$options = [
					'name' => 'make_favorite',
					'text' => $text,
					'priority' => 80,
				];
				$menu[] = ElggMenuItem::factory($options);
			}
		}

		$view_info = $entity->getViews();
		$view_info = (!$view_info) ? 0 : $view_info;
		$text = elgg_echo('izap_videos:views', [(int) $view_info]);
		$options = [
			'name' => 'views',
			'text' => elgg_format_element('span', [], $text),
			'href' => false,
			'priority' => 90,
		];
		$menu[] = ElggMenuItem::factory($options);
	}

	return $menu;
}

/**
 * Returns the url for the video to play
 *
 */
function izap_videos_urlhandler($hook, $type, $url, $params) {
	$entity = $params['entity'];
	if ($entity instanceof IzapVideos) {
		if (!$entity->getOwnerEntity()) {
			// default to a standard view if no owner.
			return false;
		}
		return elgg_get_site_url() . 'videos/play/' . get_entity($entity->container_guid)->username . '/' . $entity->guid . '/' . elgg_get_friendly_title($entity->title);
	}
}

function izap_videos_river_comment($hook_name, $entity_type, $return_value, $params) {
	$view = $params["view"];

	if ($view == 'river/object/comment/create') {
		$entity = $params['vars']['item']->getTargetEntity();
		if ($entity instanceof IzapVideos) {
			$return_value = elgg_view('river/object/comment/izap_videos', $params['vars']);
		}
	}
	return $return_value;
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

	if ($entity instanceof IzapVideos) {
		$owner = $params['event']->getActor();
		$recipient = $params['recipient'];
		$language = $params['language'];
		$method = $params['method'];

		$descr = $entity->description;
		$title = $entity->title;

		$notification->subject = elgg_echo('izap_videos:notify:subject_newvideo', [$title], $language);
		$notification->body = elgg_echo('izap_videos:notify:body_newvideo', [$owner->name, $title, $entity->getURL()], $language);
		$notification->summary = elgg_echo('izap_videos:notify:summary_newvideo', [$title, $language]);

		return $notification;
	}
}

function izap_queue_cron($hook, $entity_type, $returnvalue, $params) {
	izapTrigger_izap_videos();
}

function izap_videos_widget_urls($hook_name, $entity_type, $return_value, $params){
	$result = $return_value;
	$widget = $params["entity"];

	if (empty($result) && ($widget instanceof ElggWidget)) {
		$owner = $widget->getOwnerEntity();
		switch ($widget->handler) {
			case "izap_videos":
				$result = "videos/owner/{$owner->username}";
				break;
			case "index_latest_videos":
				$result = "/videos/all";
				break;
			case "groups_latest_videos":
				if ($owner instanceof ElggGroup) {
					$result = "videos/group/{$owner->guid}";
				} else {
					$result = "videos/owner/{$owner->username}";
				}
				break;
		}
	}
	return $result;
}

// Add or remove a group's iZAP Videos widget based on the corresponding group tools option
function izap_videos_tool_widget_handler($hook, $type, $return_value, $params) {
	if (!empty($params) && is_array($params)) {
		$entity = elgg_extract("entity", $params);

		if (!empty($entity) && ($entity instanceof ElggGroup)) {
			if (!is_array($return_value)) {
				$return_value = [];
			}

			if (!isset($return_value["enable"])) {
				$return_value["enable"] = [];
			}
			if (!isset($return_value["disable"])) {
				$return_value["disable"] = [];
			}

			if ($entity->izap_videos_enable == "yes") {
				$return_value["enable"][] = "groups_latest_videos";
			} else {
				$return_value["disable"][] = "groups_latest_videos";
			}
		}
	}

	return $return_value;
}
