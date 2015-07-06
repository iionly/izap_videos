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

elgg_require_js('izap_videos/queue_status');

// get action for queue
$action = get_input('action');
switch ($action) {
	case 'reset':
		izapResetQueue_izap_videos();
		forward(elgg_get_site_url() . '/admin/administer_utilities/izap_videos?tab=queue_status');
		break;
	case 'delete':
		izapEmptyQueue_izap_videos();
		forward(elgg_get_site_url() . '/admin/administer_utilities/izap_videos?tab=queue_status');
		break;
	default:
		break;
}

?>

<div id="videoQueue" align="center">
	<img src="<?php echo elgg_get_site_url() . 'mod/izap_videos/_graphics/queue.gif';?>" alt="queue">
</div>
<div align="right" class="mtm">
	<?php
		echo elgg_view("output/url", array(
			'href' => elgg_get_site_url() . 'action/izap_videos/admin/reset',
			'text' => elgg_echo('izap_videos:adminSettings:resetQueueAll'),
			'is_action' => true,
			'is_trusted' => true,
			'confirm' => elgg_echo('izap_videos:adminSettings:resetQueueAll_confirm'),
			'class' => 'elgg-button elgg-button-action',
		));
	?>
	<?php
		echo elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:adminSettings:resetQueue_info'), 'class' => 'mtm elgg-subtext'));
	?>
</div>
