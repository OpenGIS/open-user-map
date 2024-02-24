<?php

/**
 * @package OpenUserMapPlugin
 */
namespace OpenUserMapPlugin\Pages;

use  OpenUserMapPlugin\Base\BaseController ;
class Frontend extends BaseController
{
    public function register()
    {
        // Shortcodes
        add_action( 'init', array( $this, 'set_shortcodes' ) );
    }
    
    /**
     * Setup Shortcodes
     */
    public function set_shortcodes()
    {
        // EXIT if inside Elementor Backend
        // Check if Elementor installed and activated
        if ( did_action( 'elementor/loaded' ) ) {
            
            if ( \Elementor_OUM_Addon\Plugin::is_elementor_backend() ) {
                error_log( 'OUM: prevented shortcode rendering inside Elementor' );
                return;
            }
        
        }
        // Render Map
        add_shortcode( 'open-user-map', array( $this, 'render_block_map' ) );
        // Whitelisting OUM scripts for Complianz plugin
        add_filter(
            'script_loader_tag',
            function ( $tag, $handle, $source ) {
            if ( stristr( $handle, 'oum' ) ) {
                $tag = '<script src="' . $source . '" data-category="functional" class="cmplz-native" id="' . $handle . '-js"></script>';
            }
            return $tag;
        },
            10,
            3
        );
        // Prevent shortcode parsing by All In One SEO plugin
        add_filter( 'aioseo_disable_shortcode_parsing', '__return_true' );
    }

}