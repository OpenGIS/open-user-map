<?php 

$image_list = array();
$query = array(
  'post_type' => 'oum-location',
  'posts_per_page' => -1,
  'fields' => 'ids',
);

$locations = get_posts($query);

$target_url = (isset($block_attributes['url']) && $block_attributes['url'] != '') ? $block_attributes['url'] : '';

foreach($locations as $post_id) {
  $image = get_post_meta($post_id, '_oum_location_image', true);
  $image_thumb = null;
  $data = array();

  //exit on no image
  if(!$image) continue;

  $data['post_id'] = $post_id;

  //set full scale image
  $data['image_orig_url'] = $image;
  
  //get image thumbnail
  if(stristr($image, 'oum-useruploads')) {
    //image uploaded from frontend
    $image_thumb = get_post_meta($post_id, '_oum_location_image_thumb', true);

    //exit on no image_thumb
    if(!$image_thumb) continue;
  }else{
    //image uploaded from backend
    $image_id = attachment_url_to_postid($image);

    if($image_id > 0) {
      $image_thumb = wp_get_attachment_image_url($image_id, 'medium');
    }
  }

  if($image_thumb) {
    //use thumbnail if available
    $data['image_thumb_url'] = $image_thumb;
  }else{
    //use orginal image as fallback
    $data['image_thumb_url'] = $image;
  }

  $image_list[] = $data;

  // limit images by shortcode attribute
  if(isset($block_attributes['number']) && $block_attributes['number'] != '' && is_numeric($block_attributes['number'])) {
    if(count($image_list) >= intval($block_attributes['number'])) break;
  };
}
?>

<div class="open-user-map-image-gallery">

  <?php foreach($image_list as $image): ?>
    <?php
    $params = array_merge($_GET, array('markerid' => $image['post_id']));
    $new_query_string = http_build_query( $params );

    ?>

    <div class="oum-gallery-item">
      <a href="<?php echo $target_url; ?>?<?php echo $new_query_string; ?>">
        <img src="<?php echo $image['image_thumb_url']; ?>" data-image-orig-url="<?php echo $image['image_orig_url']; ?>" data-post-id="<?php echo $image['post_id']; ?>">
      </a>
    </div>

  <?php endforeach; ?>

</div>