<?php
/**
 * @package OpenUserMapPlugin
 */

namespace OpenUserMapPlugin\Base;

class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
