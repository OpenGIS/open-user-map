<?php
/**
 * @package OpenUserMapPlugin
 */

namespace OpenUserMapPlugin\Base;

use OpenUserMapPlugin\Base\BaseController;

class BlockController extends BaseController
{
    public function register() {
        // Gutenberg Blocks
        add_action('init', array($this, 'set_gutenberg_blocks'));

        // Elementor Widgets
        add_action('plugins_loaded', array($this, 'set_elementor_widgets'));
    }

    /**
     * Setup Gutenberg Blocks
     */
    public function set_gutenberg_blocks()
    {   
        // Register Block
        register_block_type( 
            $this->plugin_path . 'blocks',
            array(
                'render_callback' => is_admin() ? null : array($this, 'render_block_map')
            )
        );

        // add JS translation for Gutenberg Blocks script
        /*
         Pay Attention: 
         - currently doesnt work with wordpress.org translation --> use local translation file 
         - Translation file needs to be called "open-user-map-de_DE-oum_blocks_script.json"
         - Howto: https://developer.wordpress.org/block-editor/how-to-guides/internationalization/
         */
        // wp_set_script_translations( 
        //     'oum_blocks_script', 
        //     'open-user-map', 
        //     $this->plugin_path . 'languages' 
        // );
    }

    /**
     * Setup Elementor Widgets
     */
    public function set_elementor_widgets($widgets_manager)
    {
        //require_once "$this->plugin_path/elementor/elementor-oum-addon.php";

        require_once "$this->plugin_path/elementor/includes/plugin.php";

        // Run the plugin
        \Elementor_OUM_Addon\Plugin::instance();
    }
}
