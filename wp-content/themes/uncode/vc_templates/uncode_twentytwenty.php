<?php
$media_before = $media_after = $el_class = '';
extract(shortcode_atts(array(
	'media_before' => '',
	'media_after' => '',
	'el_class' => '',
) , $atts));

global $adaptive_images, $adaptive_images_async;

$el_class = $this->getExtraClass($el_class);
$css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-twentytwenty ' . $el_class, $this->settings['base'], $atts);

if ($media_before === '' || $media_after === '') return;

$media_attributes_before = uncode_get_media_info($media_before);
$media_before_metavalue = unserialize($media_attributes_before->metadata);
if (empty($media_before_metavalue)) {
	$media_before_metavalue['width'] = $media_before_metavalue['height'] = 1;
}
$resized_image_before = uncode_resize_image($media_attributes_before->guid, $media_attributes_before->path, $media_before_metavalue['width'], $media_before_metavalue['height'], 12, null, false);
$img_data_before = '';
if ($adaptive_images === 'on' && $adaptive_images_async === 'on') {
	$img_class_before = ' class="img-responsive adaptive-async"';
	$img_data_before = ' data-uniqueid="'.$media_before.'-'.big_rand().'" data-guid="'.$media_attributes_before->guid.'" data-path="'.$media_attributes_before->path.'" data-width="'.$media_before_metavalue['width'].'" data-height="'.$media_before_metavalue['height'].'" data-singlew="12" data-singleh="null" data-crop=""';
} else {
	$img_class_before = '';
	$img_data_before = '';
}

$media_attributes_after = uncode_get_media_info($media_after);
$media_after_metavalue = unserialize($media_attributes_after->metadata);
if (empty($media_after_metavalue)) {
	$media_after_metavalue['width'] = $media_after_metavalue['height'] = 1;
}
$resized_image_after = uncode_resize_image($media_attributes_after->guid, $media_attributes_after->path, $media_after_metavalue['width'], $media_after_metavalue['height'], 12, null, false);
$img_data_after = '';
if ($adaptive_images === 'on' && $adaptive_images_async === 'on') {
	$img_class_after = ' class="img-responsive adaptive-async"';
	$img_data_after = ' data-uniqueid="'.$media_after.'-'.big_rand().'" data-guid="'.$media_attributes_after->guid.'" data-path="'.$media_attributes_after->path.'" data-width="'.$media_after_metavalue['width'].'" data-height="'.$media_after_metavalue['height'].'" data-singlew="12" data-singleh="null" data-crop=""';
} else {
	$img_class_after = '';
	$img_data_after = '';
}

?>

<div class="<?php echo $css_class; ?>">
	<div class="twentytwenty-container">
		<img src="<?php echo $resized_image_before['url']; ?>" width="<?php echo $resized_image_before['width']; ?>" height="<?php echo $resized_image_before['height']; ?>"<?php echo $img_class_before.$img_data_before; ?>>
		<img src="<?php echo $resized_image_after['url']; ?>" width="<?php echo $resized_image_after['width']; ?>" height="<?php echo $resized_image_after['height']; ?>"<?php echo $img_class_after.$img_data_after; ?>>
	</div>
</div>