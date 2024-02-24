<div class="marker_icons">
  <?php
  $selected = (!$currentType) ? 'checked' : '';
  $marker_icon = 'default';
  $name = __('Default', 'open-user-map');
  $val = '';
  ?>
  
  <label class="<?php echo $selected; ?>"><div class="marker_icon_preview" data-style="<?php echo $marker_icon; ?>"></div><div class="name"><?php echo $name; ?></div><input type="radio" name="oum_marker_icon" <?php echo $selected; ?> value="<?php echo $val; ?>"></label>


  <?php foreach($terms as $tag): ?>

    <?php
    $val = $tag->term_id;
    $name = $tag->name;
    $oum_marker_icon = get_option('oum_marker_icon') ? get_option('oum_marker_icon') : 'default';
    $marker_icon = get_term_meta($val, 'oum_marker_icon', true) ? get_term_meta($val, 'oum_marker_icon', true) : $oum_marker_icon;
    $marker_user_icon = get_term_meta($tag->term_id, 'oum_marker_user_icon', true) ? get_term_meta($tag->term_id, 'oum_marker_user_icon', true) : get_option('oum_marker_user_icon');
    $user_icon_style = ($marker_user_icon && ($marker_icon == 'user1')) ? "style='background-image: url($marker_user_icon)'" : "";
    
    if($currentType) {
      $selected = ($val == $currentType->term_id) ? 'checked' : '';
    }else{
      $selected = '';
    }
    
    ?>
    
    <label class="<?php echo $selected; ?>"><div class="marker_icon_preview" data-style="<?php echo $marker_icon; ?>" <?php echo $user_icon_style; ?>></div><div class="name"><?php echo $name; ?></div><input type="radio" name="oum_marker_icon" <?php echo $selected; ?> value="<?php echo $val; ?>"></label>

  <?php endforeach; ?>
</div>
<div class="description"><?php echo __('You can manage marker categories <a href="edit-tags.php?taxonomy=oum-type&post_type=oum-location">here</a>', 'open-user-map'); ?></div>