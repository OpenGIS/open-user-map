<?php
/**
 * @package OpenUserMapPlugin
 */

namespace OpenUserMapPlugin;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        $classes = array(
            Pages\Settings::class,
            Base\Enqueue::class,
            Base\SettingsLinks::class,
            Base\LocationController::class,
            Base\BlockController::class,
            Base\TaxController::class,
        );

        // only add Frontend class when not in backend
        if(!is_admin()) {
            array_unshift($classes, Pages\Frontend::class);
        }

        return $classes; 
    }

    /**
     * Loop through the classes, initialize them and call method register() if it exists
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);

            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param $class class      class from the services array
     * @return class instance   new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
