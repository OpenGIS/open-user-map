<?php
/**
 * @package OpenUserMapPlugin
 */

namespace OpenUserMapPlugin\Base;

use OpenUserMapPlugin\Base\BaseController;

class SettingsLinks extends BaseController
{

    public function register()
    {
        add_filter('plugin_action_links_' . $this->plugin, array($this, 'settings_link'));
    }

    public function settings_link($links)
    {
        $settings_link = '<a href="edit.php?post_type=oum-location&page=open-user-map-settings">Settings</a>';
        array_push($links, $settings_link);

        return $links;
    }
}
