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

    /**
     * @uses xarcachemanager_admin_main()
     */
    public static function main()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'main');
    }

    /**
     * @uses xarcachemanager_admin_overview()
     */
    public static function overview()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'overview');
    }

    /**
     * @uses xarcachemanager_admin_modifyconfig()
     */
    public static function modifyconfig()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'modifyconfig');
    }

    /**
     * @uses xarcachemanager_admin_updateconfig()
     */
    public static function updateconfig()
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'updateconfig');
    }

    /**
     * @uses xarcachemanager_admin_stats()
     */
    public static function stats(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'stats', $args);
    }

    /**
     * @uses xarcachemanager_admin_view()
     */
    public static function view(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'view', $args);
    }

    /**
     * @uses xarcachemanager_admin_pages()
     */
    public static function pages(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'pages', $args);
    }

    /**
     * @uses xarcachemanager_admin_blocks()
     */
    public static function blocks(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'blocks', $args);
    }

    /**
     * @uses xarcachemanager_admin_modules()
     */
    public static function modules(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'modules', $args);
    }

    /**
     * @uses xarcachemanager_admin_objects()
     */
    public static function objects(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'objects', $args);
    }

    /**
     * @uses xarcachemanager_admin_variables()
     */
    public static function variables(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'variables', $args);
    }

    /**
     * @uses xarcachemanager_admin_queries()
     */
    public static function queries(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'queries', $args);
    }

    /**
     * @uses xarcachemanager_admin_modifyhook()
     */
    public static function modifyhook(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'modifyhook', $args);
    }

    /**
     * @uses xarcachemanager_admin_flushcache()
     */
    public static function flushcache(array $args = array())
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'flushcache', $args);
    }
}
