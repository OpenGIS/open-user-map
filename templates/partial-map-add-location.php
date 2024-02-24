<div id="add-location-overlay" class="add-location">
  <div class="location-overlay-content">
    <div id="close-add-location-overlay">&#x2715;</div>
    <form id="oum_add_location" enctype="multipart/form-data">
      <h2><?php 
echo  ( get_option( 'oum_form_headline' ) ? get_option( 'oum_form_headline' ) : __( 'Add a new location', 'open-user-map' ) ) ;
?></h2>
      <?php 
wp_nonce_field( 'oum_location', 'oum_location_nonce' );
?>

      <?php 

if ( get_option( 'oum_enable_title', 'on' ) ) {
    ?>
        <?php 
    $maxlength = ( get_option( 'oum_title_maxlength' ) > 0 ? 'maxlength="' . get_option( 'oum_title_maxlength' ) . '"' : '' );
    ?>
        <input type="text" id="oum_location_title" name="oum_location_title" <?php 
    if ( get_option( 'oum_title_required', 'on' ) ) {
        ?>required<?php 
    }
    ?> placeholder="<?php 
    echo  $oum_title_label ;
    if ( get_option( 'oum_title_required', 'on' ) ) {
        ?>*<?php 
    }
    ?>" <?php 
    echo  $maxlength ;
    ?> />
      <?php 
}

?>
      
      <label class="oum-label"><?php 
echo  $oum_map_label ;
?></label>
      <div class="map-wrap">
        <div id="mapGetLocation" class="leaflet-map map-style_<?php 
echo  $map_style ;
?>"></div>
      </div>
      <input type="hidden" id="oum_location_lat" name="oum_location_lat" required placeholder="<?php 
echo  __( 'Latitude', 'open-user-map' ) ;
?>*" />
      <input type="hidden" id="oum_location_lng" name="oum_location_lng" required placeholder="<?php 
echo  __( 'Longitude', 'open-user-map' ) ;
?>*" />

      <?php 
?>

      
      <?php 
$oum_custom_fields = get_option( 'oum_custom_fields' );
?>
      <?php 

if ( is_array( $oum_custom_fields ) ) {
    ?>
        <div class="oum_custom_fields_wrapper">
        <?php 
    foreach ( $oum_custom_fields as $index => $custom_field ) {
        ?>
          <?php 
        if ( $custom_field['label'] == '' && $custom_field['fieldtype'] != 'html' ) {
            continue;
        }
        $custom_field['fieldtype'] = ( isset( $custom_field['fieldtype'] ) ? $custom_field['fieldtype'] : 'text' );
        $custom_field['description'] = ( isset( $custom_field['description'] ) ? $custom_field['description'] : '' );
        $label = esc_attr( $custom_field['label'] ) . (( isset( $custom_field['required'] ) ? '*' : '' ));
        $description = ( $custom_field['description'] ? '<div class="oum_custom_field_description">' . $custom_field['description'] . '</div>' : '' );
        $maxlength = ( $custom_field['maxlength'] ? 'maxlength="' . $custom_field['maxlength'] . '"' : '' );
        $html = ( $custom_field['html'] ? $custom_field['html'] : '' );
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'text' ) {
            ?>
            <div>
              <input type="text" name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" placeholder="<?php 
            echo  $label ;
            ?>" <?php 
            echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
            ?> value="" <?php 
            echo  $maxlength ;
            ?> />
              <?php 
            echo  $description ;
            ?>
            </div>
          <?php 
        }
        
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'link' ) {
            ?>
            <div>
              <input type="url" name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" placeholder="<?php 
            echo  $label ;
            ?>" <?php 
            echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
            ?> value="" <?php 
            echo  $maxlength ;
            ?> />
              <?php 
            echo  $description ;
            ?>
            </div>
          <?php 
        }
        
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'email' ) {
            ?>
            <div>
              <input type="email" name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" placeholder="<?php 
            echo  $label ;
            ?>" <?php 
            echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
            ?> value="" <?php 
            echo  $maxlength ;
            ?> />
              <?php 
            echo  $description ;
            ?>
            </div>
          <?php 
        }
        
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'checkbox' ) {
            ?>
            <div>
              <fieldset class="<?php 
            echo  ( isset( $custom_field['required'] ) ? 'is-required' : '' ) ;
            ?>">
                <legend><?php 
            echo  $label ;
            ?></legend>
                <?php 
            $options = ( isset( $custom_field['options'] ) ? explode( '|', $custom_field['options'] ) : array() );
            foreach ( $options as $option ) {
                ?>
                  <div>
                    <label>
                      <input style="accent-color: <?php 
                echo  $oum_ui_color ;
                ?>" type="checkbox" name="oum_location_custom_fields[<?php 
                echo  $index ;
                ?>][]" value="<?php 
                echo  esc_attr( trim( $option ) ) ;
                ?>" <?php 
                echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
                ?>>
                      <span><?php 
                echo  trim( $option ) ;
                ?></span>
                    </label>
                  </div>
                <?php 
            }
            ?>
              </fieldset>
              <?php 
            echo  $description ;
            ?>
            </div>
          <?php 
        }
        
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'radio' ) {
            ?>
            <div>
              <fieldset class="<?php 
            echo  ( isset( $custom_field['required'] ) ? 'is-required' : '' ) ;
            ?>">
                <legend><?php 
            echo  $label ;
            ?></legend>
                <?php 
            $options = ( isset( $custom_field['options'] ) ? explode( '|', $custom_field['options'] ) : array() );
            foreach ( $options as $option ) {
                ?>
                  <div>
                    <label>
                      <input style="accent-color: <?php 
                echo  $oum_ui_color ;
                ?>" type="radio" name="oum_location_custom_fields[<?php 
                echo  $index ;
                ?>]" value="<?php 
                echo  esc_attr( trim( $option ) ) ;
                ?>" <?php 
                echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
                ?>>
                      <span><?php 
                echo  trim( $option ) ;
                ?></span>
                    </label>
                  </div>
                <?php 
            }
            ?>
              </fieldset>
              <?php 
            echo  $description ;
            ?>
            </div>
          <?php 
        }
        
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'select' ) {
            ?>
            <div>
              <label class="oum-label"><?php 
            echo  esc_attr( $label ) ;
            ?></label>
              <select name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" <?php 
            echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
            ?>>
                <?php 
            $options = ( isset( $custom_field['options'] ) ? explode( '|', $custom_field['options'] ) : array() );
            if ( isset( $custom_field['emptyoption'] ) ) {
                ?>
                  <option></option>
                <?php 
            }
            foreach ( $options as $option ) {
                ?>
                  <option value="<?php 
                echo  esc_attr( trim( $option ) ) ;
                ?>"><?php 
                echo  trim( $option ) ;
                ?></option>
                <?php 
            }
            ?>
              </select>
              <?php 
            echo  $description ;
            ?>
            </div>
          <?php 
        }
        
        ?>

          <?php 
        
        if ( $custom_field['fieldtype'] == 'html' ) {
            ?>
            <div class="oum-custom-field-html">
              <?php 
            echo  $html ;
            ?>
            </div>
          <?php 
        }
        
        ?>

        <?php 
    }
    ?>
        </div>
      <?php 
}

?>
      

      <?php 

if ( get_option( 'oum_enable_address', 'on' ) === 'on' ) {
    ?>
        <input type="text" id="oum_location_address" name="oum_location_address" placeholder="<?php 
    echo  $oum_address_label ;
    ?>" />
      <?php 
}

?>

      <?php 

if ( get_option( 'oum_enable_description', 'on' ) === 'on' ) {
    ?>
        <textarea id="oum_location_text" name="oum_location_text" placeholder="<?php 
    echo  $oum_description_label ;
    echo  ( get_option( 'oum_description_required' ) ? '*' : '' ) ;
    ?>" <?php 
    echo  ( get_option( 'oum_description_required' ) ? 'required' : '' ) ;
    ?>></textarea>
      <?php 
}

?>
      
      <?php 

if ( get_option( 'oum_enable_image', 'on' ) === 'on' || get_option( 'oum_enable_audio', 'on' ) === 'on' ) {
    ?>
        <label class="oum-label"><?php 
    echo  $oum_upload_media_label ;
    ?></label>
        <div class="oum_media">
          <?php 
    
    if ( get_option( 'oum_enable_image', 'on' ) === 'on' ) {
        ?>
            <div class="media-upload">
              <label style="color: <?php 
        echo  $oum_ui_color ;
        ?>" for="oum_location_image" title="<?php 
        echo  __( 'Upload Image', 'open-user-map' ) ;
        ?>"><span class="dashicons dashicons-format-image"></span><?php 
        echo  ( get_option( 'oum_image_required' ) ? '*' : '' ) ;
        ?></label>
              <input type="file" id="oum_location_image" name="oum_location_image" accept="image/*" multiple="false" <?php 
        echo  ( get_option( 'oum_image_required' ) ? 'required' : '' ) ;
        ?> />
              <div class="preview">
                <span></span>
                <div id="oum_remove_image" class="remove-upload">&times;</div>
              </div>
            </div>
          <?php 
    }
    
    ?>
          <?php 
    
    if ( get_option( 'oum_enable_audio', 'on' ) === 'on' ) {
        ?>
            <div class="media-upload">
              <label style="color: <?php 
        echo  $oum_ui_color ;
        ?>" for="oum_location_audio" title="<?php 
        echo  __( 'Upload Audio', 'open-user-map' ) ;
        ?>"><span class="dashicons dashicons-format-audio"></span><?php 
        echo  ( get_option( 'oum_audio_required' ) ? '*' : '' ) ;
        ?></label>
              <input type="file" id="oum_location_audio" name="oum_location_audio" accept="audio/mp3,audio/mpeg3,audio/wav,audio/mp4,audio/mpeg,audio/x-m4a" multiple="false" <?php 
        echo  ( get_option( 'oum_audio_required' ) ? 'required' : '' ) ;
        ?> />
              <div class="preview">
                <span></span>
                <div id="oum_remove_audio" class="remove-upload">&times;</div>
              </div>
            </div>
          <?php 
    }
    
    ?>
        </div>
      <?php 
}

?>

      <?php 
?>

      <input type="submit" id="oum_submit_btn" style="background: <?php 
echo  $oum_ui_color ;
?>" value="<?php 
echo  ( get_option( 'oum_submit_button_label' ) ? get_option( 'oum_submit_button_label' ) : __( 'Submit location for review', 'open-user-map' ) ) ;
?>" />
    </form>

    <div id="oum_add_location_error" style="display: none"></div>

    <div id="oum_add_location_thankyou" style="display: none">
      <h3><?php 
echo  ( $thankyou_headline ? $thankyou_headline : __( 'Thank you!', 'open-user-map' ) ) ;
?></h3>
      <p><?php 
echo  ( $thankyou_text ? $thankyou_text : __( 'We will check your location suggestion and release it as soon as possible.', 'open-user-map' ) ) ;
?></p>
      <button id="oum_add_another_location" style="background: <?php 
echo  $oum_ui_color ;
?>" type="button"><?php 
echo  $oum_addanother_label ;
?></button>
    </div>
  </div>
</div>