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
$queue_object = new izapQueue();
$queuedVideos = $queue_object->get();
?>

<div>
	<h3>
		<?php echo elgg_echo('izap_videos:queueStatus') . $queueStatus . ' (' . izap_count_queue() . ')';?>
	</h3>
<?php
	if (count($queuedVideos)) {
?>
		<table class="izap_table">
		<tbody>
	<?php
			$i = 0;
			foreach($queuedVideos as $queuedVideo) {
				$extension_length = strlen(izap_get_file_extension($queuedVideo['main_file']));
				$outputPath = substr($queuedVideo['main_file'], 0, '-' . ($extension_length + 1));

				$ORIGNAL_name = basename($queuedVideo['main_file']);
				$ORIGNAL_size = izapFormatBytes(filesize($queuedVideo['main_file']));

				$FLV_name = basename($outputPath . '_c.flv');
				$FLV_size = izapFormatBytes(filesize($outputPath . '_c.flv'));
	?>
				<tr class="<?php echo (!$i && izapIsQueueRunning_izap_videos()) ? 'queue_selected' : '';?>">
				<td>
					<?php echo $ORIGNAL_name . '<br>' . $FLV_name;?>
				</td>
				<td>
					<?php echo $ORIGNAL_size . '<br>' . $FLV_size;?>
				</td>
				<td>
				<?php
					if ($queuedVideo['conversion'] != IN_PROCESS) {
						echo elgg_view('output/confirmlink',array(
							'href' => elgg_get_site_url() . 'action/izap_videos/admin/reset?guid=' . $queuedVideo['guid'],
							'text' => '',
							'is_action' => true,
							'is_trusted' => true,
							'class' => 'elgg-icon elgg-icon-delete-alt',
							'confirm'=> elgg_echo('izap_videos:adminSettings:resetQueue_confirm')
						));
					}
				?>
				</td>
				</tr>
		<?php
				$i++;
			}
		?>
		</tbody>
		</table>
<?php
	} else {
?>
		<div align="center">
		<?php
			echo elgg_view("output/longtext", array("value" => elgg_echo('izap_videos:queueStatus:none'), 'class' => 'mtm'));
		?>
		</div>
	<?php
	}
?>
</div>
