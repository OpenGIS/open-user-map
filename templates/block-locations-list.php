<?php

// Load settings
$oum_enable_gmaps_link = get_option( 'oum_enable_gmaps_link', 'on' );
// Build query
$count = get_option( 'posts_per_page', 10 );
$paged = ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 );
$query = array(
    'post_type'      => 'oum-location',
    'fields'         => 'ids',
    'posts_per_page' => $count,
    'paged'          => $paged,
);
// Custom Attribute: Filter for types
if ( isset( $block_attributes['types'] ) && $block_attributes['types'] != '' ) {
    $selected_types_slugs = explode( '|', $block_attributes['types'] );
    $query['tax_query'] = array(array(
        'taxonomy' => 'oum-type',
        'field'    => 'slug',
        'terms'    => $selected_types_slugs,
    ));
}
// Custom Attribute: Filter for ids
if ( isset( $block_attributes['ids'] ) && $block_attributes['ids'] != '' ) {
    $selected_ids = explode( '|', $block_attributes['ids'] );
    $query['include'] = $selected_ids;
}
// Init WP_Query
$locations_query = new WP_Query($query);
$locations_list = array();
if ( $locations_query->have_posts() ) {
    while ( $locations_query->have_posts() ) {
        $locations_query->the_post();
        $post_id = get_the_ID();
        // Prepare data
        $location_meta = get_post_meta( $post_id, '_oum_location_key', true );
        $name = str_replace( "'", "\\'", strip_tags( get_the_title( $post_id ) ) );
        $address = ( isset( $location_meta['address'] ) ? str_replace( "'", "\\'", preg_replace( '/\\r|\\n/', '', $location_meta['address'] ) ) : '' );
        $text = ( isset( $location_meta["text"] ) ? str_replace( "'", "\\'", str_replace( array("\r\n", "\r", "\n"), "<br>", $location_meta["text"] ) ) : '' );
        $image = get_post_meta( $post_id, '_oum_location_image', true );
        $image_thumb = null;
        if ( stristr( $image, 'oum-useruploads' ) ) {
            //image uploaded from frontend
            $image_thumb = get_post_meta( $post_id, '_oum_location_image_thumb', true );
        } else {
            //image uploaded from backend
            $image_id = attachment_url_to_postid( $image );
            if ( $image_id > 0 ) {
                $image_thumb = wp_get_attachment_image_url( $image_id, 'medium' );
            }
        }
        if ( isset( $image_thumb ) && $image_thumb != '' ) {
            //use thumbnail if available
            $image = $image_thumb;
        }
        //make image url relative
        $site_url = 'http://';
        if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
            $site_url = 'https://';
        }
        $site_url .= $_SERVER['SERVER_NAME'];
        $image = str_replace( $site_url, '', $image );
        $audio = get_post_meta( $post_id, '_oum_location_audio', true );
        // custom fields
        $custom_fields = [];
        $meta_custom_fields = ( isset( $location_meta['custom_fields'] ) ? $location_meta['custom_fields'] : false );
        $active_custom_fields = get_option( 'oum_custom_fields' );
        if ( is_array( $meta_custom_fields ) && is_array( $active_custom_fields ) ) {
            foreach ( $active_custom_fields as $index => $active_custom_field ) {
                //don't add if private
                if ( isset( $active_custom_field['private'] ) ) {
                    continue;
                }
                if ( isset( $meta_custom_fields[$index] ) ) {
                    array_push( $custom_fields, array(
                        'label'     => $active_custom_field['label'],
                        'val'       => $meta_custom_fields[$index],
                        'fieldtype' => $active_custom_field['fieldtype'],
                    ) );
                }
            }
        }
        if ( !isset( $location_meta['lat'] ) && !isset( $location_meta['lng'] ) ) {
            continue;
        }
        $geolocation = array(
            'lat' => $location_meta['lat'],
            'lng' => $location_meta['lng'],
        );
        if ( isset( $location_types ) && is_array( $location_types ) && count( $location_types ) == 1 && !get_option( 'oum_enable_multiple_marker_types' ) ) {
            //get current location icon from oum-type taxonomy
            $type = $location_types[0];
            $current_marker_icon = ( get_term_meta( $type->term_id, 'oum_marker_icon', true ) ? get_term_meta( $type->term_id, 'oum_marker_icon', true ) : 'default' );
            $current_marker_user_icon = get_term_meta( $type->term_id, 'oum_marker_user_icon', true );
        } else {
            //get current location icon from settings
            $current_marker_icon = ( get_option( 'oum_marker_icon' ) ? get_option( 'oum_marker_icon' ) : 'default' );
            $current_marker_user_icon = get_option( 'oum_marker_user_icon' );
        }
        if ( $current_marker_icon == 'user1' && $current_marker_user_icon ) {
            $icon = esc_url( $current_marker_user_icon );
        } else {
            $icon = esc_url( $this->plugin_url ) . 'src/leaflet/images/marker-icon_' . esc_attr( $current_marker_icon ) . '-2x.png';
        }
        // collect locations for JS use
        $location = array(
            'post_id'       => $post_id,
            'date'          => get_the_date( '', $post_id ),
            'name'          => $name,
            'address'       => $address,
            'lat'           => $geolocation['lat'],
            'lng'           => $geolocation['lng'],
            'text'          => $text,
            'image'         => $image,
            'audio'         => $audio,
            'icon'          => $icon,
            'custom_fields' => $custom_fields,
        );
        if ( isset( $location_types ) && is_array( $location_types ) && count( $location_types ) > 0 ) {
            foreach ( $location_types as $term ) {
                $location['types'][] = (string) $term->term_taxonomy_id;
            }
        }
        $locations_list[] = $location;
    }
}
?>

<div class="open-user-map-locations-list">

  <div class="oum-locations-list-items">
    <?php 
foreach ( $locations_list as $location ) {
    ?>

      <?php 
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
    //HOOK: modify location list item content
    $content = apply_filters( 'oum_location_list_item_content', $content, $location );
    // set location
    $oum_location = [
        'title'   => html_entity_decode( esc_attr( $location['name'] ) ),
        'lat'     => esc_attr( $location["lat"] ),
        'lng'     => esc_attr( $location["lng"] ),
        'content' => $content,
        'icon'    => esc_attr( $location["icon"] ),
        'types'   => ( isset( $location["types"] ) ? $location["types"] : [] ),
        'post_id' => esc_attr( $location["post_id"] ),
    ];
    ?>

      <div class="oum-locations-list-item">
        <?php 
    echo $oum_location['content'];
    ?>
      </div>

    <?php 
}
?>
  </div>

  <?php 
if ( $locations_query->max_num_pages > 1 ) {
    ?>
    <nav class="pagination oum-locations-list-pagination">
      <?php 
    echo paginate_links( array(
        'current'   => max( 1, get_query_var( 'paged' ) ),
        'total'     => $locations_query->max_num_pages,
        'prev_text' => __( '&laquo; Prev' ),
        'next_text' => __( 'Next &raquo;' ),
    ) );
    ?>
    </nav>
  <?php 
}
?>

  <?php 
wp_reset_postdata();
?>

</div>