<div class="marker_icons">
  <?php 
$marker_icon = ( get_option( 'oum_marker_icon' ) ? get_option( 'oum_marker_icon' ) : 'default' );
$items = $this->marker_icons;
foreach ( $items as $val ) {
    $selected = ( $marker_icon == $val ? 'checked' : '' );
    echo  "<label class='{$selected}'><div class='marker_icon_preview' data-style='{$val}'></div><input type='radio' name='oum_marker_icon' {$selected} value='{$val}'></label>" ;
}
?>

  <?php 
?>

  <?php 

if ( !oum_fs()->is_plan_or_trial( 'pro' ) || !oum_fs()->is_premium() ) {
    ?>

    <?php 
    //pro marker icons
    $pro_items = $this->pro_marker_icons;
    foreach ( $pro_items as $val ) {
        echo  "<label class='pro-only label_marker_user_icon'><div class='marker_icon_preview' data-style='{$val}'></div>" ;
        echo  "\n        <div class='icon_upload'>\n          <button disabled class='button button-secondary'>" . __( 'Upload Icon', 'open-user-map' ) . "</button>\n          <p class='description'>PNG, 50 x 82 Pixel</p>\n        </div>\n      " ;
        echo  "<a class='oum-gopro-text' href='" . oum_fs()->get_upgrade_url() . "'>" . __( 'Upgrade to PRO to use custom icons.', 'open-user-map' ) . "</a>" ;
        echo  "</label>" ;
    }
    ?>

  <?php 
}

?>

</div>