<?php

/**
 * configure page caching (TODO)
 */
function xarcachemanager_admin_pages($args)
{ 
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) return;

    $data = array();

    $varCacheDir = xarCoreGetVarDirPath() . '/cache';
    if (file_exists($varCacheDir . '/output/cache.pagelevel')) {
        $iscached = 1;
    } else {
        $iscached = 0;
    }

    if (empty($iscached)) {
        $data['pages'] = array();
        return $data;
    }

    $cachingConfigFile = $varCacheDir . '/config.caching.php';

    if (!file_exists($cachingConfigFile)) {
        $msg=xarML('That is strange.  The #(1) file seems to be 
                    missing.', $cachingConfigFile);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_FILE_NOT_EXIST',
                        new SystemException($msg));
            
        return false;
    }

    include $cachingConfigFile;

    $keyslist = str_replace( '.', '', array_keys($cachingConfiguration));
    $valueslist = array_values($cachingConfiguration);
    $data['settings'] = array();
    
    $arraysize = sizeof($keyslist);
    for ($i=0;$i<$arraysize;$i++) {
        $data['settings'][$keyslist[$i]] = $valueslist[$i];
    }

    $filter = array('Class' => 2);
    $data['themes'] = xarModAPIFunc('themes',
                                    'admin',
                                    'getlist', $filter);

    $data['groups'] = xarModAPIFunc('roles','user','getallgroups');

    xarVarFetch('submit','str',$submit,'');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        xarVarFetch('groups','isset',$groups,array(),XARVAR_NOT_REQUIRED);
        $grouplist = array();
        foreach ($data['groups'] as $idx => $group) {
            if (!empty($groups[$group['uid']])) {
                $data['groups'][$idx]['checked'] = 1;
                $grouplist[] = $group['uid'];
            }
        }
        $cachegroups = join(';', $grouplist); 
    
        xarVarFetch('sessionless','isset',$sessionless,'',XARVAR_NOT_REQUIRED);
        $sessionlesslist = '';
        if (!empty($sessionless)) {
            $urls = preg_split('/\s+/',$sessionless,-1,PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServerGetBaseURL();
            $checkurls = array();
            foreach ($urls as $url) {
                if (empty($url) || !strstr($url,$baseurl)) continue;
                $checkurls[] = $url;
            }
            if (count($checkurls) > 0) {
                $sessionlesslist = "'" . join("','",$checkurls) . "'";
            }
        }

        xarVarFetch('autocache','isset',$autocache,'',XARVAR_NOT_REQUIRED);
        if (empty($autocache['period'])) {
            $autocache['period'] = 0;
        }
        $autocache['period'] = xarModAPIFunc('xarcachemanager', 'admin', 'convertseconds',
                                             array('starttime' => $autocache['period'],
                                                   'direction' => 'to'));
        if (empty($autocache['threshold'])) {
            $autocache['threshold'] = 0;
        }
        if (empty($autocache['maxpages'])) {
            $autocache['maxpages'] = 0;
        }
        $includelist = '';
        if (!empty($autocache['include'])) {
            $urls = preg_split('/\s+/',$autocache['include'],-1,PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServerGetBaseURL();
            $checkurls = array();
            foreach ($urls as $url) {
                if (empty($url) || !strstr($url,$baseurl)) continue;
                $checkurls[] = $url;
            }
            if (count($checkurls) > 0) {
                $includelist = "'" . join("','",$checkurls) . "'";
            }
        }
        $excludelist = '';
        if (!empty($autocache['include'])) {
            $urls = preg_split('/\s+/',$autocache['exclude'],-1,PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServerGetBaseURL();
            $checkurls = array();
            foreach ($urls as $url) {
                if (empty($url) || !strstr($url,$baseurl)) continue;
                $checkurls[] = $url;
            }
            if (count($checkurls) > 0) {
                $excludelist = "'" . join("','",$checkurls) . "'";
            }
        }

        if (!is_writable($cachingConfigFile)) {
            $msg=xarML('The caching configuration file is not writable by the web server.  
                       #(1) must be writable by the web server for 
                       the output caching to be managed by xarCacheManager.', $cachingConfigFile);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                            new SystemException($msg));
            return false;
        }
    
        $cachingConfig = join('', file($cachingConfigFile));
   
        $cachingConfig = preg_replace('/\[\'Page.CacheGroups\'\]\s*=\s*(\'|\")(.*)\\1;/', "['Page.CacheGroups'] = '$cachegroups';", $cachingConfig);
    
        $cachingConfig = preg_replace('/\[\'Page.SessionLess\'\]\s*=\s*array\s*\((.*)\)\s*;/i', "['Page.SessionLess'] = array($sessionlesslist);", $cachingConfig);

        $cachingConfig = preg_replace('/\[\'AutoCache.Period\'\]\s*=\s*(.*?);/', "['AutoCache.Period'] = $autocache[period];", $cachingConfig);
        $cachingConfig = preg_replace('/\[\'AutoCache.Threshold\'\]\s*=\s*(.*?);/', "['AutoCache.Threshold'] = $autocache[threshold];", $cachingConfig);
        $cachingConfig = preg_replace('/\[\'AutoCache.MaxPages\'\]\s*=\s*(.*?);/', "['AutoCache.MaxPages'] = $autocache[maxpages];", $cachingConfig);
        $cachingConfig = preg_replace('/\[\'AutoCache.Include\'\]\s*=\s*array\s*\((.*)\)\s*;/i', "['AutoCache.Include'] = array($includelist);", $cachingConfig);
        $cachingConfig = preg_replace('/\[\'AutoCache.Exclude\'\]\s*=\s*array\s*\((.*)\)\s*;/i', "['AutoCache.Exclude'] = array($excludelist);", $cachingConfig);

        $fp = fopen ($cachingConfigFile, 'wb');
        fwrite ($fp, $cachingConfig);
        fclose ($fp);

        // set the cache dir
        $outputCacheDir = xarCoreGetVarDirPath() . '/cache/output';

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
        }

        xarResponseRedirect(xarModURL('xarcachemanager','admin','pages'));
        return true;

    } elseif (!empty($data['settings']['PageCacheGroups'])) {
        $grouplist = explode(';',$data['settings']['PageCacheGroups']);
        foreach ($data['groups'] as $idx => $group) {
            if (in_array($group['uid'],$grouplist)) {
                $data['groups'][$idx]['checked'] = 1;
            }
        }
    }

    if (!isset($data['settings']['PageSessionLess'])) {
        $data['sessionless'] = xarML("Please add the following line to your config.caching.php file :\n#(1)",
                                     '$cachingConfiguration[\'Page.SessionLess\'] = array();');
    } elseif (!empty($data['settings']['PageSessionLess']) && count($data['settings']['PageSessionLess']) > 0) {
        $data['sessionless'] = join("\n",$data['settings']['PageSessionLess']);
    } else {
        $data['sessionless'] = '';
    }

    if (!isset($data['settings']['AutoCachePeriod'])) {
        $data['settings']['AutoCachePeriod'] = 0;
    }
    $data['settings']['AutoCachePeriod'] = xarModAPIFunc('xarcachemanager', 'admin', 'convertseconds',
                                               array('starttime' => $data['settings']['AutoCachePeriod'],
                                                     'direction' => 'from'));

    if (!isset($data['settings']['AutoCacheThreshold'])) {
        $data['settings']['AutoCacheThreshold'] = 10;
    }
    if (!isset($data['settings']['AutoCacheMaxPages'])) {
        $data['settings']['AutoCacheMaxPages'] = 25;
    }
    if (!isset($data['settings']['AutoCacheInclude'])) {
        $data['settings']['AutoCacheInclude'] = xarServerGetBaseURL() . "\n" . xarServerGetBaseURL() . 'index.php';
    } elseif (is_array($data['settings']['AutoCacheInclude'])) {
        $data['settings']['AutoCacheInclude'] = join("\n",$data['settings']['AutoCacheInclude']);
    }
    if (!isset($data['settings']['AutoCacheExclude'])) {
        $data['settings']['AutoCacheExclude'] = '';
    } elseif (is_array($data['settings']['AutoCacheExclude'])) {
        $data['settings']['AutoCacheExclude'] = join("\n",$data['settings']['AutoCacheExclude']);
    }

    // Get some current information from the auto-cache log
    $data['autocachepages'] = array();
    $outputCacheDir = xarCoreGetVarDirPath() . '/cache/output';
    if (file_exists($outputCacheDir . '/autocache.log') &&
        filesize($outputCacheDir . '/autocache.log') > 0) {
        $logs = file($outputCacheDir . '/autocache.log');
        $data['autocachehits'] = array('HIT' => 0,
                                       'MISS' => 0);
        $autocacheproposed = array();
        foreach ($logs as $entry) {
            if (empty($entry)) continue;
            list($time,$status,$addr,$url) = explode(' ',$entry);
            $url = trim($url);
            if (!isset($start)) $start = $time;
            $end = $time;
            if (!isset($data['autocachepages'][$url])) {
                $data['autocachepages'][$url] = array();
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
        $data['autocachestart'] = $start;
        $data['autocacheend'] = $end;
        // check that all required URLs are included
        if (!empty($cachingConfiguration['AutoCache.Include'])) {
            foreach ($cachingConfiguration['AutoCache.Include'] as $url) {
                if (!isset($autocacheproposed[$url]) ||
                    $autocacheproposed[$url] < $cachingConfiguration['AutoCache.Threshold'])
                    $autocacheproposed[$url] = 99999999;
            }
        }
        // check that all forbidden URLs are excluded
        if (!empty($cachingConfiguration['AutoCache.Exclude'])) {
            foreach ($cachingConfiguration['AutoCache.Exclude'] as $url) {
                if (isset($autocacheproposed[$url])) unset($autocacheproposed[$url]);
            }
        }
        // sort descending by count
        arsort($autocacheproposed, SORT_NUMERIC);
        $data['autocacheproposed'] = array();
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
    //$data['pages'] = xarModAPIfunc('xarcachemanager', 'admin', 'getpages');
    $data['pages'] = array('todo' => 'something ?');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
