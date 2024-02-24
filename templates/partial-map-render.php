<?php

$oum_all_locations = [];
foreach ( $locations_list as $location ) {
    
    if ( get_option( 'oum_enable_location_date' ) === 'on' ) {
        $date_tag = '<div class="oum_location_date">' . wp_kses_post( $location['date'] ) . '</div>';
    } else {
        $date_tag = '';
    }
    
    $name_tag = ( get_option( 'oum_enable_title', 'on' ) == 'on' ? '<h3 class="oum_location_name">' . esc_attr( $location['name'] ) . '</h3>' : '' );
    //error_log(print_r($location, true));
    
    if ( $location['image'] ) {
        $img_tag = '<div class="oum_location_image"><img class="skip-lazy" src="' . esc_url_raw( $location['image'] ) . '"></div>';
    } else {
        $img_tag = '';
    }
    
    //HOOK: modify location image
    $img_tag = apply_filters( 'oum_location_bubble_image', $img_tag, $location );
    $audio_tag = ( $location['audio'] ? '<audio controls="controls" style="width:100%"><source type="audio/mp4" src="' . $location['audio'] . '"><source type="audio/mpeg" src="' . $location['audio'] . '"><source type="audio/wav" src="' . $location['audio'] . '"></audio>' : '' );
    $address_tag = '';
    
    if ( get_option( 'oum_enable_address', 'on' ) === 'on' ) {
        $address_tag = ( $location['address'] && !get_option( 'oum_hide_address' ) ? esc_attr( $location['address'] ) : '' );
        if ( $oum_enable_gmaps_link === 'on' && $address_tag ) {
            $address_tag = '<a title="' . __( 'go to Google Maps', 'open-user-map' ) . '" href="https://www.google.com/maps/search/?api=1&amp;query=' . esc_attr( $location['lat'] ) . '%2C' . esc_attr( $location['lng'] ) . '" target="_blank">' . $address_tag . '</a>';
        }
    }
    
    $address_tag = ( $address_tag != '' ? '<div class="oum_location_address">' . $address_tag . '</div>' : '' );
    
    if ( get_option( 'oum_enable_description', 'on' ) === 'on' ) {
        $description_tag = '<div class="oum_location_description">' . wp_kses_post( $location['text'] ) . '</div>';
    } else {
        $description_tag = '';
    }
    
    $custom_fields = '';
    
    if ( isset( $location['custom_fields'] ) && is_array( $location['custom_fields'] ) ) {
        $custom_fields .= '<div class="oum_location_custom_fields">';
        foreach ( $location['custom_fields'] as $custom_field ) {
            if ( !$custom_field['val'] || $custom_field['val'] == '' ) {
                continue;
            }
            
            if ( is_array( $custom_field['val'] ) ) {
                array_walk( $custom_field['val'], function ( &$x ) {
                    $x = '<span>' . $x . '</span>';
                } );
                $custom_fields .= '<div class="oum_custom_field"><strong>' . $custom_field['label'] . ':</strong> ' . implode( '', $custom_field['val'] ) . '</div>';
            } else {
                
                if ( stristr( $custom_field['val'], '|' ) ) {
                    //multiple entries separated with | symbol
                    $custom_fields .= '<div class="oum_custom_field"><strong>' . $custom_field['label'] . ':</strong> ';
                    foreach ( explode( '|', $custom_field['val'] ) as $entry ) {
                        $entry = trim( $entry );
                        
                        if ( wp_http_validate_url( $entry ) ) {
                            //URL
                            $custom_fields .= '<a target="_blank" href="' . $entry . '">' . $entry . '</a> ';
                        } elseif ( is_email( $entry ) && $custom_field['fieldtype'] == 'email' ) {
                            //Email
                            $custom_fields .= '<a target="_blank" href="mailto:' . $entry . '">' . $entry . '</a> ';
                        } else {
                            //Text
                            $custom_fields .= '<span>' . $entry . '</span>';
                        }
                    
                    }
                    $custom_fields .= '</div>';
                } else {
                    //single entry
                    
                    if ( wp_http_validate_url( $custom_field['val'] ) ) {
                        //URL
                        $custom_fields .= '<div class="oum_custom_field"><strong>' . $custom_field['label'] . ':</strong> <a target="_blank" href="' . $custom_field['val'] . '">' . $custom_field['val'] . '</a></div>';
                    } elseif ( is_email( $custom_field['val'] ) && $custom_field['fieldtype'] == 'email' ) {
                        //Email
                        $custom_fields .= '<div class="oum_custom_field"><strong>' . $custom_field['label'] . ':</strong> <a target="_blank" href="mailto:' . $custom_field['val'] . '">' . $custom_field['val'] . '</a></div>';
                    } else {
                        //Text
                        $custom_fields .= '<div class="oum_custom_field"><strong>' . $custom_field['label'] . ':</strong> ' . $custom_field['val'] . '</div>';
                    }
                
                }
            
            }
        
        }
        $custom_fields .= '</div>';
    }
    
    
    if ( get_option( 'oum_enable_single_page' ) ) {
        $link_tag = '<div class="oum_read_more"><a href="' . get_the_permalink( $location['post_id'] ) . '">' . __( 'Read more', 'open-user-map' ) . '</a></div>';
    } else {
        $link_tag = '';
    }
    
    // building bubble block content
    $content = $img_tag;
    $content .= '<div class="oum_location_text">';
    $content .= $date_tag;
    $content .= $address_tag;
    $content .= $name_tag;
    $content .= $custom_fields;
    $content .= $description_tag;
    $content .= $audio_tag;
    $content .= $link_tag;
    $content .= '</div>';
    // removing backslash escape
    $content = str_replace( "\\", "", $content );
    //HOOK: modify location bubble content
    $content = apply_filters( 'oum_location_bubble_content', $content, $location );
    // set location
    $oum_location = [
        'title'     => html_entity_decode( esc_attr( $location['name'] ) ),
        'lat'       => esc_attr( $location["lat"] ),
        'lng'       => esc_attr( $location["lng"] ),
        'content'   => $content,
        'icon'      => esc_attr( $location["icon"] ),
        'type'      => ( isset( $location["type_term_id"] ) ? esc_attr( $location["type_term_id"] ) : '' ),
        'type_name' => ( isset( $location["type_term_id"] ) ? esc_attr( $location["type_name"] ) : '' ),
        'post_id'   => esc_attr( $location["post_id"] ),
    ];
    $oum_all_locations[] = $oum_location;
}
// Fixing height without unit
$oum_map_height = ( is_numeric( $oum_map_height ) ? $oum_map_height . 'px' : $oum_map_height );
$oum_map_height_mobile = ( is_numeric( $oum_map_height_mobile ) ? $oum_map_height_mobile . 'px' : $oum_map_height_mobile );
?>

<div class="box-wrap map-size-<?php 
echo  esc_attr( $map_size ) ;
?> map-size-mobile-<?php 
echo  esc_attr( $map_size_mobile ) ;
?> <?php 

if ( $oum_enable_regions == 'on' && $regions && count( $regions ) > 0 ) {
    ?>oum-regions-<?php 
    echo  $oum_regions_layout_style ;
    ?> <?php 
}

?>">
  <?php 

if ( $oum_enable_regions == 'on' && $regions && count( $regions ) > 0 ) {
    ?>
    <div class="tab-wrap">
      <div class="oum-tabs" id="nav-tab-<?php 
    echo  $unique_id ;
    ?>" role="tablist">
        <?php 
    $i = 0;
    ?>
        <?php 
    foreach ( $regions as $region ) {
        ?>

          <?php 
        $i++;
        $name = $region->name;
        $t_id = $region->term_id;
        $term_lat = get_term_meta( $t_id, 'oum_lat', true );
        $term_lng = get_term_meta( $t_id, 'oum_lng', true );
        $term_zoom = get_term_meta( $t_id, 'oum_zoom', true );
        ?>
          <div class="nav-item nav-link <?php 
        echo  ( isset( $oum_start_region_name ) && $name == $oum_start_region_name ? 'active' : '' ) ;
        ?> change_region" data-lat="<?php 
        echo  esc_attr( $term_lat ) ;
        ?>" data-lng="<?php 
        echo  esc_attr( $term_lng ) ;
        ?>" data-zoom="<?php 
        echo  esc_attr( $term_zoom ) ;
        ?>" data-toggle="tab"><?php 
        echo  esc_html( $name ) ;
        ?></div>

        <?php 
    }
    ?>
      </div>
    </div>
  <?php 
}

?>

  <div class="map-wrap">
    <div id="map-<?php 
echo  $unique_id ;
?>" class="leaflet-map map-style_<?php 
echo  esc_attr( $map_style ) ;
?>"></div>
    
    <?php 
if ( $oum_enable_searchbar === 'true' && $oum_searchbar_type == 'markers' ) {
    ?>
      <div id="oum_search_marker"></div>
    <?php 
}
?>

    <?php 

if ( $oum_enable_add_location === 'on' ) {
    ?>
    
      <?php 
    ?>

      <?php 
    
    if ( !oum_fs()->is_plan_or_trial( 'pro' ) || !oum_fs()->is_premium() ) {
        ?>

        <div id="open-add-location-overlay" class="open-add-location-overlay" style="background-color: <?php 
        echo  $oum_ui_color ;
        ?>"><span class="btn_icon">+</span><span class="btn_text"><?php 
        echo  esc_attr( $oum_plus_button_label ) ;
        ?></span></div>

      <?php 
    }
    
    ?>

    <?php 
}

?>

    <?php 
?>

    <script type="text/javascript" data-category="functional" class="cmplz-native" id="oum-inline-js">
      var map_el = `map-<?php 
echo  $unique_id ;
?>`;

      if(document.getElementById(map_el)) {

        var mapStyle = `<?php 
echo  esc_attr( $map_style ) ;
?>`;
        var oum_tile_provider_mapbox_key = `<?php 
echo  esc_attr( $oum_tile_provider_mapbox_key ) ;
?>`;
        var marker_icon_url = `<?php 
echo  ( $marker_icon == 'user1' && $marker_user_icon ? esc_url( $marker_user_icon ) : esc_url( $this->plugin_url ) . 'src/leaflet/images/marker-icon_' . esc_attr( $marker_icon ) . '-2x.png' ) ;
?>`;
        var marker_shadow_url = `<?php 
echo  esc_url( $this->plugin_url ) ;
?>src/leaflet/images/marker-shadow.png`;
        var oum_enable_scrollwheel_zoom_map = <?php 
echo  $oum_enable_scrollwheel_zoom_map ;
?>;
        var oum_enable_cluster = <?php 
echo  $oum_enable_cluster ;
?>;
        var oum_enable_fullscreen = <?php 
echo  $oum_enable_fullscreen ;
?>;

        var oum_enable_searchbar = <?php 
echo  $oum_enable_searchbar ;
?>;
        var oum_searchbar_type = `<?php 
echo  $oum_searchbar_type ;
?>`;

        var oum_geosearch_selected_provider = ``; 
        var oum_geosearch_provider = `<?php 
echo  $oum_geosearch_provider ;
?>`;
        var oum_geosearch_provider_geoapify_key = `<?php 
echo  esc_attr( $oum_geosearch_provider_geoapify_key ) ;
?>`;
        var oum_geosearch_provider_here_key = `<?php 
echo  esc_attr( $oum_geosearch_provider_here_key ) ;
?>`;
        var oum_geosearch_provider_mapbox_key = `<?php 
echo  esc_attr( $oum_geosearch_provider_mapbox_key ) ;
?>`;
        
        var oum_enable_searchaddress_button = <?php 
echo  $oum_enable_searchaddress_button ;
?>;
        var oum_searchaddress_label = `<?php 
echo  esc_attr( $oum_searchaddress_label ) ;
?>`;

        var oum_enable_searchmarkers_button = <?php 
echo  $oum_enable_searchmarkers_button ;
?>;
        var oum_searchmarkers_label = `<?php 
echo  esc_attr( $oum_searchmarkers_label ) ;
?>`;
        var oum_searchmarkers_zoom = `<?php 
echo  esc_attr( $oum_searchmarkers_zoom ) ;
?>`;

        var oum_enable_currentlocation = <?php 
echo  $oum_enable_currentlocation ;
?>;
        var oum_collapse_filter = <?php 
echo  $oum_collapse_filter ;
?>;
        var oum_action_after_submit = `<?php 
echo  $oum_action_after_submit ;
?>`;
        var thankyou_redirect = `<?php 
echo  $thankyou_redirect ;
?>`;
        var start_lat = `<?php 
echo  esc_attr( $start_lat ) ;
?>`;
        var start_lng = `<?php 
echo  esc_attr( $start_lng ) ;
?>`;
        var start_zoom = `<?php 
echo  esc_attr( $start_zoom ) ;
?>`;
        var oum_enable_fixed_map_bounds = `<?php 
echo  $oum_enable_fixed_map_bounds ;
?>`;
        var oum_minimum_zoom_level = `<?php 
echo  $oum_minimum_zoom_level ;
?>`;
        var oum_use_settings_start_location = <?php 
echo  $oum_use_settings_start_location ;
?>;
        var oum_has_regions = <?php 
echo  ( $oum_enable_regions == 'on' && $regions && count( $regions ) > 0 ? 'true' : 'false' ) ;
?>;

        var oum_location = {};
        var locations_without_type = [];
        var locations_by_type = [];
        var oum_custom_css = '';
        var oum_custom_script = '';
        var oumMap;
        var oumMap2;

        var oumPrepareLocations = (location) => {
          //console.log(location, 'location');
          if(location.type) {
            // add new marker category to array if not exists
            if(!locations_by_type.find(markercategory => markercategory.id === location.type)) {
              let newmarkercategory = {
                id: location.type,
                name: location.type_name,
                icon: location.icon,
                locations : []
              }
              locations_by_type.push(newmarkercategory);
            }

            // add location to marker category
            let markercategory = locations_by_type.find(markercategory => markercategory.id === location.type);
            markercategory.locations.push(location);

          }else{
            locations_without_type.push(location);
          }
        };

        var oumConditionalField = (sourceField, targetField, condShow, condHide) => {
          const sourceElement = document.querySelector(sourceField);
          const targetElement = document.querySelector(targetField).parentElement; /* works with custom fields only */

          /* trigger on change */
          sourceElement.onchange = function(e) {
            const val = this.value;
            
            console.log('OUM: run condition', {val, sourceField, targetField, condShow, condHide});
            
            if(condShow.includes(val)) {
              targetElement.style.display = 'block';
            }else if(condHide.includes(this.value)) {
              targetElement.style.display = 'none';
            }
          }

          /* trigger initially */
          let changeEvent = new Event('change');
          sourceElement.dispatchEvent(changeEvent);
        };

        /* Transfer PHP array to JS array */
        var oum_all_locations = <?php 
echo  json_encode( $oum_all_locations ) ;
?>;

        /* Group Locations by marker categories (if exist) for further processing */
        oum_all_locations.forEach(oumPrepareLocations);


        /**
         * Add Custom Styles
         */
        
        <?php 

if ( $oum_ui_color ) {
    ?>

          /* custom color */
          oum_custom_css += `
            .open-user-map .add-location #close-add-location-overlay:hover {color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map input.oum-switch[type="checkbox"]:checked + label::before {background-color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map .add-location .location-overlay-content #oum_add_location_thankyou h3 {color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map .oum_location_text a {color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map .oum-tabs {border-color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map .oum-tabs .nav-item:hover {color: <?php 
    echo  $oum_ui_color ;
    ?> !important; border-color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map .oum-tabs .nav-item.active {color: <?php 
    echo  $oum_ui_color ;
    ?> !important; border-color: <?php 
    echo  $oum_ui_color ;
    ?> !important}
            .open-user-map .box-wrap .map-wrap .oum-attribution a {color: <?php 
    echo  $oum_ui_color ;
    ?> !important;}`;

        <?php 
}

?>

        <?php 

if ( $oum_map_height ) {
    ?>

          /* custom map height */
          oum_custom_css += `
            .open-user-map .box-wrap > .map-wrap {padding: 0 !important; height: <?php 
    echo  esc_attr( $oum_map_height ) ;
    ?> !important; aspect-ratio: unset !important;}`;

        <?php 
}

?>

        <?php 

if ( $oum_map_height_mobile ) {
    ?>

          /* custom map height */
          oum_custom_css += `
            @media screen and (max-width: 768px) {.open-user-map .box-wrap > .map-wrap {padding: 0 !important; height: <?php 
    echo  esc_attr( $oum_map_height_mobile ) ;
    ?> !important; aspect-ratio: unset !important;}}`;

        <?php 
}

?>

        var custom_style = document.createElement('style');

        if (custom_style.styleSheet) {
          custom_style.styleSheet.cssText = oum_custom_css;
        } else {
          custom_style.appendChild(document.createTextNode(oum_custom_css));
        }

        document.getElementsByTagName('head')[0].appendChild(custom_style);

      }
    </script>

  </div>

  </div>