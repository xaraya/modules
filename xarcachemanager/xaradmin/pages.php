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
    
        xarVarFetch('sessionless','isset',$sessionless,'',XARVAR_NOT_REQUIRED);
        $urllist = '';
        if (!empty($sessionless)) {
            $urls = preg_split('/\s+/',$sessionless,-1,PREG_SPLIT_NO_EMPTY);
            $baseurl = xarServerGetBaseURL();
            $checkurls = array();
            foreach ($urls as $url) {
                if (empty($url) || !strstr($url,$baseurl)) continue;
                $checkurls[] = $url;
            }
            if (count($checkurls) > 0) {
                $urllist = "'" . join("','",$checkurls) . "'";
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
   
        $cachegroups = join(';', $grouplist); 
        $cachingConfig = preg_replace('/\[\'Page.CacheGroups\'\]\s*=\s*(\'|\")(.*)\\1;/', "['Page.CacheGroups'] = '$cachegroups';", $cachingConfig);
    
        $cachingConfig = preg_replace('/\[\'Page.SessionLess\'\]\s*=\s*array\s*\((.*)\)\s*;/i', "['Page.SessionLess'] = array($urllist);", $cachingConfig);

        $fp = fopen ($cachingConfigFile, 'wb');
        fwrite ($fp, $cachingConfig);
        fclose ($fp);

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

    // Get some page caching configurations
    //$data['pages'] = xarModAPIfunc('xarcachemanager', 'admin', 'getpages');
    $data['pages'] = array('todo' => 'something ?');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
