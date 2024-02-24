<table class="form-table">
    <tbody>

        <?php 
?>

        <tr valign="top">
            <th scope="row">
                <?php 
echo  __( 'Marker', 'open-user-map' ) ;
?>
            </th>
            <td>
                <div class="geo-coordinates-wrap">
                    <div class="map-wrap">
                        <div id="mapGetLocation" class="leaflet-map map-style_<?php 
echo  esc_attr( $map_style ) ;
?>"></div>
                    </div>
                    <div class="input-wrap">
                        <div class="geo-coordinates-hint">
                            <div class="hint"><?php 
echo  __( 'Click on the map to set a location marker or <a href="#" id="showLatLngInputs">edit GPS coordinates manually</a>.', 'open-user-map' ) ;
?></div>

                            <div class="latlng-wrap" id="latLngInputs" style="display: none;">
                                <div class="hint"><?php 
echo  __( 'Edit GPS coordinates manually:', 'open-user-map' ) ;
?></div>
                                <div>
                                    <div>
                                        <label class="meta-label" for="oum_location_lat">
                                            <?php 
echo  __( 'Lat', 'open-user-map' ) ;
?>
                                        </label>
                                        <input type="text" class="widefat" id="oum_location_lat" name="oum_location_lat" value="<?php 
echo  esc_attr( $lat ) ;
?>"></input>
                                    </div>
                                    <div>
                                        <label class="meta-label" for="oum_location_lng">
                                            <?php 
echo  __( 'Lng', 'open-user-map' ) ;
?>
                                        </label>
                                        <input type="text" class="widefat" id="oum_location_lng" name="oum_location_lng" value="<?php 
echo  esc_attr( $lng ) ;
?>"></input>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script type="text/javascript" data-category="functional" class="cmplz-native" id="oum-inline-js">
                    const lat = '<?php 
echo  esc_attr( $lat ) ;
?>';
                    const lng = '<?php 
echo  esc_attr( $lng ) ;
?>';
                    const zoom = '<?php 
echo  ( get_option( 'oum_searchmarkers_zoom' ) ? get_option( 'oum_searchmarkers_zoom' ) : $this->oum_searchmarkers_zoom_default ) ;
?>';
                    const mapStyle = '<?php 
echo  esc_attr( $map_style ) ;
?>';
                    const oum_tile_provider_mapbox_key = `<?php 
echo  esc_attr( $oum_tile_provider_mapbox_key ) ;
?>`;
                    const oum_enable_currentlocation = '<?php 
echo  ( get_option( 'oum_enable_currentlocation' ) ? true : false ) ;
?>';
                    const enableCurrentLocation = oum_enable_currentlocation ? true : false;
                    let oum_geosearch_selected_provider = ``; 
                    const oum_geosearch_provider = `<?php 
echo  ( get_option( 'oum_geosearch_provider' ) ? get_option( 'oum_geosearch_provider' ) : 'osm' ) ;
?>`;
                    const oum_geosearch_provider_geoapify_key = `<?php 
echo  get_option( 'oum_geosearch_provider_geoapify_key', '' ) ;
?>`;
                    const oum_geosearch_provider_here_key = `<?php 
echo  get_option( 'oum_geosearch_provider_here_key', '' ) ;
?>`;
                    const oum_geosearch_provider_mapbox_key = `<?php 
echo  get_option( 'oum_geosearch_provider_mapbox_key', '' ) ;
?>`;
                    const oum_searchaddress_label = `<?php 
echo  esc_attr( ( get_option( 'oum_searchaddress_label' ) ? get_option( 'oum_searchaddress_label' ) : $this->oum_searchaddress_label_default ) ) ;
?>`;

                    <?php 

if ( $marker_icon == 'user1' && $marker_user_icon ) {
    ?>
                        const marker_icon_url = `<?php 
    echo  esc_url( $marker_user_icon ) ;
    ?>`;
                    <?php 
} else {
    ?>
                        const marker_icon_url = `<?php 
    echo  esc_url( $this->plugin_url ) ;
    ?>src/leaflet/images/marker-icon_<?php 
    echo  esc_attr( $marker_icon ) ;
    ?>-2x.png`;
                    <?php 
}

?>

                    const marker_shadow_url = '<?php 
echo  esc_url( $this->plugin_url ) ;
?>src/leaflet/images/marker-shadow.png';
                    </script>

                    <?php 
// load map base scripts
$this->include_map_scripts();
wp_enqueue_script(
    'oum_backend_location_js',
    esc_url( $this->plugin_url ) . 'src/js/backend-location.js',
    array(
    'wp-polyfill',
    'oum_leaflet_providers_js',
    'oum_leaflet_markercluster_js',
    'oum_leaflet_subgroups_js',
    'oum_leaflet_geosearch_js',
    'oum_leaflet_locate_js',
    'oum_leaflet_fullscreen_js',
    'oum_leaflet_search_js',
    'oum_leaflet_gesture_js'
),
    esc_attr( $this->plugin_version )
);
?>
                </div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php 
echo  __( 'Subtitle', 'open-user-map' ) ;
?>
            </th>
            <td>
                <input type="text" class="regular-text" id="oum_location_address" name="oum_location_address" value="<?php 
echo  esc_attr( $address ) ;
?>"></input>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php 
echo  __( 'Image', 'open-user-map' ) ;
?>
            </th>
            <td>
                <a href="#" class="oum_upload_image_button button button-secondary"><?php 
echo  __( 'Upload Image', 'open-user-map' ) ;
?></a>
                <input type="hidden" id="oum_location_image" name="oum_location_image" value="<?php 
echo  esc_attr( $image ) ;
?>"></input>
                <br><br>
                <div id="oum_location_image_preview" class="<?php 
echo  $has_image ;
?>"><?php 
echo  $image_tag ;
?><div onclick="oumRemoveImageUpload()" class="remove-upload">&times;</div></div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php 
echo  __( 'Audio', 'open-user-map' ) ;
?>
            </th>
            <td>
                <a href="#" class="oum_upload_audio_button button button-secondary"><?php 
echo  __( 'Upload Audio', 'open-user-map' ) ;
?></a>
                <input type="hidden" id="oum_location_audio" name="oum_location_audio" value="<?php 
echo  esc_attr( $audio ) ;
?>"></input>
                <br><br>
                <div id="oum_location_audio_preview" class="<?php 
echo  $has_audio ;
?>">
                    <?php 
echo  $audio_tag ;
?>
                    <div onclick="oumRemoveAudioUpload()" class="remove-upload">&times;</div>
                </div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <?php 
echo  __( 'Description', 'open-user-map' ) ;
?>
            </th>
            <td>
                <?php 
wp_editor( $text, 'oum_location_text', array(
    'tinymce'       => false,
    'quicktags'     => true,
    'media_buttons' => false,
) );
?>
            </td>
        </tr>

        <?php 

if ( is_array( $active_custom_fields ) ) {
    ?>
            <?php 
    foreach ( $active_custom_fields as $index => $custom_field ) {
        ?>

                <?php 
        $custom_field['fieldtype'] = ( isset( $custom_field['fieldtype'] ) ? $custom_field['fieldtype'] : 'text' );
        $custom_field['description'] = ( isset( $custom_field['description'] ) ? $custom_field['description'] : '' );
        $label = esc_attr( $custom_field['label'] ) . (( isset( $custom_field['required'] ) ? '*' : '' ));
        $description = ( $custom_field['description'] ? '<div class="oum_custom_field_description">' . $custom_field['description'] . '</div>' : '' );
        ?>
                
                <?php 
        
        if ( $custom_field['fieldtype'] == 'text' ) {
            ?>

                    <tr valign="top" class="section-id_cf-<?php 
            echo  $index ;
            ?>">
                        <th scope="row">
                            <?php 
            echo  $label ;
            ?>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" value="<?php 
            echo  ( isset( $meta_custom_fields[$index] ) ? esc_attr( $meta_custom_fields[$index] ) : '' ) ;
            ?>"></input>
                            <?php 
            echo  $description ;
            ?>
                        </td>
                    </tr>

                <?php 
        }
        
        ?>


                <?php 
        
        if ( $custom_field['fieldtype'] == 'link' ) {
            ?>

                    <tr valign="top" class="section-id_cf-<?php 
            echo  $index ;
            ?>">
                        <th scope="row">
                            <?php 
            echo  $label ;
            ?>
                        </th>
                        <td>
                            <input type="text" class="regular-text" name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" value="<?php 
            echo  ( isset( $meta_custom_fields[$index] ) ? esc_attr( $meta_custom_fields[$index] ) : '' ) ;
            ?>"></input>
                            <?php 
            echo  $description ;
            ?>
                        </td>
                    </tr>

                <?php 
        }
        
        ?>


                <?php 
        
        if ( $custom_field['fieldtype'] == 'email' ) {
            ?>

                    <tr valign="top" class="section-id_cf-<?php 
            echo  $index ;
            ?>">
                        <th scope="row">
                            <?php 
            echo  $label ;
            ?>
                        </th>
                        <td>
                            <input type="email" class="regular-text" name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" value="<?php 
            echo  ( isset( $meta_custom_fields[$index] ) ? esc_attr( $meta_custom_fields[$index] ) : '' ) ;
            ?>"></input>
                            <?php 
            echo  $description ;
            ?>
                        </td>
                    </tr>

                <?php 
        }
        
        ?>


                <?php 
        
        if ( $custom_field['fieldtype'] == 'checkbox' ) {
            ?>

                    <tr valign="top" class="section-id_cf-<?php 
            echo  $index ;
            ?>">
                        <th scope="row">
                            <?php 
            echo  $label ;
            ?>
                        </th>
                        <td>
                            <fieldset>
                                <?php 
            $options = ( isset( $custom_field['options'] ) ? explode( '|', $custom_field['options'] ) : array() );
            ?>
                                <?php 
            foreach ( $options as $option ) {
                ?>
                                    <div>
                                        <label>
                                            <input type="checkbox" name="oum_location_custom_fields[<?php 
                echo  $index ;
                ?>][]" value="<?php 
                echo  esc_attr( $option ) ;
                ?>" <?php 
                echo  ( isset( $meta_custom_fields[$index] ) && is_array( $meta_custom_fields[$index] ) && in_array( esc_attr( $option ), $meta_custom_fields[$index] ) ? 'checked' : '' ) ;
                ?>>
                                            <span><?php 
                echo  $option ;
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
                        </td>
                    </tr>

                <?php 
        }
        
        ?>


                <?php 
        
        if ( $custom_field['fieldtype'] == 'radio' ) {
            ?>

                    <tr valign="top" class="section-id_cf-<?php 
            echo  $index ;
            ?>">
                        <th scope="row">
                            <?php 
            echo  $label ;
            ?>
                        </th>
                        <td>
                            <fieldset>
                                <?php 
            $options = ( isset( $custom_field['options'] ) ? explode( '|', $custom_field['options'] ) : array() );
            ?>
                                <?php 
            foreach ( $options as $option ) {
                ?>
                                    <div>
                                        <label>
                                            <input type="radio" name="oum_location_custom_fields[<?php 
                echo  $index ;
                ?>]" value="<?php 
                echo  esc_attr( $option ) ;
                ?>" <?php 
                echo  ( isset( $meta_custom_fields[$index] ) && esc_attr( $option ) == $meta_custom_fields[$index] ? 'checked' : '' ) ;
                ?>>
                                            <span><?php 
                echo  $option ;
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
                        </td>
                    </tr>

                <?php 
        }
        
        ?>


                <?php 
        
        if ( $custom_field['fieldtype'] == 'select' ) {
            ?>

                    <tr valign="top" class="section-id_cf-<?php 
            echo  $index ;
            ?>">
                        <th scope="row">
                            <?php 
            echo  $label ;
            ?>
                        </th>
                        <td>
                            <select name="oum_location_custom_fields[<?php 
            echo  $index ;
            ?>]" <?php 
            echo  ( isset( $custom_field['required'] ) ? 'required' : '' ) ;
            ?>>
                                <?php 
            $options = ( isset( $custom_field['options'] ) ? explode( '|', $custom_field['options'] ) : array() );
            ?>
                                <?php 
            foreach ( $options as $option ) {
                ?>
                                    <option value="<?php 
                echo  esc_attr( $option ) ;
                ?>" <?php 
                echo  ( isset( $meta_custom_fields[$index] ) && esc_attr( $option ) == $meta_custom_fields[$index] ? 'selected' : '' ) ;
                ?>><?php 
                echo  $option ;
                ?></option>
                                <?php 
            }
            ?>
                            </select>
                            <?php 
            echo  $description ;
            ?>
                        </td>
                    </tr>

                <?php 
        }
        
        ?>

            <?php 
    }
    ?>
        <?php 
}

?>

        <?php 
?>

    </tbody>
</table>