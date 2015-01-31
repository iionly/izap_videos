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

$queueStatus = (izapIsQueueRunning_izap_videos()) ? elgg_echo('izap_videos:running') : elgg_echo('izap_videos:notRunning');
$buggy_videos_object = new izapQueue();
$buggy_videos = $buggy_videos_object->get_from_trash();
$main_url = elgg_get_site_url() . 'admin/administer_utilities/izap_videos?tab=recycle_bin';
?>

<form method="post" action="<?php echo elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/izap_videos/admin/recycle_delete'); ?>">
	<div>
		<h3 align="center">
			<?php echo elgg_echo('izap_videos:error_videos');?>
		</h3>
	<?php
		if ($buggy_videos) {
	?>
			<table class="izap_table">
			<tbody>
		<?php
			foreach($buggy_videos as $video_to_be_recycled) {
				$ORIGNAL_name = $video_to_be_recycled['main_file'];
				$ORIGNAL_size = izapFormatBytes(filesize($video_to_be_recycled['main_file']));
		?>
				<tr>
				<td>
			<?php
				echo $ORIGNAL_name;
			?>
				<br>
				<?php echo elgg_echo('izap_videos:restore_size') . $ORIGNAL_size; ?>
				<br>
				<a href="<?php echo elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/izap_videos/admin/recycle?guid='.$video_to_be_recycled['guid']); ?>"><?php echo elgg_echo('izap_videos:restore');?></a>

				<br>
			<?php
				if (elgg_is_active_plugin('messages')) {
			?>
					<input type="text" name="izap[user_message_<?php echo $video_to_be_recycled['guid'];?>]" value="" /><br>
					<label><input type="checkbox" name="izap[send_message_<?php echo $video_to_be_recycled['guid'];?>]" value="yes" /><?php echo elgg_echo('izap_videos:send_user_message');?></label>
			<?php
				} else {
			?>
					<?php echo elgg_echo('izap_videos:adminSettings:messages_plugin_missing'); ?>
			<?php
				}
			?>
				<br>

			<?php
				echo elgg_view('input/submit', array('name' => "izap[" . $video_to_be_recycled['guid'] ."]", 'value' => elgg_echo('delete')));
			?>
				</td>
				</tr>
	<?php
			}
		} else {
	?>
			<div align="center">
			<?php
				echo elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:error_videos:none'), 'class' => 'mtm'));
			?>
			</div>
	<?php
		}
	?>
		</tbody>
		</table>
	</div>
</form>
