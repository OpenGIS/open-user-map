<?php

/**
 * @package OpenUserMapPlugin
 */
namespace OpenUserMapPlugin\Base;

class BaseController {
    public $plugin_path;

    public $plugin_url;

    public $plugin_version;

    public $plugin;

    public $post_status;

    public $oum_title_label_default;

    public $oum_map_label_default;

    public $oum_address_label_default;

    public $oum_description_label_default;

    public $oum_upload_media_label_default;

    public $oum_marker_types_label_default;

    public $oum_searchmarkers_label_default;

    public $oum_searchmarkers_zoom_default;

    public $oum_searchaddress_label_default;

    public $oum_addanother_label_default;

    public $oum_user_notification_label_default;

    public $map_styles = array(
        "Esri.WorldStreetMap"  => "Esri WorldStreetMap",
        "OpenStreetMap.Mapnik" => "OpenStreetMap",
        "OpenStreetMap.DE"     => "OpenStreetMap (Germany)",
        "CartoDB.DarkMatter"   => "CartoDB DarkMatter",
        "CartoDB.Positron"     => "CartoDB Positron",
        "Esri.WorldImagery"    => "Esri WorldImagery",
    );

    public $custom_map_styles = array(
        "Custom1" => "Light with big labels",
        "Custom2" => "Purple Glow with big labels",
        "Custom3" => "Blue with big labels",
    );

    public $commercial_map_styles = array(
        "MapBox.streets"           => "MapBox Streets",
        "MapBox.outdoors"          => "MapBox Outdoors",
        "MapBox.light"             => "MapBox Light",
        "MapBox.dark"              => "MapBox Dark",
        "MapBox.satellite"         => "MapBox Satellite",
        "MapBox.satellite-streets" => "MapBox Satellite Streets",
    );

    public $marker_icons = array(
        "default",
        "custom1",
        "custom2",
        "custom3",
        "custom4"
    );

    public $oum_map_sizes = array(
        "default"   => "Content width",
        "fullwidth" => "Full width",
    );

    public $oum_map_sizes_mobile = array(
        "square"    => "Square",
        "landscape" => "Landscape",
        "portrait"  => "Portrait",
    );

    public $pro_marker_icons = array("user1");

    public $oum_ui_color_default = '#e02aaf';

    public $oum_custom_field_fieldtypes = array(
        "text" => "Text",
    );

    public $pro_oum_custom_field_fieldtypes = array(
        "link"     => "Link [PRO]",
        "email"    => "Email [PRO]",
        "checkbox" => "Checkbox [PRO]",
        "radio"    => "Radio [PRO]",
        "select"   => "Select [PRO]",
        "html"     => "HTML [PRO]",
    );

    public $oum_title_required_default = true;

    public $oum_geosearch_provider = array(
        "osm" => "Open Street Map",
    );

    public $pro_oum_geosearch_provider = array(
        "geoapify" => "Geoapify [PRO]",
        "here"     => "Here [PRO]",
        "mapbox"   => "MapBox [PRO]",
    );

    public $oum_searchbar_types = array(
        "address" => "Search for address (Geosearch)",
        "markers" => "Search for location marker",
    );

    public $oum_regions_layout_styles = array(
        "layout-1" => "Top",
        "layout-2" => "Sidebar",
    );

    public $oum_incompatible_3rd_party_scripts = array(
        "gsap",
        //Bug: Avada scrolltrigger overwrites L
        "mappress-leaflet",
    );

    public function __construct() {
        $this->plugin_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) );
        $this->plugin_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );
        $this->plugin_version = get_file_data( dirname( dirname( dirname( __FILE__ ) ) ) . '/open-user-map.php', array(
            'Version' => 'Version',
        ) )['Version'];
        $this->plugin = plugin_basename( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/open-user-map.php';
        //Default labels
        $this->oum_title_label_default = __( 'Title', 'open-user-map' );
        $this->oum_map_label_default = __( 'Click on the map to set a marker', 'open-user-map' );
        $this->oum_description_label_default = __( 'Description', 'open-user-map' );
        $this->oum_upload_media_label_default = __( 'Upload media', 'open-user-map' );
        $this->oum_address_label_default = __( 'Subtitle', 'open-user-map' );
        $this->oum_marker_types_label_default = __( 'Type', 'open-user-map' );
        $this->oum_searchaddress_label_default = __( 'Search for address', 'open-user-map' );
        $this->oum_searchmarkers_label_default = __( 'Find marker', 'open-user-map' );
        $this->oum_searchmarkers_zoom_default = 8;
        $this->oum_addanother_label_default = __( 'Add another location', 'open-user-map' );
        $this->oum_user_notification_label_default = __( 'Notify me when it is published', 'open-user-map' );
        add_action( 'init', array($this, 'oum_init') );
    }

    public function oum_init() {
        $this->post_status = 'pending';
        if ( !oum_fs()->is_plan_or_trial( 'pro' ) || !oum_fs()->is_premium() ) {
            // Default: Allow Frontend Adding for everyone
            add_action( 'wp_ajax_nopriv_oum_add_location_from_frontend', array($this, 'ajax_add_location_from_frontend') );
            add_action( 'wp_ajax_oum_add_location_from_frontend', array($this, 'ajax_add_location_from_frontend') );
        }
    }

    /**
     * Render all necessary base scripts for the map
     */
    public function include_map_scripts() {
        // Unregister incompatible 3rd party scripts
        $this->remove_incompatible_3rd_party_scripts();
        // enqueue Leaflet css
        wp_enqueue_style(
            'oum_leaflet_css',
            $this->plugin_url . 'src/leaflet/leaflet.css',
            array(),
            $this->plugin_version
        );
        wp_enqueue_style(
            'oum_leaflet_gesture_css',
            $this->plugin_url . 'src/leaflet/leaflet-gesture-handling.min.css',
            array(),
            $this->plugin_version
        );
        wp_enqueue_style(
            'oum_leaflet_markercluster_css',
            $this->plugin_url . 'src/leaflet/leaflet-markercluster.css',
            array(),
            $this->plugin_version
        );
        wp_enqueue_style(
            'oum_leaflet_markercluster_default_css',
            $this->plugin_url . 'src/leaflet/leaflet-markercluster.default.css',
            array(),
            $this->plugin_version
        );
        wp_enqueue_style(
            'oum_leaflet_geosearch_css',
            $this->plugin_url . 'src/leaflet/geosearch.css',
            array(),
            $this->plugin_version
        );
        //https://unpkg.com/leaflet-geosearch@3.9.0/
        wp_enqueue_style(
            'oum_leaflet_fullscreen_css',
            $this->plugin_url . 'src/leaflet/control.fullscreen.css',
            array(),
            $this->plugin_version
        );
        //https://github.com/brunob/leaflet.fullscreen
        wp_enqueue_style(
            'oum_leaflet_locate_css',
            $this->plugin_url . 'src/leaflet/leaflet-locate.min.css',
            array(),
            $this->plugin_version
        );
        //https://github.com/domoritz/leaflet-locatecontrol
        wp_enqueue_style(
            'oum_leaflet_search_css',
            $this->plugin_url . 'src/leaflet/leaflet-search.css',
            array(),
            $this->plugin_version
        );
        //https://github.com/stefanocudini/leaflet-search
        wp_enqueue_style(
            'oum_leaflet_responsivepopup_css',
            $this->plugin_url . 'src/leaflet/leaflet-responsive-popup.css',
            array(),
            $this->plugin_version
        );
        //https://github.com/yafred/leaflet-responsive-popup
        // enqueue Leaflet javascripts
        wp_enqueue_script(
            'oum_leaflet_polyfill_unfetch_js',
            $this->plugin_url . 'src/js/polyfills/unfetch.js',
            array(),
            $this->plugin_version
        );
        wp_enqueue_script(
            'oum_leaflet_js',
            $this->plugin_url . 'src/leaflet/leaflet.js',
            array('oum_leaflet_polyfill_unfetch_js'),
            $this->plugin_version
        );
        wp_enqueue_script(
            'oum_leaflet_providers_js',
            $this->plugin_url . 'src/leaflet/leaflet-providers.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        wp_enqueue_script(
            'oum_leaflet_markercluster_js',
            $this->plugin_url . 'src/leaflet/leaflet-markercluster.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        wp_enqueue_script(
            'oum_leaflet_subgroups_js',
            $this->plugin_url . 'src/leaflet/leaflet.featuregroup.subgroup.js',
            array('oum_leaflet_js', 'oum_leaflet_markercluster_js'),
            $this->plugin_version
        );
        //https://github.com/ghybs/Leaflet.FeatureGroup.SubGroup
        wp_enqueue_script(
            'oum_leaflet_geosearch_js',
            $this->plugin_url . 'src/leaflet/geosearch.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        //https://unpkg.com/leaflet-geosearch@3.9.0/dist/bundle.min.js
        wp_enqueue_script(
            'oum_leaflet_locate_js',
            $this->plugin_url . 'src/leaflet/leaflet-locate.min.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        //https://github.com/domoritz/leaflet-locatecontrol
        wp_enqueue_script(
            'oum_leaflet_fullscreen_js',
            $this->plugin_url . 'src/leaflet/control.fullscreen.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        wp_enqueue_script(
            'oum_leaflet_search_js',
            $this->plugin_url . 'src/leaflet/leaflet-search.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        //https://github.com/stefanocudini/leaflet-search
        wp_enqueue_script(
            'oum_leaflet_gesture_js',
            $this->plugin_url . 'src/leaflet/leaflet-gesture-handling.min.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        wp_enqueue_script(
            'oum_leaflet_responsivepopup_js',
            $this->plugin_url . 'src/leaflet/leaflet-responsive-popup.js',
            array('oum_leaflet_js'),
            $this->plugin_version
        );
        //https://github.com/yafred/leaflet-responsive-popup
    }

    /**
     * Unregister incompatible 3rd party scripts
     */
    public function remove_incompatible_3rd_party_scripts() {
        foreach ( $this->oum_incompatible_3rd_party_scripts as $item ) {
            wp_deregister_script( $item );
        }
    }

    /**
     * Render the map
     */
    public function render_block_map( $block_attributes, $content ) {
        wp_enqueue_style(
            'oum_frontend_css',
            $this->plugin_url . 'assets/frontend.css',
            array(),
            $this->plugin_version
        );
        // load map base scripts
        $this->include_map_scripts();
        wp_enqueue_script(
            'oum_frontend_block_map_js',
            $this->plugin_url . 'src/js/frontend-block-map.js',
            array(
                'oum_leaflet_providers_js',
                'oum_leaflet_markercluster_js',
                'oum_leaflet_subgroups_js',
                'oum_leaflet_geosearch_js',
                'oum_leaflet_locate_js',
                'oum_leaflet_fullscreen_js',
                'oum_leaflet_search_js',
                'oum_leaflet_gesture_js'
            ),
            $this->plugin_version
        );
        // add custom js to frontend-block-map.js
        wp_localize_script( 'oum_frontend_block_map_js', 'custom_js', array(
            'snippet' => get_option( 'oum_custom_js' ),
        ) );
        wp_enqueue_script(
            'oum_frontend_ajax_js',
            $this->plugin_url . 'src/js/frontend-ajax.js',
            array('jquery', 'oum_frontend_block_map_js'),
            $this->plugin_version
        );
        wp_localize_script( 'oum_frontend_ajax_js', 'oum_ajax', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) );
        ob_start();
        require_once "{$this->plugin_path}/templates/block-map.php";
        return ob_get_clean();
    }

    /**
     * Add location from frontend (AJAX)
     */
    public function ajax_add_location_from_frontend() {
        if ( !empty( $_POST['action'] && $_POST['action'] == 'oum_add_location_from_frontend' ) ) {
            // Initialize error handling
            $error = new \WP_Error();
            // Dont save without nonce
            if ( !isset( $_POST['oum_location_nonce'] ) ) {
                die;
            }
            // Dont save if nonce is incorrect
            $nonce = $_POST['oum_location_nonce'];
            if ( !wp_verify_nonce( $nonce, 'oum_location' ) ) {
                die;
            }
            $data['oum_location_title'] = ( isset( $_POST['oum_location_title'] ) && $_POST['oum_location_title'] != '' ? sanitize_text_field( wp_strip_all_tags( $_POST['oum_location_title'] ) ) : time() );
            $data['oum_location_lat'] = sanitize_text_field( wp_strip_all_tags( $_POST['oum_location_lat'] ) );
            $data['oum_location_lng'] = sanitize_text_field( wp_strip_all_tags( $_POST['oum_location_lng'] ) );
            $data['oum_location_address'] = ( isset( $_POST['oum_location_address'] ) ? sanitize_text_field( wp_strip_all_tags( $_POST['oum_location_address'] ) ) : '' );
            $data['oum_location_text'] = ( isset( $_POST['oum_location_text'] ) ? wp_kses_post( $_POST['oum_location_text'] ) : '' );
            $data['oum_location_notification'] = ( isset( $_POST['oum_location_notification'] ) ? $_POST['oum_location_notification'] : '' );
            $data['oum_location_author_name'] = ( isset( $_POST['oum_location_notification'] ) ? sanitize_text_field( wp_strip_all_tags( $_POST['oum_location_author_name'] ) ) : '' );
            $data['oum_location_author_email'] = ( isset( $_POST['oum_location_notification'] ) ? sanitize_email( wp_strip_all_tags( $_POST['oum_location_author_email'] ) ) : '' );
            if ( isset( $_POST['oum_marker_icon'] ) ) {
                $data['oum_marker_icon'] = array();
                foreach ( $_POST['oum_marker_icon'] as $index => $val ) {
                    $data['oum_marker_icon'][$index] = (int) sanitize_text_field( wp_strip_all_tags( $val ) );
                }
            } else {
                $data['oum_marker_icon'] = '';
            }
            if ( isset( $_POST['oum_location_custom_fields'] ) && is_array( $_POST['oum_location_custom_fields'] ) ) {
                $data['oum_location_custom_fields'] = array();
                foreach ( $_POST['oum_location_custom_fields'] as $index => $val ) {
                    if ( is_array( $val ) ) {
                        //multiple values
                        $arr_vals = array();
                        foreach ( $val as $el ) {
                            $arr_vals[] = sanitize_text_field( wp_strip_all_tags( $el ) );
                        }
                        $data['oum_location_custom_fields'][$index] = $arr_vals;
                    } else {
                        //single value
                        $data['oum_location_custom_fields'][$index] = sanitize_text_field( wp_strip_all_tags( $val ) );
                    }
                }
            }
            if ( !$data['oum_location_title'] ) {
                $error->add( '001', 'Missing or incorrect Title value.' );
            }
            if ( !$data['oum_location_lat'] || !$data['oum_location_lng'] ) {
                $error->add( '002', 'Missing or incorrect location. Click on the map to set a marker.' );
            }
            if ( isset( $_FILES['oum_location_image']['name'] ) && $_FILES['oum_location_image']['name'] != '' ) {
                $valid_extensions = array('jpeg', 'jpg', 'png');
                // valid extensions
                $img = sanitize_file_name( $_FILES['oum_location_image']['name'] );
                $tmp = sanitize_text_field( $_FILES['oum_location_image']['tmp_name'] );
                // get uploaded file's extension
                $ext = strtolower( pathinfo( $img, PATHINFO_EXTENSION ) );
                //error_log(print_r($_FILES, true));
                // check internal upload handling
                if ( $tmp == '' ) {
                    $error->add( '003', 'Something went wrong with file upload. Use a valid image file.' );
                }
                // check's valid format
                if ( in_array( $ext, $valid_extensions ) ) {
                    $data['oum_location_image_src'] = $tmp;
                    $data['oum_location_image_ext'] = $ext;
                } else {
                    $error->add( '004', 'Invalid Image file extension. Please use .jpg, .jpeg or .png.' );
                }
                // check maximum filesize
                // default 10MB
                $oum_max_image_filesize = ( get_option( 'oum_max_image_filesize' ) ? get_option( 'oum_max_image_filesize' ) : 10 );
                $max_filesize = (int) $oum_max_image_filesize * 1048576;
                if ( $_FILES['oum_location_image']['size'] > $max_filesize ) {
                    $error->add( '005', 'The image file exceeds maximum size of ' . $oum_max_image_filesize . 'MB.' );
                }
            }
            if ( isset( $_FILES['oum_location_audio']['name'] ) && $_FILES['oum_location_audio']['name'] != '' ) {
                $valid_extensions = array(
                    'mp3',
                    'wav',
                    'mp4',
                    'm4a'
                );
                // valid extensions
                $img = sanitize_file_name( $_FILES['oum_location_audio']['name'] );
                $tmp = sanitize_text_field( $_FILES['oum_location_audio']['tmp_name'] );
                // get uploaded file's extension
                $ext = strtolower( pathinfo( $img, PATHINFO_EXTENSION ) );
                //error_log(print_r($_FILES, true));
                // check internal upload handling
                if ( $tmp == '' ) {
                    $error->add( '003', 'Something went wrong with file upload. Use a valid audio file.' );
                }
                // check valid format
                if ( in_array( $ext, $valid_extensions ) ) {
                    $data['oum_location_audio_src'] = $tmp;
                    $data['oum_location_audio_ext'] = $ext;
                } else {
                    $error->add( '004', 'Invalid audio file extension. Please use .mp3, .wav, .mp4 or .m4a.' );
                }
                // check maximum filesize
                // default 10MB
                $oum_max_audio_filesize = ( get_option( 'oum_max_audio_filesize' ) ? get_option( 'oum_max_audio_filesize' ) : 10 );
                $max_filesize = (int) $oum_max_audio_filesize * 1048576;
                if ( $_FILES['oum_location_audio']['size'] > $max_filesize ) {
                    $error->add( '005', 'The audio file exceeds maximum size of ' . $oum_max_audio_filesize . 'MB.' );
                }
            }
            if ( isset( $data['oum_location_notification'] ) && $data['oum_location_notification'] != '' ) {
                if ( !$data['oum_location_author_name'] ) {
                    $error->add( '006', 'Missing author name.' );
                }
                if ( !$data['oum_location_author_email'] ) {
                    $error->add( '007', 'Missing author email.' );
                }
            }
            if ( $error->has_errors() ) {
                wp_send_json_error( $error );
            } else {
                $new_post = array(
                    'post_title'     => $data['oum_location_title'],
                    'post_type'      => 'oum-location',
                    'post_status'    => $this->post_status,
                    'comment_status' => 'closed',
                );
                $post_id = wp_insert_post( $new_post );
                if ( $post_id ) {
                    // update meta
                    // Validation
                    $lat_validated = floatval( str_replace( ',', '.', $data['oum_location_lat'] ) );
                    if ( !$lat_validated ) {
                        $lat_validated = '';
                    }
                    $lng_validated = floatval( str_replace( ',', '.', $data['oum_location_lng'] ) );
                    if ( !$lng_validated ) {
                        $lng_validated = '';
                    }
                    $data_meta = array(
                        'address' => $data['oum_location_address'],
                        'lat'     => $lat_validated,
                        'lng'     => $lng_validated,
                        'text'    => $data['oum_location_text'],
                    );
                    if ( isset( $data['oum_location_notification'] ) && isset( $data['oum_location_author_name'] ) && isset( $data['oum_location_author_email'] ) ) {
                        $data_meta['notification'] = $data['oum_location_notification'];
                        $data_meta['author_name'] = $data['oum_location_author_name'];
                        $data_meta['author_email'] = $data['oum_location_author_email'];
                    }
                    if ( isset( $data['oum_location_custom_fields'] ) && is_array( $data['oum_location_custom_fields'] ) ) {
                        $data_meta['custom_fields'] = $data['oum_location_custom_fields'];
                    }
                    update_post_meta( $post_id, '_oum_location_key', $data_meta );
                    // update image
                    if ( isset( $data['oum_location_image_src'] ) && isset( $data['oum_location_image_ext'] ) ) {
                        //set uploads dir
                        $uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'oum-useruploads/';
                        wp_mkdir_p( $uploads_dir );
                        $file_name = $post_id . '.' . $data['oum_location_image_ext'];
                        $file_fullpath = $uploads_dir . $file_name;
                        // save file to wp-content/uploads/oum-useruploads/
                        if ( move_uploaded_file( $data['oum_location_image_src'], $file_fullpath ) ) {
                            $oum_location_image_url = wp_upload_dir()['baseurl'] . '/oum-useruploads/' . $file_name;
                            $data_image = esc_url_raw( $oum_location_image_url );
                            update_post_meta( $post_id, '_oum_location_image', $data_image );
                            // create thumbnail
                            $oum_location_image_path = wp_upload_dir()['basedir'] . '/oum-useruploads/' . $file_name;
                            switch ( $data['oum_location_image_ext'] ) {
                                case 'png':
                                    $img_thumb = imagescale( imagecreatefrompng( $oum_location_image_path ), 500 );
                                    $img_thumb = $this->correctImageOrientation( $oum_location_image_path, $img_thumb );
                                    imagepng( $img_thumb, $uploads_dir . $post_id . '_thumb.' . $data['oum_location_image_ext'] );
                                    break;
                                case 'jpg':
                                case 'jpeg':
                                    $img_thumb = imagescale( imagecreatefromjpeg( $oum_location_image_path ), 500 );
                                    $img_thumb = $this->correctImageOrientation( $oum_location_image_path, $img_thumb );
                                    imagejpeg( $img_thumb, $uploads_dir . $post_id . '_thumb.' . $data['oum_location_image_ext'] );
                                    break;
                                case 'gif':
                                    $img_thumb = imagescale( imagecreatefromgif( $oum_location_image_path ), 500 );
                                    $img_thumb = $this->correctImageOrientation( $oum_location_image_path, $img_thumb );
                                    imagegif( $img_thumb, $uploads_dir . $post_id . '_thumb.' . $data['oum_location_image_ext'] );
                                    break;
                                default:
                                    break;
                            }
                            $oum_location_image_thumb_url = wp_upload_dir()['baseurl'] . '/oum-useruploads/' . $post_id . '_thumb.' . $data['oum_location_image_ext'];
                            $data_image_thumb = esc_url_raw( $oum_location_image_thumb_url );
                            update_post_meta( $post_id, '_oum_location_image_thumb', $data_image_thumb );
                        }
                    }
                    // update audio
                    if ( isset( $data['oum_location_audio_src'] ) && isset( $data['oum_location_audio_ext'] ) ) {
                        //set uploads dir
                        $uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'oum-useruploads/';
                        wp_mkdir_p( $uploads_dir );
                        $file_name = $post_id . '.' . $data['oum_location_audio_ext'];
                        $file_fullpath = $uploads_dir . $file_name;
                        // save file to wp-content/uploads/oum-useruploads/
                        if ( move_uploaded_file( $data['oum_location_audio_src'], $file_fullpath ) ) {
                            $oum_location_audio_url = wp_upload_dir()['baseurl'] . '/oum-useruploads/' . $file_name;
                            $data_audio = esc_url_raw( $oum_location_audio_url );
                            update_post_meta( $post_id, '_oum_location_audio', $data_audio );
                        }
                    }
                }
                wp_send_json_success( array(
                    'message' => 'Ok, the location is now pending review.',
                    'post_id' => $post_id,
                ) );
            }
        }
        die;
        //necessary for correct ajax return in WordPress plugins
    }

    public function correctImageOrientation( $filename, $img ) {
        if ( !function_exists( 'exif_read_data' ) ) {
            //exit, if EXIF PHP Library is not available
            return $img;
        }
        $exif = @exif_read_data( $filename );
        if ( $exif && isset( $exif['Orientation'] ) ) {
            $orientation = $exif['Orientation'];
            if ( $orientation != 1 ) {
                $deg = 0;
                switch ( $orientation ) {
                    case 3:
                        $deg = 180;
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ( $deg ) {
                    $img = imagerotate( $img, $deg, 0 );
                }
            }
        }
        return $img;
    }

}
