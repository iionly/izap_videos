<?php
/**
 * Videos icon / thumbnail view
 *
 * derived from default Elgg core icon view
 *
 * @uses $vars['entity']     The entity the icon represents - uses \IzapVideos::getThumb() method
 * @uses $vars['size']       topbar, tiny, small, medium (default), large, master
 * @uses $vars['href']       Optional override for link
 * @uses $vars['img_class']  Optional CSS class added to img
 * @uses $vars['link_class'] Optional CSS class for the link
 */

$entity = $vars['entity'];

$icon_sizes = elgg_get_icon_sizes('object', \IzapVideos::SUBTYPE);
// Get size
if (!array_key_exists($vars['size'], $icon_sizes)) {
	$vars['size'] = 'medium';
}

$class = elgg_extract('img_class', $vars, '');

$title = $entity->title;
if (isset($vars['title'])) {
	$title = $vars['title'];
}
$title = htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false);


$url = $entity->getURL();
if (isset($vars['href'])) {
	$url = $vars['href'];
}

$size = $vars['size'];
if (!isset($vars['width'])) {
	$vars['width'] = $size != 'master' ? $icon_sizes[$size]['w'] : null;
}
if (!isset($vars['height'])) {
	$vars['height'] = $size != 'master' ? $icon_sizes[$size]['h'] : null;
}

$img_params = [
	'src' => $entity->getThumb(true),
	'alt' => $title,
];

if (!empty($class)) {
	$img_params['class'] = $class;
}

if (!empty($vars['width'])) {
	$img_params['width'] = $vars['width'];
}

if (!empty($vars['height'])) {
	$img_params['height'] = $vars['height'];
}

$img = elgg_view('output/img', $img_params);

if ($url) {
	$params = [
		'href' => $url,
		'text' => $img,
		'is_trusted' => true,
	];
	$class = elgg_extract('link_class', $vars, '');
	if ($class) {
		$params['class'] = $class;
	}

	echo elgg_view('output/url', $params);
} else {
	echo $img;
}
