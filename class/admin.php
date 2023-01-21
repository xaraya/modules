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

namespace Xaraya\Modules\CacheManager;

use xarObject;
use xarMod;
use xarTpl;
use sys;

class CacheAdmin extends xarObject
{
    public static function init(array $args = [])
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
    public static function stats(array $args = [])
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'stats', $args);
    }

    /**
     * @uses xarcachemanager_admin_view()
     */
    public static function view(array $args = [])
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'view', $args);
    }

    /**
     * @uses Config\PageCache::modifyConfig()
     */
    public static function pages(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.pagecache');
        $tplData = Config\PageCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'pages', $tplData);
    }

    /**
     * @uses Config\BlockCache::modifyConfig()
     */
    public static function blocks(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.blockcache');
        $tplData = Config\BlockCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'blocks', $tplData);
    }

    /**
     * @uses Config\ModuleCache::modifyConfig()
     */
    public static function modules(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.modulecache');
        $tplData = Config\ModuleCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'modules', $tplData);
    }

    /**
     * @uses Config\ObjectCache::modifyConfig()
     */
    public static function objects(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.objectcache');
        $tplData = Config\ObjectCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'objects', $tplData);
    }

    /**
     * @uses Config\VariableCache::modifyConfig()
     */
    public static function variables(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.variablecache');
        $tplData = Config\VariableCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'variables', $tplData);
    }

    /**
     * @uses Config\QueryCache::modifyConfig()
     */
    public static function queries(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.querycache');
        $tplData = Config\QueryCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'queries', $tplData);
    }

    /**
     * @uses Config\TemplateCache::modifyConfig()
     */
    public static function templates(array $args = [])
    {
        sys::import('modules.xarcachemanager.class.config.templatecache');
        $tplData = Config\TemplateCache::modifyConfig($args);
        if (!is_array($tplData)) {
            return $tplData;
        }
        return xarTpl::module('xarcachemanager', 'admin', 'templates', $tplData);
    }

    /**
     * @uses xarcachemanager_admin_flushcache()
     */
    public static function flushcache(array $args = [])
    {
        return xarMod::guiFunc('xarcachemanager', 'admin', 'flushcache', $args);
    }
}
