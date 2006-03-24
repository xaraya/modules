<?php
/*
 * Statistics
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * cache statistics
 */
function xarcachemanager_admin_stats($args)
{ 
    if (!xarSecurityCheck('AdminXarCache')) return;

    extract($args);
    if (!xarVarFetch('tab',      'str',      $tab, 'overview', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('sort',     'str',     $sort,         '', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('reset',    'str',    $reset,         '', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('startnum', 'int', $startnum,          1, XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('withlog',  'int',  $withlog,          0, XARVAR_NOT_REQUIRED)) { return; }

    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    $outputCacheDir = $varCacheDir . '/output';
    
    //Make sure xarCache is included so you can view stats even if caching is disabled
    if (!defined('XARCACHE_IS_ENABLED')) {
        include_once('includes/xarCache.php');
        xarCache_init();
    }

    $numitems = xarModGetVar('xarcachemanager','itemsperpage');
    if (empty($numitems)) {
        $numitems = 100;
        xarModSetVar('xarcachemanager','itemsperpage',$numitems);
    }

    $data = array();
    $data['tab'] = $tab;
    $data['itemsperpage'] = $numitems;

    // get the caching config settings from the config file
    $data['settings'] = xarModAPIFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                      array('from' => 'file', 'tpl_prep' => TRUE));

    $data['PageCachingEnabled'] = 0;
    $data['BlockCachingEnabled'] = 0;
    $data['ModCachingEnabled'] = 0;
    $data['QueryCachingEnabled'] = 0;
    $data['AutoCachingEnabled'] = 0;
    if (defined('XARCACHE_PAGE_IS_ENABLED')) {
        $data['PageCachingEnabled'] = 1;
        if (file_exists($outputCacheDir . '/autocache.log')) {
            $data['AutoCachingEnabled'] = 1;
        }
    }
    if (defined('XARCACHE_BLOCK_IS_ENABLED')) {
        $data['BlockCachingEnabled'] = 1;
    }
    if (defined('XARCACHE_MOD_IS_ENABLED')) {
        $data['ModCachingEnabled'] = 1;
    }
    // TODO: bring in line with other cache systems ?
    $data['QueryCachingEnabled'] = 1;

    switch ($tab) {
        case 'page':
        case 'block':
        case 'mod':
            $upper = ucfirst($tab);
            $enabled = $upper . 'CachingEnabled'; // e.g. PageCachingEnabled
            $storage = $upper . 'CacheStorage'; // e.g. BlockCacheStorage
            $logfile = $upper . 'LogFile'; // e.g. ModLogFile

            if (!empty($reset)) {
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;

                if (!empty($data['settings'][$logfile]) && file_exists($data['settings'][$logfile])) {
                    $fh = fopen($data['settings'][$logfile], 'w');
                    if (!empty($fh)) fclose($fh);
                }

                xarResponseRedirect(xarModURL('xarcachemanager','admin','stats',
                                              array('tab' => $tab)));
                return true;
            }
            if (!empty($data[$enabled]) && !empty($data['settings'][$storage])) {
                // get cache storage
                $cachestorage = xarCache_getStorage(array('storage'  => $data['settings'][$storage],
                                                          'type'     => $tab,
                                                          'cachedir' => $outputCacheDir));
                // get size of the cache
                $data['size'] = $cachestorage->getCacheSize(true);
                // get number of items in cache
                $data['numitems'] = $cachestorage->getCacheItems();
                // get a list of items in cache
                $data['items'] = $cachestorage->getCachedList();
                // analyze logfile
                if (!empty($withlog) && !empty($data['settings'][$logfile]) && file_exists($data['settings'][$logfile]) && filesize($data['settings'][$logfile]) > 0) {
                    $data['withlog'] = 1;
                    $data['totals'] = array();
                    xarcachemanager_stats_logfile($data['items'], $data['totals'], $data['settings'][$logfile], $tab);
                } else {
                    $data['withlog'] = null;
                }
                // sort items
                if (empty($sort) || $sort == 'id') {
                    $sort = null;
                    ksort($data['items']);
                } else {
                    xarcachemanager_stats_sortitems($data['items'], $sort);
                }
                // get pager
                $count = count($data['items']);
                if ($count > $numitems) {
                    $keys = array_slice(array_keys($data['items']),$startnum - 1,$numitems);
                    $items = array();
                    foreach ($keys as $key) {
                        $items[$key] = $data['items'][$key];
                    }
                    $data['items'] = $items;
                    unset($keys);
                    unset($items);
                    $data['pager'] = xarTplGetPager($startnum,
                                                    $count,
                                                    xarModURL('xarcachemanager','admin','stats',
                                                              array('tab' => $tab,
                                                                    'withlog' => empty($data['withlog']) ? null : 1,
                                                                    'sort' => $sort,
                                                                    'startnum' => '%%')),
                                                    $numitems);
                }
            } else {
                $data['items'] = array();
                $data['withlog'] = null;
            }
            break;

        case 'query':
// TODO: Get some query cache statistics when available
            break;

        case 'autocache':
            if (!empty($reset)) {
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;

                if (!empty($withlog)) {
                    if (file_exists($outputCacheDir . '/autocache.log')) {
                        $fh = fopen($outputCacheDir . '/autocache.log', 'w');
                        if (!empty($fh)) fclose($fh);
                    }
                } elseif (file_exists($outputCacheDir . '/autocache.stats')) {
                    $fh = fopen($outputCacheDir . '/autocache.stats', 'w');
                    if (!empty($fh)) fclose($fh);
                }

                xarResponseRedirect(xarModURL('xarcachemanager','admin','stats',
                                              array('tab' => 'autocache')));
                return true;
            }

            // Get some statistics from the auto-cache stats file
            $data['items'] = array();
            $data['totals'] = array('hit' => 0,
                                    'miss' => 0,
                                    'total' => 0,
                                    'ratio' => 0,
                                    'first' => 0,
                                    'last' => 0);
            if (file_exists($outputCacheDir . '/autocache.stats') &&
                filesize($outputCacheDir . '/autocache.stats') > 0) {

                // analyze statsfile
                xarcachemanager_stats_autostats($data['items'], $data['totals'], $outputCacheDir . '/autocache.stats');
            }
            if (!empty($withlog) && file_exists($outputCacheDir . '/autocache.log') &&
                filesize($outputCacheDir . '/autocache.log') > 0) {

                $data['withlog'] = 1;
                // analyze logfile and merge with stats items
                xarcachemanager_stats_autolog($data['items'], $data['totals'], $outputCacheDir . '/autocache.log');
            }
            if (count($data['items']) > 0) {
                // sort items
                if (empty($sort) || $sort == 'page') {
                    $sort = null;
                    ksort($data['items']);
                } else {
                    xarcachemanager_stats_sortitems($data['items'], $sort);
                }
                // get pager
                $count = count($data['items']);
                if ($count > $numitems) {
                    $keys = array_slice(array_keys($data['items']),$startnum - 1,$numitems);
                    $items = array();
                    foreach ($keys as $key) {
                        $items[$key] = $data['items'][$key];
                    }
                    $data['items'] = $items;
                    unset($keys);
                    unset($items);
                    $data['pager'] = xarTplGetPager($startnum,
                                                    $count,
                                                    xarModURL('xarcachemanager','admin','stats',
                                                              array('tab' => 'autocache',
                                                                    'sort' => $sort,
                                                                    'startnum' => '%%')),
                                                    $numitems);
                }
            }
            break;

        case 'overview':
        default:
            // set items per page
            if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 0, XARVAR_NOT_REQUIRED)) { return; }
            if (!empty($itemsperpage)) {
                xarModSetVar('xarcachemanager','itemsperpage',$itemsperpage);
                $data['itemsperpage'] = $itemsperpage;
            }
            // list of cache types to check
            $typelist = array('page', 'block', 'mod');
            foreach ($typelist as $type) {
                $upper = ucfirst($type);
                $enabled = $upper . 'CachingEnabled'; // e.g. PageCachingEnabled
                $storage = $upper . 'CacheStorage'; // e.g. BlockCacheStorage
                $logfile = $upper . 'LogFile'; // e.g. ModLogFile
                $cachevar = $type . 'cache'; // e.g. pagecache
                $logvar = $type . 'log'; // e.g. blocklog

                // get cache stats
                $data[$cachevar] = array('size'  => 0,
                                         'items' => 0);
                if ($data[$enabled] && !empty($data['settings'][$storage])) {
                    $cachestorage = xarCache_getStorage(array('storage'  => $data['settings'][$storage],
                                                              'type'     => $type,
                                                              'cachedir' => $outputCacheDir));
                    $data[$cachevar]['size'] = $cachestorage->getCacheSize(true);
                    $data[$cachevar]['items'] = $cachestorage->getCacheItems();
                }
                // get logfile stats
                if ($data[$enabled] && !empty($data['settings'][$logfile])) {
                    $data[$logvar] = array();
                    // status field = 1
                    xarcachemanager_stats_filestats($data[$logvar], $data['settings'][$logfile], 1, 1);
                }
            }

        // Note: the query cache is actually handled by ADODB
            // get query cache stats
            $data['settings']['QueryCacheStorage'] = 'filesystem';
            $data['querycache'] = array('size'  => 0,
                                        'items' => 0);
            if ($data['QueryCachingEnabled'] && !empty($data['settings']['QueryCacheStorage'])) {
                $querystorage = xarCache_getStorage(array('storage'  => $data['settings']['QueryCacheStorage'],
                                                          'type'     => 'adodb',
                                                          'cachedir' => 'var/cache'));
                $data['querycache']['size'] = $querystorage->getCacheSize(true);
                $data['querycache']['items'] = $querystorage->getCacheItems() - 1; // index.html
            }

            // get auto-cache stats
            $data['settings']['AutoCacheLogFile'] = $outputCacheDir . '/autocache.log';
            if ($data['AutoCachingEnabled'] && !empty($data['settings']['AutoCacheLogFile'])) {
                $data['autocachelog'] = array();
                // status field = 1
                xarcachemanager_stats_filestats($data['autocachelog'], $data['settings']['AutoCacheLogFile'], 1, 1);
            }
            if ($data['AutoCachingEnabled'] && file_exists($outputCacheDir . '/autocache.stats')) {
                $data['settings']['AutoCacheStatFile'] = $outputCacheDir . '/autocache.stats';
            } else {
                $data['settings']['AutoCacheStatFile'] = '';
            }
            if ($data['AutoCachingEnabled'] && !empty($data['settings']['AutoCacheStatFile'])) {
                $data['autocachestat'] = array();
                // hit field = 1, miss field = 2
                xarcachemanager_stats_filestats($data['autocachestat'], $data['settings']['AutoCacheStatFile'], 1, 2);
            }
            break;
    }

    return $data;
}

/**
 * count the total number of lines, hits and misses in a logfile
 */
function xarcachemanager_stats_filestats(&$totals, $logfile, $hitfield = null, $missfield = null)
{
    $totals = array('size'  => 0,
                    'lines' => 0,
                    'hit'   => 0,
                    'miss'  => 0,
                    'total' => 0,
                    'ratio' => 0);
    if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
        return;
    }

    $totals['size'] = filesize($logfile);

    $fp = fopen($logfile,'r');
    if (empty($fp)) return;

    while (!feof($fp)) {
        $entry = fgets($fp,1024);
        $entry = trim($entry);
        if (empty($entry)) continue;
        $totals['lines']++;
        if (!isset($hitfield) || !isset($missfield)) continue;
        $fields = explode(' ',$entry);
        // we're dealing with a status field in a logfile
        if ($hitfield == $missfield) {
            if (!isset($fields[$hitfield])) continue;
            $status = strtolower($fields[$hitfield]);
            $totals[$status]++;
        // we're dealing with separate fields in a stats file
        } else {
            if (!isset($fields[$hitfield]) || !isset($fields[$missfield])) continue;
            $totals['hit'] += $fields[$hitfield];
            $totals['miss'] += $fields[$missfield];
        }
    }
    fclose($fp);
    $totals['total'] = $totals['hit'] + $totals['miss'];
    if (!empty($totals['total'])) {
        $totals['ratio'] = sprintf("%.1f",100.0 * $totals['hit'] / $totals['total']);
    } else {
        $totals['ratio'] = 0.0;
    }
}

/**
 * analyze cache storage logfile for hits and misses and merge with items list
 */
function xarcachemanager_stats_logfile(&$items, &$totals, $logfile, $checktype)
{
    if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
        return;
    }

    $stats = array();
    $pages = array();
    $fh = fopen($logfile, 'r');
    if (empty($fh)) return;

    while (!feof($fh)) {
        $entry = fgets($fh, 1024);
        $entry = trim($entry);
        if (empty($entry)) continue;
        list($time,$status,$type,$key,$code,$addr,$url) = explode(' ',$entry);
        if ($type != $checktype) continue;
        $status = strtolower($status);
        if (!isset($stats[$key])) {
            $stats[$key] = array();
        }
        if (!isset($stats[$key][$code])) {
            $stats[$key][$code] = array('hit'   => 0,
                                        'miss'  => 0,
                                        'first' => $time,
                                        'last'  => 0,
                                        'pages' => array());
        }
        $stats[$key][$code][$status]++;
        $stats[$key][$code]['last'] = $time;
        if (!isset($stats[$key][$code]['pages'][$url])) {
            $stats[$key][$code]['pages'][$url] = 0;
        }
        $stats[$key][$code]['pages'][$url]++;
        if (!isset($pages[$url])) {
            $pages[$url] = 0;
        }
        $pages[$url]++;
    }
    $totals = array('hit'   => 0,
                    'miss'  => 0,
                    'total' => 0,
                    'ratio' => 0,
                    'first' => 0,
                    'last'  => 0,
                    'pages' => count($pages));
    unset($pages);

    $keycode2id = array();
    foreach (array_keys($items) as $id) {
        $keycode = $items[$id]['key'] . '-' . $items[$id]['code'];
        $keycode2id[$keycode] = $id;
    }
    // calculate totals and ratios
    foreach (array_keys($stats) as $key) {
        foreach (array_keys($stats[$key]) as $code) {
            $keycode = $key . '-' . $code;
            if (isset($keycode2id[$keycode])) {
                $id = $keycode2id[$keycode];
                $items[$id]['hit'] = $stats[$key][$code]['hit'];
                $items[$id]['miss'] = $stats[$key][$code]['miss'];
                $items[$id]['total'] = $stats[$key][$code]['hit'] + $stats[$key][$code]['miss'];
                if (!empty($items[$id]['total'])) {
                    $items[$id]['ratio'] = sprintf("%.1f",100.0 * $items[$id]['hit'] / $items[$id]['total']);
                } else {
                    $items[$id]['ratio'] = 0.0;
                }
                $items[$id]['first'] = $stats[$key][$code]['first'];
                $items[$id]['last'] = $stats[$key][$code]['last'];
                $items[$id]['pages'] = count($stats[$key][$code]['pages']);
            } else {
                $item = array('key'   => $key,
                              'code'  => $code,
                              'time'  => 0,
                              'size'  => -1,
                              'check' => '');
                $item['hit'] = $stats[$key][$code]['hit'];
                $item['miss'] = $stats[$key][$code]['miss'];
                $item['total'] = $stats[$key][$code]['hit'] + $stats[$key][$code]['miss'];
                if (!empty($item['total'])) {
                    $item['ratio'] = sprintf("%.1f",100.0 * $item['hit'] / $item['total']);
                } else {
                    $item['ratio'] = 0.0;
                }
                $item['first'] = $stats[$key][$code]['first'];
                $item['last'] = $stats[$key][$code]['last'];
                $item['pages'] = count($stats[$key][$code]['pages']);
                $items[] = $item;
            }
            $totals['hit'] += $stats[$key][$code]['hit'];
            $totals['miss'] += $stats[$key][$code]['miss'];
            if (empty($totals['first']) ||
                $totals['first'] > $stats[$key][$code]['first']) {
                $totals['first'] = $stats[$key][$code]['first'];
            }
            if (empty($totals['last']) ||
                $totals['last'] < $stats[$key][$code]['last']) {
                $totals['last'] = $stats[$key][$code]['last'];
            }
        }
    }
    $totals['total'] = $totals['hit'] + $totals['miss'];
    if (!empty($totals['total'])) {
        $totals['ratio'] = sprintf("%.1f",100.0 * $totals['hit'] / $totals['total']);
    } else {
        $totals['ratio'] = 0.0;
    }
    unset($keycode2id);
    unset($stats);
    foreach (array_keys($items) as $id) {
        if (!isset($items[$id]['hit'])) {
            $items[$id]['hit'] = '';
            $items[$id]['miss'] = '';
            $items[$id]['total'] = '';
            $items[$id]['ratio'] = '';
            $items[$id]['first'] = '';
            $items[$id]['last'] = '';
            $items[$id]['pages'] = '';
        }
    }
}

/**
 * analyze auto-cache statsfile for hits and misses
 */
function xarcachemanager_stats_autostats(&$items, &$totals, $logfile)
{
    if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
        return;
    }

    $fh = fopen($logfile, 'r');
    if (empty($fh)) return;

    while (!feof($fh)) {
        $entry = fgets($fh, 1024);
        $entry = trim($entry);
        if (empty($entry)) continue;
        list($url,$hit,$miss,$first,$last) = explode(' ',$entry);
        $page = $url;
        if (strlen($page) > 105) {
            $page = wordwrap($page,105,"\n",1);
        }
        $page = xarVarPrepForDisplay($page);
        $items[$url] = array('page' => $page,
                             'hit' => $hit,
                             'miss' => $miss,
                             'total' => ($hit + $miss),
                             'ratio' => sprintf("%.1f",100.0 * $hit / ($hit + $miss)),
                             'first' => $first,
                             'last' => $last);
        $totals['hit'] += $hit;
        $totals['miss'] += $miss;
        if (empty($totals['first']) ||
            $totals['first'] > $first) {
            $totals['first'] = $first;
        }
        if (empty($totals['last']) ||
            $totals['last'] < $last) {
            $totals['last'] = $last;
        }
    }
    fclose($fh);
    $totals['total'] = $totals['hit'] + $totals['miss'];
    if (!empty($totals['total'])) {
        $totals['ratio'] = sprintf("%.1f",100.0 * $totals['hit'] / $totals['total']);
    } else {
        $totals['ratio'] = 0.0;
    }

}

/**
 * analyze auto-cache logfile for hits and misses and merge with stats items
 */
function xarcachemanager_stats_autolog(&$items, &$totals, $logfile)
{
    if (empty($logfile) || !file_exists($logfile) || filesize($logfile) < 1) {
        return;
    }

    $fh = fopen($logfile, 'r');
    if (empty($fh)) return;

    while (!feof($fh)) {
        $entry = fgets($fh, 1024);
        $entry = trim($entry);
        if (empty($entry)) continue;
        list($time,$status,$addr,$url) = explode(' ',$entry);
        $status = strtolower($status);
        if (!isset($items[$url])) {
            $items[$url] =  array('hit'   => 0,
                                  'miss'  => 0,
                                  'first' => $time,
                                  'last'  => 0);
        }
        $items[$url][$status]++;
        if (empty($items[$url]['first']) ||
            $items[$url]['first'] > $time) {
            $items[$url]['first'] = $time;
        }
        if (empty($items[$url]['last']) ||
            $items[$url]['last'] < $time) {
            $items[$url]['last'] = $time;
        }
    }
    fclose($fh);
    $totals = array('hit'   => 0,
                    'miss'  => 0,
                    'total' => 0,
                    'ratio' => 0,
                    'first' => 0,
                    'last'  => 0);

    // re-calculate totals and ratios
    foreach (array_keys($items) as $url) {
        $page = $url;
        if (strlen($page) > 105) {
            $page = wordwrap($page,105,"\n",1);
        }
        $items[$url]['page'] = xarVarPrepForDisplay($page);
        $items[$url]['total'] = $items[$url]['hit'] + $items[$url]['miss'];
        if (!empty($items[$url]['total'])) {
            $items[$url]['ratio'] = sprintf("%.1f",100.0 * $items[$url]['hit'] / $items[$url]['total']);
        } else {
            $items[$url]['ratio'] = 0.0;
        }
        $totals['hit'] += $items[$url]['hit'];
        $totals['miss'] += $items[$url]['miss'];
        if (empty($totals['first']) ||
            $totals['first'] > $items[$url]['first']) {
            $totals['first'] = $items[$url]['first'];
        }
        if (empty($totals['last']) ||
            $totals['last'] < $items[$url]['last']) {
            $totals['last'] = $items[$url]['last'];
        }
    }
    $totals['total'] = $totals['hit'] + $totals['miss'];
    if (!empty($totals['total'])) {
        $totals['ratio'] = sprintf("%.1f",100.0 * $totals['hit'] / $totals['total']);
    } else {
        $totals['ratio'] = 0.0;
    }
}

/**
 * sort items
 */
function xarcachemanager_stats_sortitems(&$items, $sort)
{
    $sort = strtolower($sort);

    switch($sort)
    {
        case 'key':
        case 'code':
            $sortcode = 'return strcmp($a["' . $sort . '"],$b["' . $sort . '"]);';
            break;

        case 'time':
        case 'size':
        case 'hit':
        case 'miss':
        case 'total':
        case 'ratio':
        case 'first':
        case 'last':
        case 'pages':
            $sortcode = 'if ($a["' . $sort . '"] == $b["' . $sort . '"]) return 0;
                         return ($a["' . $sort . '"] > $b["' . $sort . '"]) ? -1 : 1;';
            break;

        default:
            return;
    }
    $sortfunc = create_function('$a, $b', $sortcode);
    uasort($items, $sortfunc);
}

?>
