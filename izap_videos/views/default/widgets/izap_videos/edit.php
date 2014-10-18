<?php
/**
 * Widget settings for latest videos
 *
 */

// set default value
if (!isset($vars['entity']->num_display)) {
	$vars['entity']->num_display = 4;
}

$params = array(
	'name' => 'params[num_display]',
	'value' => $vars['entity']->num_display,
	'options' => array(1, 2, 4, 6, 8, 10),
);
$select = elgg_view('input/select', $params);

?>
<div>
	<?php echo elgg_echo('izap_videos:numbertodisplay'); ?>:
	<?php echo $select; ?>
</div>
