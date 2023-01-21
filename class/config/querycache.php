<?php
/**
 * Classes to manage config for the cache system of Xaraya
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

namespace Xaraya\Modules\CacheManager\Config;

use xarSecurity;
use xarVar;
use xarSec;
use xarModVars;
use xarMod;
use sys;

sys::import('modules.xarcachemanager.class.config');
use Xaraya\Modules\CacheManager\CacheConfig;

class QueryCache extends CacheConfig
{
    public static function init(array $args = [])
    {
    }

    /**
     * configure query caching (TODO)
     * @return array
     */
    public static function modifyConfig($args)
    {
        extract($args);

        if (!xarSecurity::check('AdminXarCache')) {
            return;
        }

        $data = [];

        xarVar::fetch('submit', 'str', $submit, '');
        if (!empty($submit)) {
            // Confirm authorisation code
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            xarVar::fetch('expire', 'isset', $expire, []);
            foreach ($expire as $module => $querylist) {
                if ($module == 'core') {
                    // define some way to store configuration options for the core
                    foreach ($querylist as $query => $time) {
                    }
                } elseif (xarMod::isAvailable($module)) {
                    // stored in module variables (for now ?)
                    foreach ($querylist as $query => $time) {
                        if (empty($time) || !is_numeric($time)) {
                            xarModVars::set($module, 'cache.'.$query, 0);
                        } else {
                            xarModVars::set($module, 'cache.'.$query, $time);
                        }
                    }
                }
            }
            //xarResponse::Redirect(xarController::URL('xarcachemanager', 'admin', 'queries'));
            //return true;
        }

        // Get some query caching configurations
        $data['queries'] = static::getConfig();

        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }

    /**
     * get configuration of query caching for expensive queries
     *
     * @todo currently unsupported + refers to legacy modules
     * @return array of query caching configurations
     */
    public static function getConfig()
    {
        $queries = [];

        // TODO: add some configuration options for query caching in the core
        $queries['core'] = ['TODO' => 0];

        // TODO: enable $dbconn->LogSQL() and check expensive SQL queries for new candidates

        $candidates = [
            'articles' => ['userapi.getall'], // TODO: round off current pubdate
            'categories' => ['userapi.getcat'],
            'comments' => ['userapi.get_author_count',
                        'userapi.get_multiple', ],
            'dynamicdata' => [], // TODO: make dependent on arguments
            'privileges' => [],
            'roles' => ['userapi.countall',
                        'userapi.getall',
                        'userapi.countallactive',
                        'userapi.getallactive', ],
            'xarbb' => ['userapi.countposts',
                        'userapi.getalltopics', ],
        ];

        foreach ($candidates as $module => $querylist) {
            if (!xarMod::isAvailable($module)) {
                continue;
            }
            $queries[$module] = [];
            foreach ($querylist as $query) {
                // stored in module variables (for now ?)
                $queries[$module][$query] = xarModVars::get($module, 'cache.'.$query);
            }
        }

        return $queries;
    }
}
