<?php

/**
 * @package OpenUserMapPlugin
 */
namespace OpenUserMapPlugin\Pages;

use OpenUserMapPlugin\Base\BaseController;
class Settings extends BaseController {
    public function register() {
        add_action( 'init', array($this, 'migrate_deprecated_settings') );
        add_action( 'admin_menu', array($this, 'add_admin_pages') );
        add_action( 'admin_init', array($this, 'add_plugin_settings') );
        add_action( 'admin_init', array($this, 'add_oum_wizard') );
        add_action( 'admin_notices', array($this, 'show_getting_started_notice') );
        add_action( 'wp_ajax_oum_dismiss_getting_started_notice', array($this, 'getting_started_dismiss_notice') );
        add_action( 'wp_ajax_oum_csv_export', array($this, 'csv_export') );
        add_action( 'wp_ajax_oum_csv_import', array($this, 'csv_import') );
    }

    public function add_admin_pages() {
        //add_options_page('Open User Map', 'Open User Map', 'manage_options', 'open_user_map', array($this, 'admin_index'));
        add_submenu_page(
            'edit.php?post_type=oum-location',
            'Settings',
            'Settings',
            'manage_options',
            'open-user-map-settings',
            array($this, 'admin_index')
        );
    }

    public function add_plugin_settings() {
        register_setting( 'open-user-map-settings-getting-started-notice', 'oum_getting_started_notice_dismissed' );
        register_setting( 'open-user-map-settings-group', 'oum_map_style', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_tile_provider_mapbox_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_marker_icon', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_marker_user_icon', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_map_size', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_map_height', array(
            'sanitize_callback' => array($this, 'validate_size'),
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_map_size_mobile', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_map_height_mobile', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_start_lat', array(
            'sanitize_callback' => array($this, 'validate_geocoordinate'),
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_start_lng', array(
            'sanitize_callback' => array($this, 'validate_geocoordinate'),
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_start_zoom', array(
            'sanitize_callback' => array($this, 'validate_zoom'),
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_fixed_map_bounds', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_minimum_zoom_level', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_title', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_title_required', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_title_maxlength', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_title_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_map_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_hide_address', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_address', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_geosearch_provider', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_geosearch_provider_geoapify_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_geosearch_provider_here_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_geosearch_provider_mapbox_key', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_searchbar', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_searchbar_type', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_searchaddress_button', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_searchaddress_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_searchmarkers_button', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_searchmarkers_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_searchmarkers_zoom', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_gmaps_link', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_address_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_description', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_description_required', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_description_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_upload_media_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_image', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_image_required', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_audio', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_audio_required', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_custom_fields', array(
            'sanitize_callback' => array($this, 'validate_array'),
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_scrollwheel_zoom_map', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_cluster', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_fullscreen', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_currentlocation', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_disable_oum_attribution', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_max_image_filesize', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_max_audio_filesize', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_action_after_submit', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_thankyou_redirect', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_thankyou_headline', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_thankyou_text', array(
            'sanitize_callback' => 'wp_kses_post',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_addanother_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_plus_button_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_submit_button_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_form_headline', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_user_notification', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_user_notification_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_user_notification_subject', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_admin_notification', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_admin_notification_email', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_admin_notification_subject', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_user_restriction', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_redirect_to_registration', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_auto_publish', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_auto_publish_for_everyone', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_add_user_location', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_marker_types', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_empty_marker_type', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_multiple_marker_types', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_collapse_filter', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_marker_types_label', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_ui_color', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_add_location', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_single_page', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_location_date', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_enable_regions', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_regions_layout_style', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group', 'oum_custom_js', array(
            'sanitize_callback' => 'wp_kses_post',
        ) );
        register_setting( 'open-user-map-settings-group-wizard-1', 'oum_wizard_usecase', array(
            'sanitize_callback' => array($this, 'process_wizard_usecase'),
        ) );
        register_setting( 'open-user-map-settings-group-wizard-1', 'oum_wizard_usecase_done', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( 'open-user-map-settings-group-wizard-2', 'oum_wizard_finish_done', array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
    }

    public function migrate_deprecated_settings() {
        // Variant 1: invert old settings
        $options = array(
            'oum_disable_add_location'  => 'oum_enable_add_location',
            'oum_disable_title'         => 'oum_enable_title',
            'oum_disable_address'       => 'oum_enable_address',
            'oum_disable_gmaps_link'    => 'oum_enable_gmaps_link',
            'oum_disable_description'   => 'oum_enable_description',
            'oum_disable_image'         => 'oum_enable_image',
            'oum_disable_audio'         => 'oum_enable_audio',
            'oum_disable_cluster'       => 'oum_enable_cluster',
            'oum_disable_fullscreen'    => 'oum_enable_fullscreen',
            'oum_disable_searchaddress' => 'oum_enable_searchaddress_button',
        );
        foreach ( $options as $old_option => $new_option ) {
            $old_setting = get_option( $old_option );
            // do nothing if old option doesnt exist
            if ( $old_setting === false ) {
                //error_log('Open User Map: Deprecated option ' . $old_option . ' does not exist. Nothing to do.');
                continue;
            }
            if ( empty( $old_setting ) ) {
                $new_setting = 'on';
            } else {
                $new_setting = '';
            }
            //update (or create) new
            update_option( $new_option, $new_setting );
            error_log( 'Open User Map: Update new option ' . $new_option . ' from old option ' . $old_option . '. New Value: ' . $new_setting );
            //delete old
            delete_option( $old_option );
            error_log( 'Open User Map: Deleting old option ' . $new_option . '.' );
        }
        // Variant 2: rename settings (keep value)
        $options = array(
            'oum_enable_searchaddress' => 'oum_enable_searchbar',
        );
        foreach ( $options as $old_option => $new_option ) {
            $old_setting = get_option( $old_option );
            // do nothing if old option doesnt exist
            if ( $old_setting === false ) {
                //error_log('Open User Map: Deprecated option ' . $old_option . ' does not exist. Nothing to do.');
                continue;
            }
            //update (or create) new
            update_option( $new_option, $old_setting );
            error_log( 'Open User Map: Update new option ' . $new_option . ' from old option ' . $old_option . '. New Value: ' . $old_setting );
            //delete old
            delete_option( $old_option );
            error_log( 'Open User Map: Deleting old option ' . $new_option . '.' );
        }
        // Variant 3: change value of a setting
        if ( get_option( 'oum_map_style' ) == 'Stamen.TonerLite' ) {
            update_option( 'oum_map_style', 'CartoDB.Positron' );
        }
        if ( get_option( 'oum_map_style' ) == 'Stadia.StamenTonerLite' ) {
            update_option( 'oum_map_style', 'CartoDB.Positron' );
        }
    }

    public function add_oum_wizard() {
        if ( get_option( 'oum_enable_add_location' ) !== 'on' && get_option( 'oum_enable_add_location' ) !== '' || get_option( 'oum_wizard_usecase_done' ) && !get_option( 'oum_wizard_finish_done' ) ) {
            add_action( 'admin_body_class', function ( $class ) {
                $class .= ' oum-settings-wizard';
                return $class;
            } );
        }
    }

    public function admin_index() {
        require_once $this->plugin_path . 'templates/page-backend-settings.php';
    }

    public static function validate_geocoordinate( $input ) {
        // Validation
        $geocoordinate_validated = floatval( str_replace( ',', '.', sanitize_text_field( $input ) ) );
        if ( !$geocoordinate_validated && $geocoordinate_validated != '0' ) {
            $geocoordinate_validated = '';
        }
        return $geocoordinate_validated;
    }

    public static function validate_zoom( $input ) {
        // Validation
        $zoom_validated = floatval( str_replace( ',', '.', sanitize_text_field( $input ) ) );
        if ( !$zoom_validated ) {
            $zoom_validated = '';
        }
        return $zoom_validated;
    }

    public static function validate_size( $input ) {
        // Add px if it's missing
        $size_validated = ( is_numeric( $input ) ? $input . 'px' : sanitize_text_field( $input ) );
        return $size_validated;
    }

    public function validate_array( $array ) {
        // if not an array
        if ( !is_array( $array ) ) {
            return '';
        }
        foreach ( $array as &$value ) {
            if ( !is_array( $value ) ) {
                // sanitize if value is not an array
                $value = sanitize_text_field( $value );
            } else {
                // go inside this function again
                $this->validate_array( $value );
            }
        }
        return $array;
    }

    public static function show_getting_started_notice() {
        // return if already dismissed
        if ( get_option( 'oum_getting_started_notice_dismissed' ) ) {
            return;
        }
        $screen = get_current_screen();
        //error_log(print_r($screen, true));
        // Only render this notice on a Open User Map page.
        if ( !$screen || 'edit.php?post_type=oum-location' !== $screen->parent_file ) {
            return;
        }
        // Render the notice's HTML.
        echo '<div class="notice oum-getting-started-notice notice-success is-dismissible">';
        echo sprintf( __( '<h3>🚀 Getting started with Open User Map</h3><ol><li>Use the page editor or Elementor to insert the <b>"Open User Map"</b> block onto a page. Alternatively, you can use the shortcode <code>[open-user-map]</code></li><li>You can <a href="%s">manage Locations</a> under <i>Open User Map > All Locations</i></li><li><a href="%s">Customize</a> styles and features under <i>Open User Map > Settings</i></li></ol>', 'open-user-map' ), 'edit.php?post_type=oum-location', 'edit.php?post_type=oum-location&page=open-user-map-settings' );
        echo '</div>';
    }

    public static function getting_started_dismiss_notice() {
        update_option( 'oum_getting_started_notice_dismissed', 1 );
    }

    public function process_wizard_usecase( $input ) {
        // Adjust OUM settings based on the wizard
        if ( $input == 1 ) {
            // everybody
            update_option( 'oum_enable_add_location', 'on' );
        } elseif ( $input == 2 ) {
            //just me
            update_option( 'oum_enable_add_location', '' );
            //disable fullscreen button
            update_option( 'oum_enable_fullscreen', '' );
            //disable searchbar
            update_option( 'oum_enable_searchbar', '' );
            //disable search address button
            update_option( 'oum_enable_searchaddress_button', '' );
            //disable search markers button
            update_option( 'oum_enable_searchmarkers_button', '' );
            //disable current location button
            update_option( 'oum_enable_currentlocation', '' );
            //disable location date
            update_option( 'oum_enable_location_date', '' );
        }
        return $input;
    }

    public function csv_export() {
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'oum_csv_export' ) {
            // Initialize error handling
            $error = new \WP_Error();
            // TODO: Exit if no nonce
            if ( $error->has_errors() ) {
                // Return errors
                wp_send_json_error( $error );
            } else {
                // EXPORT
                $all_oum_locations = get_posts( array(
                    'post_type'      => 'oum-location',
                    'posts_per_page' => -1,
                    'fields'         => 'ids',
                ) );
                $locations_list = array();
                foreach ( $all_oum_locations as $post_id ) {
                    // get fields
                    $location = array(
                        'post_id'      => $post_id,
                        'title'        => oum_get_location_value( 'title', $post_id ),
                        'image'        => oum_get_location_value( 'image', $post_id, true ),
                        'audio'        => oum_get_location_value( 'audio', $post_id, true ),
                        'type'         => oum_get_location_value( 'type', $post_id ),
                        'type'         => '[' . oum_get_location_value( 'type', $post_id ) . ']',
                        'address'      => oum_get_location_value( 'address', $post_id ),
                        'lat'          => oum_get_location_value( 'lat', $post_id ),
                        'lng'          => oum_get_location_value( 'lng', $post_id ),
                        'text'         => oum_get_location_value( 'text', $post_id ),
                        'notification' => oum_get_location_value( 'notification', $post_id ),
                        'author_name'  => oum_get_location_value( 'author_name', $post_id ),
                        'author_email' => oum_get_location_value( 'author_email', $post_id ),
                        'user_id'      => oum_get_location_value( 'user_id', $post_id ),
                    );
                    //get custom fields
                    $location_customfields = array();
                    $available_custom_fields = get_option( 'oum_custom_fields', array() );
                    // all available custom fields
                    foreach ( $available_custom_fields as $custom_field_id => $custom_field ) {
                        $value = oum_get_location_value( $custom_field['label'], $post_id, true );
                        // transform array to [A|B|C] (also empty array)
                        if ( is_array( $value ) ) {
                            $value = '[' . implode( '|', $value ) . ']';
                        }
                        $location_customfields['CUSTOMFIELD_' . $custom_field_id . '_' . $custom_field['label']] = $value;
                    }
                    $location_data = array_merge( $location, $location_customfields );
                    $locations_list[] = $location_data;
                }
                //preparing values for CSV
                foreach ( $locations_list as $i => $row ) {
                    foreach ( $row as $j => $val ) {
                        //escape "
                        $locations_list[$i][$j] = str_replace( '"', '""', $val );
                    }
                }
                echo json_encode( $locations_list );
                die;
            }
        }
    }

    public function detectDelimiter( $csvFile ) {
        $delimiters = array(
            ';'  => 0,
            ','  => 0,
            "\t" => 0,
            "|"  => 0,
        );
        $handle = fopen( $csvFile, "r" );
        $firstLine = fgets( $handle );
        fclose( $handle );
        foreach ( $delimiters as $delimiter => &$count ) {
            $count = count( str_getcsv( $firstLine, $delimiter ) );
        }
        return array_search( max( $delimiters ), $delimiters );
    }

    public function csv_import() {
        if ( isset( $_POST['action'] ) && $_POST['action'] == 'oum_csv_import' ) {
            // Initialize error handling
            $error = new \WP_Error();
            // Dont save without nonce
            if ( !isset( $_POST['oum_location_nonce'] ) ) {
                $error->add( '002', 'Not allowed' );
            }
            // Dont save if nonce is incorrect
            $nonce = $_POST['oum_location_nonce'];
            if ( !wp_verify_nonce( $nonce, 'oum_location' ) ) {
                $error->add( '002', 'Not allowed' );
            }
            // Exit if no file
            if ( !isset( $_POST['url'] ) ) {
                $error->add( '001', 'File upload failed.' );
            }
            // TODO: Exit if no CSV filetype
            if ( $error->has_errors() ) {
                // Return errors
                wp_send_json_error( $error );
            } else {
                // IMPORT
                $path_1 = wp_get_upload_dir()['basedir'];
                $path_2 = explode( '/uploads/', $_POST['url'] )['1'];
                $csv_file = $path_1 . '/' . $path_2;
                $delimiter = $this->detectDelimiter( $csv_file );
                // parse csv file to array
                $file_to_read = fopen( $csv_file, 'r' );
                while ( !feof( $file_to_read ) ) {
                    $rows[] = fgetcsv( $file_to_read, 99999, $delimiter );
                }
                fclose( $file_to_read );
                // build assoziative array
                array_walk( $rows, function ( &$a ) use($rows) {
                    $a = array_combine( $rows[0], $a );
                    if ( is_array( $a ) ) {
                        $a = array_combine( $rows[0], $a );
                    } else {
                        error_log( 'Open User Map: a row is not of type array and therefor will not be imported' );
                    }
                } );
                array_shift( $rows );
                # remove column header
                $locations = $rows;
                // Create or Update the posts
                $cnt_imported_locations = 0;
                foreach ( $locations as $location ) {
                    // Marker categories
                    $types = $location['type'];
                    if ( strpos( $types, '[', 0 ) !== false && strpos( $types, ']', -1 ) !== false ) {
                        $types = substr( $types, 1, -1 );
                        $types = explode( '|', $types );
                    }
                    // update or insert post
                    if ( $location['post_id'] == '' ) {
                        $location['post_id'] = 0;
                    }
                    $insert_post = wp_insert_post( array(
                        'ID'         => $location['post_id'],
                        'post_type'  => 'oum-location',
                        'post_title' => $location['title'],
                        'post_name'  => sanitize_title( $location['title'] ),
                        'tax_input'  => array(
                            'oum-type' => $types,
                        ),
                    ) );
                    if ( $insert_post ) {
                        // Add fields
                        $fields = array(
                            'oum_location_nonce'        => $nonce,
                            'oum_location_image'        => $location['image'],
                            'oum_location_audio'        => $location['audio'],
                            'oum_location_address'      => $location['address'],
                            'oum_location_lat'          => $location['lat'],
                            'oum_location_lng'          => $location['lng'],
                            'oum_location_text'         => $location['text'],
                            'oum_location_notification' => $location['notification'],
                            'oum_location_author_name'  => $location['author_name'],
                            'oum_location_author_email' => $location['author_email'],
                        );
                        // Add custom fields
                        $customfields = array_filter( $location, function ( $val, $key ) {
                            return strpos( $key, 'CUSTOMFIELD' ) === 0;
                        }, ARRAY_FILTER_USE_BOTH );
                        foreach ( $customfields as $key => $val ) {
                            $id = explode( '_', $key )[1];
                            // transform [A|B|C] to array
                            if ( strpos( $val, '[', 0 ) !== false && strpos( $val, ']', -1 ) !== false ) {
                                $val = substr( $val, 1, -1 );
                                $val = explode( '|', $val );
                            }
                            $fields['oum_location_custom_fields'][$id] = $val;
                        }
                        // Validate and Save
                        \OpenUserMapPlugin\Base\LocationController::save_fields( $insert_post, $fields );
                        $cnt_imported_locations++;
                    }
                }
                // return success message
                wp_send_json_success( $cnt_imported_locations . ' Locations have been imported successfully.' );
            }
        }
    }

}
