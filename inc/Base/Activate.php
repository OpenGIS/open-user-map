<?php
/**
 * @package OpenUserMapPlugin
 */

namespace OpenUserMapPlugin\Base;

class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
