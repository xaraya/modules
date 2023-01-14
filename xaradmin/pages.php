<?php
/**
 * Configure page caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.manager');
use Xaraya\Modules\CacheManager\CacheManager;

/**
 * configure page caching (TODO)
 * @return array
 */
function xarcachemanager_admin_pages($args)
{
    extract($args);

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = [];
    if (!xarCache::$outputCacheIsEnabled || !xarOutputCache::$pageCacheIsEnabled) {
        $data['pages'] = [];
        return $data;
    }

    // Get the output cache directory
    $outputCacheDir = xarCache::getOutputCacheDir();

    $cachingConfiguration = CacheManager::get_config(
        ['from' => 'file']
    );

    $data['settings'] = xarMod::apiFunc(
        'xarcachemanager',
        'admin',
        'config_tpl_prep',
        $cachingConfiguration
    );

    $filter = ['Class' => 2];
    $data['themes'] = xarMod::apiFunc(
        'themes',
        'admin',
        'getlist',
        $filter
    );

    $data['groups'] = xarMod::apiFunc('roles', 'user', 'getallgroups');

    xarVar::fetch('submit', 'str', $submit, '');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        xarVar::fetch('groups', 'isset', $groups, [], xarVar::NOT_REQUIRED);
        $grouplist = [];
        foreach ($data['groups'] as $idx => $group) {
            if (!empty($groups[$group['id']])) {
                $data['groups'][$idx]['checked'] = 1;
                $grouplist[] = $group['id'];
            }
        }
        $cachegroups = join(';', $grouplist);

        xarVar::fetch('sessionless', 'isset', $sessionless, '', xarVar::NOT_REQUIRED);
        $sessionlesslist = [];
        if (!empty($sessionless)) {
            $urls = preg_split('/\s+/', $sessionless, -1, PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServer::getBaseURL();
            foreach ($urls as $url) {
                // jsb: hmmm, do we really want to limit the seesionless url list
                // to those that are under the current baseurl?  I run my sites with
                // one base url, but many people use alternates.
                if (empty($url) || !strstr($url, $baseurl)) {
                    continue;
                }
                $sessionlesslist[] = $url;
            }
        }

        // set option for auto regeneration of session-less url list cache on event invalidation
        xarVar::fetch('autoregenerate', 'isset', $autoregenerate, '', xarVar::NOT_REQUIRED);
        if ($autoregenerate) {
            xarModVars::set('xarcachemanager', 'AutoRegenSessionless', 1);
        } else {
            xarModVars::set('xarcachemanager', 'AutoRegenSessionless', 0);
        }

        xarVar::fetch('autocache', 'isset', $autocache, '', xarVar::NOT_REQUIRED);
        if (empty($autocache['period'])) {
            $autocache['period'] = 0;
        }
        $autocache['period'] = CacheManager::convertseconds(
            ['starttime' => $autocache['period'],
                                                   'direction' => 'to', ]
        );
        if (empty($autocache['threshold'])) {
            $autocache['threshold'] = 0;
        }
        if (empty($autocache['maxpages'])) {
            $autocache['maxpages'] = 0;
        }
        $includelist = [];
        if (!empty($autocache['include'])) {
            $urls = preg_split('/\s+/', $autocache['include'], -1, PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServer::getBaseURL();
            foreach ($urls as $url) {
                // jsb: cfr. above note on sessionless url check
                if (empty($url) || !strstr($url, $baseurl)) {
                    continue;
                }
                $includelist[] = $url;
            }
        }
        $excludelist = [];
        if (!empty($autocache['exclude'])) {
            $urls = preg_split('/\s+/', $autocache['exclude'], -1, PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServer::getBaseURL();
            foreach ($urls as $url) {
                // jsb: cfr. above note on sessionless url check
                if (empty($url) || !strstr($url, $baseurl)) {
                    continue;
                }
                $excludelist[] = $url;
            }
        }
        if (empty($autocache['keepstats'])) {
            $autocache['keepstats'] = 0;
        } else {
            $autocache['keepstats'] = 1;
        }

        // save the new config settings
        $configSettings = [];
        $configSettings['Page.CacheGroups']    = $cachegroups;
        $configSettings['Page.SessionLess']    = $sessionlesslist;
        $configSettings['AutoCache.Period']    = $autocache['period'];
        $configSettings['AutoCache.Threshold'] = $autocache['threshold'];
        $configSettings['AutoCache.MaxPages']  = $autocache['maxpages'];
        $configSettings['AutoCache.Include']   = $includelist;
        $configSettings['AutoCache.Exclude']   = $excludelist;
        $configSettings['AutoCache.KeepStats'] = $autocache['keepstats'];

        CacheManager::save_config(
            ['configSettings' => $configSettings]
        );

        if (empty($autocache['period'])) {
            // remove autocache.start and autocache.log files
            if (file_exists($outputCacheDir . '/autocache.start')) {
                unlink($outputCacheDir . '/autocache.start');
            }
            if (file_exists($outputCacheDir . '/autocache.log')) {
                unlink($outputCacheDir . '/autocache.log');
            }
        } elseif (!file_exists($outputCacheDir . '/autocache.start') ||
                  !isset($data['settings']['AutoCachePeriod']) ||
                  // only re-initialise if the period changes
                  $data['settings']['AutoCachePeriod'] != $autocache['period']) {
            // initialise autocache.start and autocache.log files
            touch($outputCacheDir . '/autocache.start');
            $fp = fopen($outputCacheDir . '/autocache.log', 'w');
            fclose($fp);

            // make sure the xarcachemanager event handler is known to the event system
            if (!xarMod::apiFunc('modules', 'admin', 'geteventhandlers')) {
                return;
            }
        }

        if (empty($autocache['keepstats'])) {
            // remove autocache.stats file
            if (file_exists($outputCacheDir . '/autocache.stats')) {
                unlink($outputCacheDir . '/autocache.stats');
            }
        }

        xarResponse::Redirect(xarController::URL('xarcachemanager', 'admin', 'pages'));
        return true;
    } elseif (!empty($data['settings']['PageCacheGroups'])) {
        $grouplist = explode(';', $data['settings']['PageCacheGroups']);
        foreach ($data['groups'] as $idx => $group) {
            if (in_array($group['id'], $grouplist)) {
                $data['groups'][$idx]['checked'] = 1;
            }
        }
    }

    if (!isset($data['settings']['PageSessionLess'])) {
        $data['sessionless'] = xarMLS::translate(
            "Please add the following line to your config.caching.php file :\n#(1)",
            '$cachingConfiguration[\'Page.SessionLess\'] = array();'
        );
    } elseif (!empty($data['settings']['PageSessionLess']) && count($data['settings']['PageSessionLess']) > 0) {
        $data['sessionless'] = join("\n", $data['settings']['PageSessionLess']);
    } else {
        $data['sessionless'] = '';
    }

    if (!isset($data['settings']['AutoCachePeriod'])) {
        $data['settings']['AutoCachePeriod'] = 0;
    }
    $data['settings']['AutoCachePeriod'] = CacheManager::convertseconds(
        ['starttime' => $data['settings']['AutoCachePeriod'],
                                                     'direction' => 'from', ]
    );

    if (!isset($data['settings']['AutoCacheThreshold'])) {
        $data['settings']['AutoCacheThreshold'] = 10;
    }
    if (!isset($data['settings']['AutoCacheMaxPages'])) {
        $data['settings']['AutoCacheMaxPages'] = 25;
    }
    if (!isset($data['settings']['AutoCacheInclude'])) {
        $data['settings']['AutoCacheInclude'] = xarServer::getBaseURL() . "\n" . xarServer::getBaseURL() . 'index.php';
    } elseif (is_array($data['settings']['AutoCacheInclude'])) {
        $data['settings']['AutoCacheInclude'] = join("\n", $data['settings']['AutoCacheInclude']);
    }
    if (!isset($data['settings']['AutoCacheExclude'])) {
        $data['settings']['AutoCacheExclude'] = '';
    } elseif (is_array($data['settings']['AutoCacheExclude'])) {
        $data['settings']['AutoCacheExclude'] = join("\n", $data['settings']['AutoCacheExclude']);
    }
    if (!isset($data['settings']['AutoCacheKeepStats'])) {
        $data['settings']['AutoCacheKeepStats'] = 0;
    }

    // Get some current information from the auto-cache log
    $data['autocachepages'] = [];
    if (file_exists($outputCacheDir . '/autocache.log') &&
        filesize($outputCacheDir . '/autocache.log') > 0) {
        $logs = file($outputCacheDir . '/autocache.log');
        $data['autocachehits'] = ['HIT' => 0,
                                       'MISS' => 0, ];
        $autocacheproposed = [];
        foreach ($logs as $entry) {
            if (empty($entry)) {
                continue;
            }
            [$time, $status, $addr, $url] = explode(' ', $entry);
            $url = trim($url);
            if (!isset($start)) {
                $start = $time;
            }
            $end = $time;
            if (!isset($data['autocachepages'][$url])) {
                $data['autocachepages'][$url] = [];
            }
            if (!isset($data['autocachepages'][$url][$status])) {
                $data['autocachepages'][$url][$status] = 0;
            }
            if (!isset($autocacheproposed[$url])) {
                $autocacheproposed[$url] = 0;
            }
            $data['autocachepages'][$url][$status]++;
            $data['autocachehits'][$status]++;
            $autocacheproposed[$url]++;
        }
        unset($logs);
        ksort($data['autocachepages']);
        $data['autocachestart'] = $start;
        $data['autocacheend'] = $end;
        // check that all required URLs are included
        if (!empty($cachingConfiguration['AutoCache.Include'])) {
            foreach ($cachingConfiguration['AutoCache.Include'] as $url) {
                if (!isset($autocacheproposed[$url]) ||
                    $autocacheproposed[$url] < $cachingConfiguration['AutoCache.Threshold']) {
                    $autocacheproposed[$url] = 99999999;
                }
            }
        }
        // check that all forbidden URLs are excluded
        if (!empty($cachingConfiguration['AutoCache.Exclude'])) {
            foreach ($cachingConfiguration['AutoCache.Exclude'] as $url) {
                if (isset($autocacheproposed[$url])) {
                    unset($autocacheproposed[$url]);
                }
            }
        }
        // sort descending by count
        arsort($autocacheproposed, SORT_NUMERIC);
        $data['autocacheproposed'] = [];
        // build the list of URLs proposed for session-less caching
        foreach ($autocacheproposed as $url => $count) {
            if (count($data['autocacheproposed']) >= $cachingConfiguration['AutoCache.MaxPages'] ||
                $count < $cachingConfiguration['AutoCache.Threshold']) {
                break;
            }
            $data['autocacheproposed'][$url] = $count;
        }
    }

    // Get some page caching configurations
    //$data['pages'] = xarMod::apiFunc('xarcachemanager', 'admin', 'getpages');
    $data['pages'] = ['todo' => 'something ?'];

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
