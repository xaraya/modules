<?php

/**
 * cache statistics
 */
function xarcachemanager_admin_stats($args)
{ 
    if (!xarSecurityCheck('AdminXarCache')) return;

    extract($args);
    if (!xarVarFetch('tab',   'str',   $tab, 'overview', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('sort',  'str',  $sort,         '', XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('reset', 'str', $reset,         '', XARVAR_NOT_REQUIRED)) { return; }

    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    $outputCacheDir = $varCacheDir . '/output';

    $data = array();
    $data['tab'] = $tab;

    // get the caching config settings from the config file
    $data['settings'] = xarModAPIFunc('xarcachemanager', 'admin', 'get_cachingconfig',
                                      array('from' => 'file', 'tpl_prep' => TRUE));

    $data['pageCachingEnabled'] = 0;
    $data['blockCachingEnabled'] = 0;
    $data['queryCachingEnabled'] = 0;
    $data['autoCachingEnabled'] = 0;
    if (file_exists($outputCacheDir . '/cache.touch')) {
        if (file_exists($outputCacheDir . '/cache.pagelevel')) {
            $data['pageCachingEnabled'] = 1;
            if (file_exists($outputCacheDir . '/autocache.log')) {
                $data['autoCachingEnabled'] = 1;
            }
        }
        if (file_exists($outputCacheDir . '/cache.blocklevel')) {
            $data['blockCachingEnabled'] = 1;
        }
// FIXME: bring in line with other cache systems
        $data['queryCachingEnabled'] = 1;
    }

    switch ($tab) {
        case 'autocache':
            if (!empty($reset)) {
                // Confirm authorisation code
                if (!xarSecConfirmAuthKey()) return;

                if (file_exists($outputCacheDir . '/autocache.stats')) {
                    $fh = fopen($outputCacheDir . '/autocache.stats', 'w');
                    if (!empty($fh)) fclose($fh);
                }

                xarResponseRedirect(xarModURL('xarcachemanager','admin','stats',
                                              array('tab' => 'autocache')));
                return true;
            }

            // Get some statistics from the auto-cache stats file
            $data['autocachestats'] = array();
            $data['autocachetotal'] = array('hit' => 0,
                                            'miss' => 0,
                                            'total' => 0,
                                            'ratio' => 0,
                                            'first' => 0,
                                            'last' => 0);
            if (file_exists($outputCacheDir . '/autocache.stats') &&
                filesize($outputCacheDir . '/autocache.stats') > 0) {

                $fh = fopen($outputCacheDir . '/autocache.stats', 'r');
                while (!feof($fh)) {
                    $entry = fgets($fh, 1024);
                    $entry = trim($entry);
                    if (empty($entry)) continue;
                    list($url,$hit,$miss,$first,$last) = explode(' ',$entry);
                    $url = xarVarPrepForDisplay($url);
                    $data['autocachestats'][$url] = array('hit' => $hit,
                                                          'miss' => $miss,
                                                          'total' => ($hit + $miss),
                                                          'ratio' => sprintf("%.1f",100.0 * $hit / ($hit + $miss)),
                                                          'first' => $first,
                                                          'last' => $last);
                    $data['autocachetotal']['hit'] += $hit;
                    $data['autocachetotal']['miss'] += $miss;
                    if (empty($data['autocachetotal']['first']) ||
                        $data['autocachetotal']['first'] > $first) {
                        $data['autocachetotal']['first'] = $first;
                    }
                    if (empty($data['autocachetotal']['last']) ||
                        $data['autocachetotal']['last'] < $last) {
                        $data['autocachetotal']['last'] = $last;
                    }
                }
                fclose($fh);
                if (empty($sort) || $sort == 'page') {
                    ksort($data['autocachestats']);
                } elseif (strtolower($sort) == 'hit') {
                    $sortfunc = create_function('$a, $b','if ($a["hit"] == $b["hit"]) return 0;
                                                          return ($a["hit"] > $b["hit"]) ? -1 : 1;');
                    uasort($data['autocachestats'], $sortfunc);
                } elseif (strtolower($sort) == 'miss') {
                    $sortfunc = create_function('$a, $b','if ($a["miss"] == $b["miss"]) return 0;
                                                          return ($a["miss"] > $b["miss"]) ? -1 : 1;');
                    uasort($data['autocachestats'], $sortfunc);
                } elseif (strtolower($sort) == 'total') {
                    $sortfunc = create_function('$a, $b','if ($a["total"] == $b["total"]) return 0;
                                                          return ($a["total"] > $b["total"]) ? -1 : 1;');
                    uasort($data['autocachestats'], $sortfunc);
                } elseif (strtolower($sort) == 'ratio') {
                    $sortfunc = create_function('$a, $b','if ($a["ratio"] == $b["ratio"]) return 0;
                                                          return ($a["ratio"] > $b["ratio"]) ? -1 : 1;');
                    uasort($data['autocachestats'], $sortfunc);
                } elseif (strtolower($sort) == 'first') {
                    $sortfunc = create_function('$a, $b','if ($a["first"] == $b["first"]) return 0;
                                                          return ($a["first"] > $b["first"]) ? -1 : 1;');
                    uasort($data['autocachestats'], $sortfunc);
                } elseif (strtolower($sort) == 'last') {
                    $sortfunc = create_function('$a, $b','if ($a["last"] == $b["last"]) return 0;
                                                          return ($a["last"] > $b["last"]) ? -1 : 1;');
                    uasort($data['autocachestats'], $sortfunc);
                }
                $data['autocachetotal']['total'] = $data['autocachetotal']['hit'] + $data['autocachetotal']['miss'];
                $data['autocachetotal']['ratio'] = sprintf("%.1f",100.0 * $data['autocachetotal']['hit'] / ($data['autocachetotal']['hit'] + $data['autocachetotal']['miss']));
            }
            break;

        case 'page':
            if ($data['pageCachingEnabled'] && !empty($data['settings']['PageCacheStorage'])) {
                $pagestorage = xarCache_getStorage(array('storage'  => $data['settings']['PageCacheStorage'],
                                                         'type'     => 'page',
                                                         'cachedir' => $outputCacheDir));
                $data['items'] = $pagestorage->getCachedList();
                if (empty($sort) || $sort == 'id') {
                    ksort($data['items']);
                } elseif (strtolower($sort) == 'key') {
                    $sortfunc = create_function('$a, $b','return strcmp($a["key"],$b["key"]);');
                    uasort($data['items'], $sortfunc);
                } elseif (strtolower($sort) == 'code') {
                    $sortfunc = create_function('$a, $b','return strcmp($a["code"],$b["code"]);');
                    uasort($data['items'], $sortfunc);
                } elseif (strtolower($sort) == 'time') {
                    $sortfunc = create_function('$a, $b','if ($a["time"] == $b["time"]) return 0;
                                                          return ($a["time"] > $b["time"]) ? -1 : 1;');
                    uasort($data['items'], $sortfunc);
                } elseif (strtolower($sort) == 'size') {
                    $sortfunc = create_function('$a, $b','if ($a["size"] == $b["size"]) return 0;
                                                          return ($a["size"] > $b["size"]) ? -1 : 1;');
                    uasort($data['items'], $sortfunc);
                }
            }
            break;

        case 'block':
            if ($data['blockCachingEnabled'] && !empty($data['settings']['BlockCacheStorage'])) {
                $blockstorage = xarCache_getStorage(array('storage'  => $data['settings']['BlockCacheStorage'],
                                                          'type'     => 'block',
                                                          'cachedir' => $outputCacheDir));
                $data['items'] = $blockstorage->getCachedList();
                if (empty($sort) || $sort == 'id') {
                    ksort($data['items']);
                } elseif (strtolower($sort) == 'key') {
                    $sortfunc = create_function('$a, $b','return strcmp($a["key"],$b["key"]);');
                    uasort($data['items'], $sortfunc);
                } elseif (strtolower($sort) == 'code') {
                    $sortfunc = create_function('$a, $b','return strcmp($a["code"],$b["code"]);');
                    uasort($data['items'], $sortfunc);
                } elseif (strtolower($sort) == 'time') {
                    $sortfunc = create_function('$a, $b','if ($a["time"] == $b["time"]) return 0;
                                                          return ($a["time"] > $b["time"]) ? -1 : 1;');
                    uasort($data['items'], $sortfunc);
                } elseif (strtolower($sort) == 'size') {
                    $sortfunc = create_function('$a, $b','if ($a["size"] == $b["size"]) return 0;
                                                          return ($a["size"] > $b["size"]) ? -1 : 1;');
                    uasort($data['items'], $sortfunc);
                }
            }
            break;

        case 'query':
// TODO: Get some page/block/query cache statistics when available
            break;

        case 'overview':
        default:
            if ($data['pageCachingEnabled'] && !empty($data['settings']['PageCacheStorage'])) {
                $pagestorage = xarCache_getStorage(array('storage'  => $data['settings']['PageCacheStorage'],
                                                         'type'     => 'page',
                                                         'cachedir' => $outputCacheDir));
                $data['pagecachesize'] = $pagestorage->getCacheSize(true);
                $data['pagecacheitems'] = $pagestorage->getCacheItems();
            } else {
                $data['pagecachesize'] = 0;
                $data['pagecacheitems'] = 0;
            }
            if ($data['pageCachingEnabled'] && !empty($data['settings']['PageLogFile']) &&
                file_exists($data['settings']['PageLogFile']) && filesize($data['settings']['PageLogFile']) > 0) {
                $data['pagelogsize'] = filesize($data['settings']['PageLogFile']);
                $data['pageloglines'] = 0;
                $fp = fopen($data['settings']['PageLogFile'],'r');
                if ($fp) {
                    while (!feof($fp)) {
                        $dummy = fgets($fp,1024);
                        $data['pageloglines']++;
                    }
                    fclose($fp);
                }
            } else {
                $data['pagelogsize'] = 0;
                $data['pageloglines'] = 0;
            }

            if ($data['blockCachingEnabled'] && !empty($data['settings']['BlockCacheStorage'])) {
                $blockstorage = xarCache_getStorage(array('storage'  => $data['settings']['BlockCacheStorage'],
                                                          'type'     => 'block',
                                                          'cachedir' => $outputCacheDir));
                $data['blockcachesize'] = $blockstorage->getCacheSize(true);
                $data['blockcacheitems'] = $blockstorage->getCacheItems();
            } else {
                $data['blockcachesize'] = 0;
                $data['blockcacheitems'] = 0;
            }
            if ($data['blockCachingEnabled'] && !empty($data['settings']['BlockLogFile']) &&
                file_exists($data['settings']['BlockLogFile']) && filesize($data['settings']['BlockLogFile']) > 0) {
                $data['blocklogsize'] = filesize($data['settings']['BlockLogFile']);
                $data['blockloglines'] = 0;
                $fp = fopen($data['settings']['BlockLogFile'],'r');
                if ($fp) {
                    while (!feof($fp)) {
                        $dummy = fgets($fp,1024);
                        $data['blockloglines']++;
                    }
                    fclose($fp);
                }
            } else {
                $data['blocklogsize'] = 0;
                $data['blockloglines'] = 0;
            }

        // Note: the query cache is actually handled by ADODB
            $data['settings']['QueryCacheStorage'] = 'filesystem';
            if ($data['blockCachingEnabled'] && !empty($data['settings']['BlockCacheStorage'])) {
                $querystorage = xarCache_getStorage(array('storage'  => $data['settings']['QueryCacheStorage'],
                                                          'type'     => 'adodb',
                                                          'cachedir' => 'var/cache'));
                $data['querycachesize'] = $querystorage->getCacheSize(true);
                $data['querycacheitems'] = $querystorage->getCacheItems() - 1; // index.html
            } else {
                $data['querycachesize'] = 0;
                $data['querycacheitems'] = 0;
            }

            $data['settings']['AutoCacheLogFile'] = $outputCacheDir . '/autocache.log';
            if ($data['autoCachingEnabled'] && !empty($data['settings']['AutoCacheLogFile']) &&
                file_exists($data['settings']['AutoCacheLogFile']) && filesize($data['settings']['AutoCacheLogFile']) > 0) {
                $data['autocachelogsize'] = filesize($data['settings']['AutoCacheLogFile']);
                $data['autocacheloglines'] = 0;
                $fp = fopen($data['settings']['AutoCacheLogFile'],'r');
                if ($fp) {
                    while (!feof($fp)) {
                        $dummy = fgets($fp,1024);
                        $data['autocacheloglines']++;
                    }
                    fclose($fp);
                }
            } else {
                $data['autocachelogsize'] = 0;
                $data['autocacheloglines'] = 0;
            }
            if ($data['autoCachingEnabled'] && file_exists($outputCacheDir . '/autocache.stats')) {
                $data['settings']['AutoCacheStatFile'] = $outputCacheDir . '/autocache.stats';
            } else {
                $data['settings']['AutoCacheStatFile'] = '';
            }
            if ($data['autoCachingEnabled'] && !empty($data['settings']['AutoCacheStatFile']) &&
                file_exists($data['settings']['AutoCacheStatFile']) && filesize($data['settings']['AutoCacheStatFile']) > 0) {
                $data['autocachestatsize'] = filesize($data['settings']['AutoCacheStatFile']);
                $data['autocachestatlines'] = 0;
                $fp = fopen($data['settings']['AutoCacheStatFile'],'r');
                if ($fp) {
                    while (!feof($fp)) {
                        $dummy = fgets($fp,1024);
                        $data['autocachestatlines']++;
                    }
                    fclose($fp);
                }
            } else {
                $data['autocachestatsize'] = 0;
                $data['autocachestatlines'] = 0;
            }
            break;
    }

    return $data;
}

?>
