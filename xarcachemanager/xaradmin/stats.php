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
        case 'block':
        case 'query':
// TODO: Get some page/block/query cache statistics when available
            break;

        case 'overview':
        default:
            break;
    }

    return $data;
}

?>
