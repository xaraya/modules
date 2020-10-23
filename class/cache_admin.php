<?php
/**
 * Classes to run admin gui functions
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

class xarCache_Admin extends xarObject
{
    public static function init(array $args = array())
    {
    }

    public static function main()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'main');
    }

    public static function overview()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'overview');
    }

    public static function modifyconfig()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'modifyconfig');
    }

    public static function updateconfig()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'updateconfig');
    }

    public static function stats(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'stats', $args);
    }

    public static function view(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'view', $args);
    }

    public static function pages(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'pages', $args);
    }

    public static function blocks(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'blocks', $args);
    }

    public static function modules(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'modules', $args);
    }

    public static function objects(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'objects', $args);
    }

    public static function variables(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'variables', $args);
    }

    public static function queries(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'queries', $args);
    }

    public static function modifyhook(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'modifyhook', $args);
    }

    public static function flushcache(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'flushcache', $args);
    }
}
