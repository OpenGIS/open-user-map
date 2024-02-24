<?php

/**
 * @package OpenUserMapPlugin
 */
namespace OpenUserMapPlugin\Base;

use  OpenUserMapPlugin\Base\BaseController ;
class TaxController extends BaseController
{
    public  $settings ;
    public function register()
    {
        
        if ( get_option( 'oum_enable_regions' ) ) {
            // Taxonomy: region
            add_action( 'init', array( $this, 'region_tax' ) );
            add_action( 'oum-region_add_form_fields', array( $this, 'region_tax_add_custom_fields' ) );
            add_action(
                'oum-region_edit_form_fields',
                array( $this, 'region_tax_edit_custom_fields' ),
                10,
                2
            );
            add_action( 'edited_oum-region', array( $this, 'region_tax_save' ) );
            add_action( 'create_oum-region', array( $this, 'region_tax_save' ) );
            add_action( 'manage_edit-oum-region_columns', array( $this, 'set_custom_region_columns' ) );
            add_action(
                'manage_oum-region_custom_column',
                array( $this, 'set_custom_region_columns_data' ),
                10,
                3
            );
            // this method has 3 attributes
        }
    
    }
    
    /**
     * Taxonomy: oum-type
     */
    public static function type_tax()
    {
        $labels = array(
            'name'                       => __( 'Marker Categories', 'open-user-map' ),
            'singular_name'              => __( 'Marker Category', 'open-user-map' ),
            'menu_name'                  => __( 'Marker Categories', 'open-user-map' ),
            'all_items'                  => __( 'All Marker Categories', 'open-user-map' ),
            'edit_item'                  => __( 'Edit Marker Category', 'open-user-map' ),
            'view_item'                  => __( 'Show Marker Category', 'open-user-map' ),
            'update_item'                => __( 'Update Marker Category', 'open-user-map' ),
            'add_new_item'               => __( 'Add new Marker Category', 'open-user-map' ),
            'new_item_name'              => __( 'New Type name', 'open-user-map' ),
            'search_items'               => __( 'Search Marker Categories', 'open-user-map' ),
            'choose_from_most_used'      => __( 'Choose from the most used Marker Categories', 'open-user-map' ),
            'popular_items'              => __( 'Popular Marker Categories', 'open-user-map' ),
            'add_or_remove_items'        => __( 'Add or remove Marker Categories', 'open-user-map' ),
            'separate_items_with_commas' => __( 'Separate Marker Categories with commas', 'open-user-map' ),
            'back_to_items'              => __( 'Back to Marker Categories', 'open-user-map' ),
        );
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'exclude_from_search' => true,
            'show_in_nav_menus'   => false,
            'show_admin_column'   => true,
            'show_in_quick_edit'  => false,
            'meta_box_cb'         => false,
            'hierarchical'        => false,
            'show_in_rest'        => true,
        );
        register_taxonomy( 'oum-type', 'oum-location', $args );
    }
    
    public function type_tax_add_custom_fields( $term )
    {
        wp_nonce_field( 'oum_location', 'oum_location_nonce' );
        // render view
        require_once "{$this->plugin_path}/templates/page-backend-add-type.php";
        wp_enqueue_script(
            'oum_backend_type_js',
            $this->plugin_url . 'src/js/backend-type.js',
            array( 'wp-polyfill' ),
            $this->plugin_version
        );
    }
    
    public function type_tax_edit_custom_fields( $tag, $taxonomy )
    {
        wp_nonce_field( 'oum_location', 'oum_location_nonce' );
        // render view
        require_once "{$this->plugin_path}/templates/page-backend-edit-type.php";
        wp_enqueue_script(
            'oum_backend_type_js',
            $this->plugin_url . 'src/js/backend-type.js',
            array( 'wp-polyfill' ),
            $this->plugin_version
        );
    }
    
    public function type_tax_save( $term_id )
    {
        // Dont save without nonce
        if ( !isset( $_POST['oum_location_nonce'] ) ) {
            return $term_id;
        }
        // Dont save if nonce is incorrect
        $nonce = $_POST['oum_location_nonce'];
        if ( !wp_verify_nonce( $nonce, 'oum_location' ) ) {
            return $term_id;
        }
        // Dont save if wordpress just auto-saves
        if ( defined( 'DOING AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $term_id;
        }
        
        if ( isset( $_POST['oum_marker_icon'] ) ) {
            // Validation
            $oum_marker_icon_validated = sanitize_text_field( $_POST['oum_marker_icon'] );
            if ( !$oum_marker_icon_validated ) {
                $oum_marker_icon_validated = '';
            }
            if ( $oum_marker_icon_validated ) {
                update_term_meta( $term_id, 'oum_marker_icon', $oum_marker_icon_validated );
            }
        }
        
        
        if ( isset( $_POST['oum_marker_user_icon'] ) ) {
            // Validation
            $oum_marker_user_icon_validated = sanitize_text_field( $_POST['oum_marker_user_icon'] );
            if ( !$oum_marker_user_icon_validated ) {
                $oum_marker_user_icon_validated = '';
            }
            if ( $oum_marker_user_icon_validated ) {
                update_term_meta( $term_id, 'oum_marker_user_icon', $oum_marker_user_icon_validated );
            }
        }
    
    }
    
    public function add_type_tax_meta_box_to_location()
    {
        add_meta_box(
            'taxonomy_box',
            __( 'Marker Category', 'open-user-map' ),
            array( $this, 'render_type_tax_meta_box' ),
            'oum-location',
            'side'
        );
    }
    
    public function render_type_tax_meta_box( $post )
    {
        $terms = get_terms( array(
            'taxonomy'   => 'oum-type',
            'hide_empty' => false,
        ) );
        $currentType = ( get_the_terms( $post->ID, 'oum-type' ) ? get_the_terms( $post->ID, 'oum-type' )[0] : false );
        // render view
        require_once "{$this->plugin_path}/templates/page-backend-select-type.php";
        wp_enqueue_script(
            'oum_backend_type_js',
            $this->plugin_url . 'src/js/backend-type.js',
            array( 'wp-polyfill' ),
            $this->plugin_version
        );
    }
    
    public function save_type_tax_with_location( $post_id )
    {
        if ( isset( $_POST['oum_marker_icon'] ) ) {
            wp_set_object_terms( $post_id, (int) sanitize_text_field( $_POST['oum_marker_icon'] ), 'oum-type' );
        }
    }
    
    /**
     * Taxonomy: oum-region
     */
    public static function region_tax()
    {
        $labels = array(
            'name'                       => __( 'Regions', 'open-user-map' ),
            'singular_name'              => __( 'Region', 'open-user-map' ),
            'menu_name'                  => __( 'Regions', 'open-user-map' ),
            'all_items'                  => __( 'All Regions', 'open-user-map' ),
            'edit_item'                  => __( 'Edit Region', 'open-user-map' ),
            'view_item'                  => __( 'Show Region', 'open-user-map' ),
            'update_item'                => __( 'Update Region', 'open-user-map' ),
            'add_new_item'               => __( 'Add new Region', 'open-user-map' ),
            'new_item_name'              => __( 'New Type name', 'open-user-map' ),
            'search_items'               => __( 'Search Regions', 'open-user-map' ),
            'choose_from_most_used'      => __( 'Choose from the most used Regions', 'open-user-map' ),
            'popular_items'              => __( 'Popular Regions', 'open-user-map' ),
            'add_or_remove_items'        => __( 'Add or remove Regions', 'open-user-map' ),
            'separate_items_with_commas' => __( 'Separate Regions with commas', 'open-user-map' ),
            'back_to_items'              => __( 'Back to Regions', 'open-user-map' ),
        );
        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'exclude_from_search' => true,
            'show_in_nav_menus'   => false,
            'show_admin_column'   => false,
            'show_in_quick_edit'  => false,
            'meta_box_cb'         => false,
            'hierarchical'        => false,
            'show_in_rest'        => false,
        );
        register_taxonomy( 'oum-region', 'oum-location', $args );
    }
    
    public function region_tax_add_custom_fields( $term )
    {
        wp_nonce_field( 'oum_location', 'oum_location_nonce' );
        // render view
        require_once "{$this->plugin_path}/templates/page-backend-add-region.php";
    }
    
    public function region_tax_edit_custom_fields( $tag, $taxonomy )
    {
        wp_nonce_field( 'oum_location', 'oum_location_nonce' );
        // render view
        require_once "{$this->plugin_path}/templates/page-backend-edit-region.php";
    }
    
    public function region_tax_save( $term_id )
    {
        // Dont save without nonce
        if ( !isset( $_POST['oum_location_nonce'] ) ) {
            return $term_id;
        }
        // Dont save if nonce is incorrect
        $nonce = $_POST['oum_location_nonce'];
        if ( !wp_verify_nonce( $nonce, 'oum_location' ) ) {
            return $term_id;
        }
        // Dont save if wordpress just auto-saves
        if ( defined( 'DOING AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $term_id;
        }
        
        if ( isset( $_POST['oum_lat'] ) ) {
            // Validation
            $oum_lat_validated = floatval( str_replace( ',', '.', sanitize_text_field( $_POST['oum_lat'] ) ) );
            if ( !$oum_lat_validated ) {
                $oum_lat_validated = '';
            }
            if ( $oum_lat_validated ) {
                update_term_meta( $term_id, 'oum_lat', $oum_lat_validated );
            }
        }
        
        
        if ( isset( $_POST['oum_lng'] ) ) {
            // Validation
            $oum_lng_validated = floatval( str_replace( ',', '.', sanitize_text_field( $_POST['oum_lng'] ) ) );
            if ( !$oum_lng_validated ) {
                $oum_lng_validated = '';
            }
            if ( $oum_lng_validated ) {
                update_term_meta( $term_id, 'oum_lng', $oum_lng_validated );
            }
        }
        
        
        if ( isset( $_POST['oum_zoom'] ) ) {
            // Validation
            $oum_zoom_validated = floatval( str_replace( ',', '.', sanitize_text_field( $_POST['oum_zoom'] ) ) );
            if ( !$oum_zoom_validated ) {
                $oum_zoom_validated = '';
            }
            if ( $oum_zoom_validated ) {
                update_term_meta( $term_id, 'oum_zoom', $oum_zoom_validated );
            }
        }
    
    }
    
    public static function set_custom_region_columns( $columns )
    {
        // preserve default columns
        $name = $columns['name'];
        unset( $columns['description'], $columns['slug'], $columns['posts'] );
        $columns['name'] = $name;
        $columns['geocoordinates'] = __( 'Coordinates', 'open-user-map' );
        $columns['zoom'] = __( 'Zoom', 'open-user-map' );
        return $columns;
    }
    
    public static function set_custom_region_columns_data( $content, $column, $term_id )
    {
        $data = get_term_meta( $term_id );
        $lat = ( isset( $data['oum_lat'][0] ) ? $data['oum_lat'][0] : '' );
        $lng = ( isset( $data['oum_lng'][0] ) ? $data['oum_lng'][0] : '' );
        $zoom = ( isset( $data['oum_zoom'][0] ) ? $data['oum_zoom'][0] : '' );
        switch ( $column ) {
            case 'geocoordinates':
                echo  esc_attr( $lat ) . ', ' . esc_attr( $lng ) ;
                break;
            case 'zoom':
                echo  esc_attr( $zoom ) ;
                break;
            default:
                break;
        }
    }

}