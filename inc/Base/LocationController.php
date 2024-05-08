<?php

/**
 * @package OpenUserMapPlugin
 */
namespace OpenUserMapPlugin\Base;

use OpenUserMapPlugin\Base\BaseController;
class LocationController extends BaseController {
    public $settings;

    public function register() {
        // CPT: Location
        add_action( 'init', array($this, 'location_cpt') );
        add_action( 'admin_init', array($this, 'oum_capabilities') );
        add_action( 'add_meta_boxes', array($this, 'add_meta_box') );
        add_action( 'save_post', array($this, 'save_fields') );
        add_action( 'manage_oum-location_posts_columns', array($this, 'set_custom_location_columns') );
        add_action(
            'manage_oum-location_posts_custom_column',
            array($this, 'set_custom_location_columns_data'),
            10,
            2
        );
        // this method has 2 attributes
        add_action( 'admin_menu', array($this, 'add_pending_counter_to_menu') );
        add_filter( 'the_content', array($this, 'default_location_content') );
    }

    /**
     * CPT: Location
     */
    public static function location_cpt() {
        $labels = array(
            'name'               => __( 'Locations', 'open-user-map' ),
            'singular_name'      => __( 'Location', 'open-user-map' ),
            'add_new'            => __( 'Add new Location', 'open-user-map' ),
            'add_new_item'       => __( 'Add new Location', 'open-user-map' ),
            'edit_item'          => __( 'Edit Location', 'open-user-map' ),
            'new_item'           => __( 'New Location', 'open-user-map' ),
            'all_items'          => __( 'All Locations', 'open-user-map' ),
            'view_item'          => __( 'View Location', 'open-user-map' ),
            'search_items'       => __( 'Search Locations', 'open-user-map' ),
            'not_found'          => __( 'No Locations found', 'open-user-map' ),
            'not_found_in_trash' => __( 'No Location in trash', 'open-user-map' ),
            'parent_item_colon'  => '',
            'menu_name'          => __( 'Open User Map', 'open-user-map' ),
        );
        $args = array(
            'labels'              => $labels,
            'capability_type'     => 'oum-location',
            'map_meta_cap'        => true,
            'description'         => __( 'Location', 'open-user-map' ),
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'exclude_from_search' => true,
            'show_in_nav_menus'   => false,
            'has_archive'         => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-location-alt',
            'supports'            => array(
                'title',
                'author',
                'thumbnail',
                'excerpt'
            ),
        );
        register_post_type( 'oum-location', $args );
    }

    /**
     * Assign default capabilities to default user roles (same as 'post')
     */
    public function oum_capabilities() {
        // Administrator, Editor
        $roles = array('editor', 'administrator');
        foreach ( $roles as $the_role ) {
            $role = get_role( $the_role );
            if ( !is_null( $role ) ) {
                $role->add_cap( 'read_oum-location' );
                $role->add_cap( 'read_private_oum-locations' );
                $role->add_cap( 'edit_oum-location' );
                $role->add_cap( 'edit_oum-locations' );
                $role->add_cap( 'edit_others_oum-locations' );
                $role->add_cap( 'edit_published_oum-locations' );
                $role->add_cap( 'edit_private_oum-locations' );
                $role->add_cap( 'publish_oum-locations' );
                $role->add_cap( 'delete_oum-locations' );
                $role->add_cap( 'delete_others_oum-locations' );
                $role->add_cap( 'delete_private_oum-locations' );
                $role->add_cap( 'delete_published_oum-locations' );
            }
        }
        // Author
        $role = get_role( 'author' );
        if ( !is_null( $role ) ) {
            $role->add_cap( 'edit_oum-locations' );
            $role->add_cap( 'edit_published_oum-locations' );
            $role->add_cap( 'publish_oum-locations' );
            $role->add_cap( 'delete_oum-locations' );
            $role->add_cap( 'delete_published_oum-locations' );
        }
        // Contributor
        $role = get_role( 'contributor' );
        if ( !is_null( $role ) ) {
            $role->add_cap( 'edit_oum-locations' );
            $role->add_cap( 'delete_oum-locations' );
        }
    }

    public function add_meta_box() {
        add_meta_box(
            'location_customfields',
            __( 'Open User Map Location Settings', 'open-user-map' ),
            array($this, 'render_customfields_box'),
            'oum-location',
            'normal',
            'high'
        );
    }

    public function render_customfields_box( $post ) {
        wp_nonce_field( 'oum_location', 'oum_location_nonce' );
        $data = get_post_meta( $post->ID, '_oum_location_key', true );
        $address = ( isset( $data['address'] ) ? $data['address'] : '' );
        $lat = ( isset( $data['lat'] ) ? $data['lat'] : '' );
        $lng = ( isset( $data['lng'] ) ? $data['lng'] : '' );
        $text = ( isset( $data['text'] ) ? $data['text'] : '' );
        $image = get_post_meta( $post->ID, '_oum_location_image', true );
        $has_image = ( isset( $image ) && $image != '' ? 'has-image' : '' );
        $image_tag = ( $has_image ? '<img src="' . esc_attr( $image ) . '" style="width: 100%;">' : '' );
        $audio = get_post_meta( $post->ID, '_oum_location_audio', true );
        $has_audio = ( isset( $audio ) && $audio != '' ? 'has-audio' : '' );
        $audio_tag = ( $has_audio ? '<audio controls="controls" style="width:100%"><source type="audio/mp4" src="' . esc_attr( $audio ) . '"><source type="audio/mpeg" src="' . esc_attr( $audio ) . '"><source type="audio/wav" src="' . esc_attr( $audio ) . '"></audio>' : '' );
        $notification = ( isset( $data['notification'] ) ? $data['notification'] : '' );
        $author_name = ( isset( $data['author_name'] ) ? $data['author_name'] : '' );
        $author_email = ( isset( $data['author_email'] ) ? $data['author_email'] : '' );
        $text_notify_me_on_publish_label = ( get_option( 'oum_user_notification_label' ) ? get_option( 'oum_user_notification_label' ) : $this->oum_user_notification_label_default );
        $text_notify_me_on_publish_name = __( 'Your name', 'open-user-map' );
        $text_notify_me_on_publish_email = __( 'Your email', 'open-user-map' );
        $notified = get_post_meta( $post->ID, '_oum_location_notified', true );
        $notified_tag = ( isset( $notified ) && $notified != '' ? '<p>User has been notified on ' . date( "Y-m-d H:i:s", $notified ) . '</p>' : '' );
        $user_id = ( isset( $data['user_id'] ) ? $data['user_id'] : '' );
        // Set map style
        $map_style = ( get_option( 'oum_map_style' ) ? get_option( 'oum_map_style' ) : 'Esri.WorldStreetMap' );
        $oum_tile_provider_mapbox_key = get_option( 'oum_tile_provider_mapbox_key', '' );
        $marker_icon = ( get_option( 'oum_marker_icon' ) ? get_option( 'oum_marker_icon' ) : 'default' );
        $marker_user_icon = get_option( 'oum_marker_user_icon' );
        $meta_custom_fields = ( isset( $data['custom_fields'] ) ? $data['custom_fields'] : false );
        $active_custom_fields = get_option( 'oum_custom_fields' );
        // render view
        require_once "{$this->plugin_path}/templates/page-backend-location.php";
    }

    /**
     * Save Location Fields (Backend)
     */
    public static function save_fields( $post_id, $fields = array() ) {
        $location_data = $_REQUEST;
        // Set data source ($_REQUEST or $fields)
        if ( !empty( $fields ) ) {
            $location_data = $fields;
            $location_data['post_type'] = 'oum-location';
        }
        // Dont save if not a location
        if ( !isset( $location_data['post_type'] ) || $location_data['post_type'] != 'oum-location' ) {
            return $post_id;
        }
        // Set post thumbnail and excerpt when saving inline (Quick Edit) and exit
        if ( isset( $location_data['action'] ) && in_array( $location_data['action'], array('edit', 'inline-save') ) ) {
            // Dont save if wordpress just auto-saves
            if ( defined( 'DOING AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }
            // Dont save if user is not allowed to do
            if ( !(current_user_can( 'edit_post', $post_id ) || current_user_can( 'edit_oum-locations' )) ) {
                return $post_id;
            }
            // Set featured image if not set
            if ( !has_post_thumbnail( $post_id ) ) {
                // Set location image as post thumbnail
                $post_image = oum_get_location_value( 'image', $post_id, true );
                if ( $post_image ) {
                    // Download image from the specified URL and upload it to the WordPress media library
                    $upload = media_sideload_image(
                        $post_image,
                        $post_id,
                        null,
                        'src'
                    );
                    // Check if upload was successful
                    if ( !is_wp_error( $upload ) ) {
                        // Find the uploaded image in the database
                        $attachment_id = attachment_url_to_postid( $upload );
                        // Check if the image was found
                        if ( $attachment_id ) {
                            // Set the image as the featured image for the post
                            set_post_thumbnail( $post_id, $attachment_id );
                        }
                    }
                }
            }
            // Set excerpt if not set
            if ( get_the_excerpt( $post_id ) == '' ) {
                // Set location text as post excerpt
                $max_length = 400;
                $post_text = oum_get_location_value( 'text', $post_id, true );
                $text = wp_strip_all_tags( $post_text );
                if ( $text ) {
                    if ( strlen( $text ) > $max_length ) {
                        $text = substr( $text, 0, $max_length );
                        $last_space = strrpos( $text, ' ' );
                        if ( $last_space !== false ) {
                            $text = substr( $text, 0, $last_space );
                        }
                        $text .= '...';
                    }
                    $post = array(
                        'ID'           => $post_id,
                        'post_excerpt' => sanitize_text_field( $text ),
                    );
                    wp_update_post( $post );
                }
            }
            return $post_id;
        }
        // Dont save without nonce
        if ( !isset( $location_data['oum_location_nonce'] ) ) {
            return $post_id;
        }
        // Dont save if nonce is incorrect
        $nonce = $location_data['oum_location_nonce'];
        if ( !wp_verify_nonce( $nonce, 'oum_location' ) ) {
            return $post_id;
        }
        // Dont save if wordpress just auto-saves
        if ( defined( 'DOING AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // Dont save if user is not allowed to do
        if ( !(current_user_can( 'edit_post', $post_id ) || current_user_can( 'edit_oum-locations' )) ) {
            return $post_id;
        }
        // Validation
        $lat_validated = ( isset( $location_data['oum_location_lat'] ) ? floatval( str_replace( ',', '.', sanitize_text_field( $location_data['oum_location_lat'] ) ) ) : '' );
        $lng_validated = ( isset( $location_data['oum_location_lng'] ) ? floatval( str_replace( ',', '.', sanitize_text_field( $location_data['oum_location_lng'] ) ) ) : '' );
        $data = array(
            'address'      => ( isset( $location_data['oum_location_address'] ) ? sanitize_text_field( $location_data['oum_location_address'] ) : '' ),
            'lat'          => $lat_validated,
            'lng'          => $lng_validated,
            'text'         => ( isset( $location_data['oum_location_text'] ) ? wp_kses_post( $location_data['oum_location_text'] ) : '' ),
            'author_name'  => ( isset( $location_data['oum_location_author_name'] ) ? sanitize_text_field( $location_data['oum_location_author_name'] ) : '' ),
            'author_email' => ( isset( $location_data['oum_location_author_email'] ) ? sanitize_text_field( $location_data['oum_location_author_email'] ) : '' ),
        );
        if ( isset( $location_data['oum_location_notification'] ) ) {
            $data['notification'] = sanitize_text_field( $location_data['oum_location_notification'] );
        }
        if ( isset( $location_data['oum_location_custom_fields'] ) && is_array( $location_data['oum_location_custom_fields'] ) ) {
            $data['custom_fields'] = $location_data['oum_location_custom_fields'];
        }
        update_post_meta( $post_id, '_oum_location_key', $data );
        if ( isset( $location_data['oum_location_image'] ) ) {
            // validate & store image seperately (to avoid serialized URLs [bad for search & replace due to domain change])
            $data_image = esc_url_raw( $location_data['oum_location_image'] );
            update_post_meta( $post_id, '_oum_location_image', $data_image );
            // Set featured image if not set
            if ( !has_post_thumbnail( $post_id ) ) {
                // Set location image as post thumbnail
                $post_image = oum_get_location_value( 'image', $post_id, true );
                if ( $post_image ) {
                    // Download image from the specified URL and upload it to the WordPress media library
                    $upload = media_sideload_image(
                        $post_image,
                        $post_id,
                        null,
                        'src'
                    );
                    // Check if upload was successful
                    if ( !is_wp_error( $upload ) ) {
                        // Find the uploaded image in the database
                        $attachment_id = attachment_url_to_postid( $upload );
                        // Check if the image was found
                        if ( $attachment_id ) {
                            // Set the image as the featured image for the post
                            set_post_thumbnail( $post_id, $attachment_id );
                        }
                    }
                }
            }
        }
        if ( isset( $location_data['oum_location_audio'] ) ) {
            // validate & store audio seperately (to avoid serialized URLs [bad for search & replace due to domain change])
            $data_audio = esc_url_raw( $location_data['oum_location_audio'] );
            update_post_meta( $post_id, '_oum_location_audio', $data_audio );
        }
        // Set excerpt if not set
        if ( get_the_excerpt( $post_id ) == '' ) {
            // Set location text as post excerpt
            $max_length = 400;
            $post_text = oum_get_location_value( 'text', $post_id, true );
            $text = wp_strip_all_tags( $post_text );
            if ( $text ) {
                if ( strlen( $text ) > $max_length ) {
                    $text = substr( $text, 0, $max_length );
                    $last_space = strrpos( $text, ' ' );
                    if ( $last_space !== false ) {
                        $text = substr( $text, 0, $last_space );
                    }
                    $text .= '...';
                }
                $post = array(
                    'ID'           => $post_id,
                    'post_excerpt' => sanitize_text_field( $text ),
                );
                wp_update_post( $post );
            }
        }
    }

    public function set_custom_location_columns( $columns ) {
        // preserve default columns
        $title = $columns['title'];
        $date = $columns['date'];
        unset($columns['title'], $columns['date']);
        $columns['title'] = $title;
        $columns['text'] = __( 'Text', 'open-user-map' );
        $columns['address'] = __( 'Subtitle', 'open-user-map' );
        $columns['geocoordinates'] = __( 'Coordinates', 'open-user-map' );
        $columns['date'] = $date;
        return $columns;
    }

    public function set_custom_location_columns_data( $column, $post_id ) {
        $data = get_post_meta( $post_id, '_oum_location_key', true );
        $text = ( isset( $data['text'] ) ? $data['text'] : '' );
        $address = ( isset( $data['address'] ) ? $data['address'] : '' );
        $lat = ( isset( $data['lat'] ) ? $data['lat'] : '' );
        $lng = ( isset( $data['lng'] ) ? $data['lng'] : '' );
        switch ( $column ) {
            case 'text':
                echo esc_html( $text );
                break;
            case 'address':
                echo esc_html( $address );
                break;
            case 'geocoordinates':
                echo esc_attr( $lat ) . ', ' . esc_attr( $lng );
                break;
            case 'notification':
                echo esc_attr( $notification );
                break;
            default:
                break;
        }
    }

    public function add_pending_counter_to_menu() {
        global $menu;
        $count = count( get_posts( array(
            'post_type'      => 'oum-location',
            'post_status'    => 'pending',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) ) );
        $menu_item = wp_list_filter( $menu, array(
            2 => 'edit.php?post_type=oum-location',
        ) );
        if ( !empty( $menu_item ) && $count >= 1 ) {
            $menu_item_position = key( $menu_item );
            // get the array key (position) of the element
            $menu[$menu_item_position][0] .= ' <span class="awaiting-mod">' . $count . '</span>';
        }
    }

    /**
     * Get a value from a location
     */
    public static function get_location_value( $attr, $post_id, $raw = false ) {
        $location = get_post_meta( $post_id, '_oum_location_key', true );
        $custom_field_ids = get_option( 'oum_custom_fields', array() );
        // get all available custom fields
        $types = get_terms( array(
            'taxonomy'   => 'oum-type',
            'hide_empty' => false,
        ) );
        // get all available types
        $value = '';
        if ( $attr == 'title' ) {
            // GET TITLE
            $value = get_the_title( $post_id );
        } elseif ( $attr == 'image' ) {
            // GET IMAGE
            $image = get_post_meta( $post_id, '_oum_location_image', true );
            $has_image = ( isset( $image ) && $image != '' ? 'has-image' : '' );
            if ( !$raw ) {
                $value = ( $has_image ? '<img src="' . esc_attr( $image ) . '">' : '' );
            } else {
                $value = ( $has_image ? esc_attr( $image ) : '' );
            }
        } elseif ( $attr == 'audio' ) {
            // GET AUDIO
            $audio = get_post_meta( $post_id, '_oum_location_audio', true );
            $has_audio = ( isset( $audio ) && $audio != '' ? 'has-audio' : '' );
            if ( !$raw ) {
                $value = ( $has_audio ? '<audio controls="controls" style="width:100%"><source type="audio/mp4" src="' . esc_attr( $audio ) . '"><source type="audio/mpeg" src="' . esc_attr( $audio ) . '"><source type="audio/wav" src="' . esc_attr( $audio ) . '"></audio>' : '' );
            } else {
                $value = ( $has_audio ? esc_attr( $audio ) : '' );
            }
        } elseif ( $attr == 'type' ) {
            // GET TYPE
            $location_types = ( get_the_terms( $post_id, 'oum-type' ) && !is_wp_error( get_the_terms( $post_id, 'oum-type' ) ) ? get_the_terms( $post_id, 'oum-type' ) : false );
            if ( isset( $location_types ) && is_array( $location_types ) && count( $location_types ) == 1 && !get_option( 'oum_enable_multiple_marker_types' ) ) {
                $value = $location_types[0]->name;
            } else {
                $value = '';
                if ( isset( $location_types ) && is_array( $location_types ) ) {
                    $value = implode( '|', wp_list_pluck( $location_types, 'name' ) );
                }
            }
        } elseif ( $attr == 'map' ) {
            // GET MAP
            $plugin_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
            $map_style = ( get_option( 'oum_map_style' ) ? get_option( 'oum_map_style' ) : 'Esri.WorldStreetMap' );
            $oum_tile_provider_mapbox_key = get_option( 'oum_tile_provider_mapbox_key', '' );
            $lat = $location['lat'];
            $lng = $location['lng'];
            $zoom = '13';
            $marker_icon = ( get_option( 'oum_marker_icon' ) ? get_option( 'oum_marker_icon' ) : 'default' );
            $marker_user_icon = get_option( 'oum_marker_user_icon' );
            $marker_icon_url = ( $marker_icon == 'user1' && $marker_user_icon ? esc_url( $marker_user_icon ) : esc_url( $plugin_url ) . 'src/leaflet/images/marker-icon_' . esc_attr( $marker_icon ) . '-2x.png' );
            $marker_shadow_url = esc_url( $plugin_url ) . 'src/leaflet/images/marker-shadow.png';
            $value = '<div id="mapRenderLocation" data-lat="' . $lat . '" data-lng="' . $lng . '" data-zoom="' . $zoom . '" data-mapstyle="' . $map_style . '" data-tile_provider_mapbox_key="' . $oum_tile_provider_mapbox_key . '" data-marker_icon_url="' . $marker_icon_url . '" data-marker_shadow_url="' . $marker_shadow_url . '" class="open-user-map-location-map leaflet-map map-style_' . $map_style . '"></div>';
        } elseif ( $attr == 'route' ) {
            // GET GOOGLE ROUTE LINK
            $lat = esc_attr( $location['lat'] );
            $lng = esc_attr( $location['lng'] );
            $text = ( $location['address'] ? $location['address'] : __( 'Route on Google Maps', 'open-user-map' ) );
            $value = '<a title="' . __( 'go to Google Maps', 'open-user-map' ) . '" href="https://www.google.com/maps/search/?api=1&amp;query=' . $lat . '%2C' . $lng . '" target="_blank">' . $text . '</a>';
        } elseif ( isset( $location[$attr] ) ) {
            // GET DEFAULT FIELD
            $value = $location[$attr];
        } else {
            // GET CUSTOM FIELD
            foreach ( $custom_field_ids as $custom_field_id => $custom_field ) {
                if ( strtolower( $custom_field['label'] ) == strtolower( $attr ) && isset( $location['custom_fields'][$custom_field_id] ) ) {
                    $value = $location['custom_fields'][$custom_field_id];
                    break;
                }
            }
        }
        if ( !$raw ) {
            //change array to list
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }
        }
        return $value;
    }

    public function default_location_content( $content ) {
        // Check if we're inside the main loop in a single Post of type 'custom_post_type'.
        if ( is_singular( 'oum-location' ) && in_the_loop() && is_main_query() ) {
            // Check if the content is empty
            if ( empty( trim( $content ) ) ) {
                // Custom content to display if the original content is empty
                $custom_content = '
                <!-- wp:group {"className":"open-user-map-single-default-template","layout":{"type":"default"}} -->
                <div class="wp-block-group open-user-map-single-default-template">
                
                <!-- wp:columns -->
                <div class="wp-block-columns">
                
                    <!-- wp:column {"width":"66.66%"} -->
                    <div class="wp-block-column" style="flex-basis:66.66%">

                    <!-- wp:shortcode -->
                    [open-user-map-location value="text"]
                    <!-- /wp:shortcode -->
                
                </div>

                <!-- /wp:column -->

                <!-- wp:column {"width":"33.33%"} -->
                <div class="wp-block-column" style="flex-basis:33.33%">

                    <!-- wp:shortcode -->
                    [open-user-map-location value="map"]
                    <!-- /wp:shortcode -->

                    <!-- wp:shortcode -->
                    [open-user-map-location value="route"]
                    <!-- /wp:shortcode -->

                    <!-- wp:shortcode -->
                    [open-user-map-location value="type"]
                    <!-- /wp:shortcode -->

                </div>
                <!-- /wp:column -->
                </div>
                <!-- /wp:columns -->
                </div>
                <!-- /wp:group -->
                ';
                return $custom_content;
            }
        }
        // Return the original content if it's not empty or if the conditions are not met
        return $content;
    }

}
