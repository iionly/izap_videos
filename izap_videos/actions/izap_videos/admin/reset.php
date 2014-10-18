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

$queue_object = new izapQueue();
foreach ($queue_object->get(get_input('guid')) as $key => $prods) {
	get_entity($prods['guid'])->delete();
}

system_message(elgg_echo('izap_videos:adminSettings:reset_queue'));
forward(REFERER);
