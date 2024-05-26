<?php

/**
 * @package OpenUserMapPlugin
 */
/*
Plugin Name: Open User Map
Plugin URI: https://wordpress.org/plugins/open-user-map/
Description: Create a customizable, simple or interactive map. Anyone can add new markers without registering — perfect for collaborative and community projects.
Author: 100plugins
Version: 1.3.42
Author URI: https://www.open-user-map.com/
License: GPLv3 or later
Text Domain: open-user-map
*/
/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

Copyright 2023 100plugins
*/
defined( 'ABSPATH' ) or die( 'Direct access is not allowed.' );
if ( function_exists( 'oum_fs' ) ) {
    oum_fs()->set_basename( false, __FILE__ );
} else {
    // FREEMIUS INTEGRATION CODE
    if ( !function_exists( 'oum_fs' ) ) {
        // Create a helper function for easy SDK access.
        function oum_fs() {
            global $oum_fs;
            if ( !isset( $oum_fs ) ) {
                // Enable the new Freemius Garbage Collector (Beta)
                // if ( ! defined( 'WP_FS__ENABLE_GARBAGE_COLLECTOR' ) ) {
                //     define( 'WP_FS__ENABLE_GARBAGE_COLLECTOR', true );
                // }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $oum_fs = fs_dynamic_init( array(
                    'id'             => '9083',
                    'slug'           => 'open-user-map',
                    'premium_slug'   => 'open-user-map-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_e4bbeb52c0d44fa562ba49d2c632d',
                    'is_premium'     => false,
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 7,
                        'is_require_payment' => false,
                    ),
                    'menu'           => array(
                        'slug'       => 'edit.php?post_type=oum-location',
                        'first-path' => 'edit.php?post_type=oum-location&page=open-user-map-settings',
                        'contact'    => false,
                        'support'    => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $oum_fs;
        }

        // Init Freemius.
        oum_fs();
        // Signal that SDK was initiated.
        do_action( 'oum_fs_loaded' );
    }
    // Special uninstall routine with Freemius
    function oum_fs_uninstall_cleanup() {
        global $wpdb;
        //delete posts
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "posts WHERE post_type='oum-location'" );
        //delete postmeta
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "postmeta WHERE meta_key LIKE '%oum_%'" );
        //delete options
        $wpdb->query( "DELETE FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'oum_%'" );
    }

    oum_fs()->add_action( 'after_uninstall', 'oum_fs_uninstall_cleanup' );
    // Better Opt-In Screen
    oum_fs()->add_action( 'connect/before', function () {
        echo '<div class="oum-wizard">
            <div class="hero">
                <div class="logo">Open User Map</div>
                <div class="overline">' . __( 'Quick Setup (1/3)', 'open-user-map' ) . '</div>
                <h1>' . __( 'Hi! Thanks for using Open User Map', 'open-user-map' ) . '</h1>
                <ul class="steps">
                    <li class="done"></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
            <div class="step-content">';
    } );
    oum_fs()->add_action( 'connect/after', function () {
        echo '</div></div>';
    } );
    // ... Your plugin's main file logic ...
    // Require once the composer autoload
    if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
        require_once dirname( __FILE__ ) . '/vendor/autoload.php';
    }
    /**
     * The code that runs during plugin activation
     */
    function oum_activate_plugin() {
        OpenUserMapPlugin\Base\Activate::activate();
    }

    register_activation_hook( __FILE__, 'oum_activate_plugin' );
    /**
     * The code that runs during plugin deactivation
     */
    function oum_deactivate_plugin() {
        OpenUserMapPlugin\Base\Deactivate::deactivate();
    }

    register_deactivation_hook( __FILE__, 'oum_deactivate_plugin' );
    /**
     * Initialize all the core classes of the plugin
     */
    if ( class_exists( 'OpenUserMapPlugin\\Init' ) ) {
        // OpenUserMapPlugin\Init::register_services();
        try {
            OpenUserMapPlugin\Init::register_services();
        } catch ( \Error $e ) {
            return 'An error has occurred. Please look in the settings under Open User Map > Help > Debug Info.';
            error_log( $e->getMessage() . '(' . $e->getFile() . ' Line: ' . $e->getLine() . ')' );
        }
    }
    /**
     * Get a value from a location (public function)
     * 
     * possible attributes: 
     * - title
     * - image
     * - audio
     * - type
     * - map
     * - address
     * - lat
     * - lng
     * - route
     * - text
     * - notification
     * - author_name
     * - author_email
     * - user_id
     * - CUSTOM FIELD LABEL
     */
    function oum_get_location_value(  $attr, $post_id, $raw = false  ) {
        return OpenUserMapPlugin\Base\LocationController::get_location_value( $attr, $post_id, $raw );
    }

}