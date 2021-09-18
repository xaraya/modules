<?php
/**
 * Site Tools Template Cache Management
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 *
 * View Cache Files
 * @param  $ 'action' action taken on cache file
 * @param $ 'confirm' confirm action on delete
 */
function sitetools_admin_cacheview($args)
{
    /* Get parameters from whatever input we need. */
    if (!xarVar::fetch('action', 'str:1', $action, false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('hashn', 'str:1:', $hashn, false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('templn', 'str:1:', $templn, false, xarVar::NOT_REQUIRED)) {
        return;
    }

    /* Security check - important to do this as early as possible */
    if (!xarSecurity::check('AdminSiteTools')) {
        return;
    }

    $cachedir  = xarModVars::get('sitetools', 'templcachepath');
    $cachefile = xarModVars::get('sitetools', 'templcachepath').'/CACHEKEYS';
    $scriptcache=xarModVars::get('sitetools', 'templcachepath').'/d4609360b2e77516aabf27c1f468ee33.php';
    $data=[];
    $data['popup']=false;
    /* Check for confirmation. */
    $data['authid'] = xarSec::genAuthKey();
    if (empty($action)) {
        /* No action set yet - display cache file list and await action */
        $data['showfiles']=false;
        /* Generate a one-time authorisation code for this operation */
        $data['items']='';
        $cachelist=[];
        $cachenames=[];

        /* put all the names of the templates and hashed cache file into an array */
        umask();
        $count=0;
        $cachekeyfile=file($cachefile);
        $fd = fopen($cachefile, 'r');
        while ([$line_num, $line] = each($cachekeyfile)) {
            $cachelist[]=[explode(": ", $line)];
            ++$count;
        }
        $data['count']=$count;
        fclose($fd);

        /* generate all the URLS for cache file list */
        foreach ($cachelist as $hashname) {
            foreach ($hashname as $filen) {
                $hashn=htmlspecialchars($filen[0]);
                $templn=htmlspecialchars($filen[1]);
                $fullnurl=xarController::URL(
                    'sitetools',
                    'admin',
                    'cacheview',
                    ['action'=>'show','templn'=>$templn,'hashn'=>$hashn]
                );
                $cachenames[$hashn]=['hashn'=>$hashn,
                                   'templn'=>$templn,
                                   'fullnurl'=>$fullnurl, ];
            }
        }
        /*      var=$scriptcache;
               if ($var == true) unlink $scriptcache;
        */
        asort($cachenames);
        $data['items']=$cachenames;

        /* Return the template variables defined in this function */
        return $data;
    } elseif ($action=='show') {
        $data['showfiles']= true;
        $hashfile=$cachedir.'/'.$hashn.'.php';
        $newfile=[];
        $filetxt=[];
        $newfile = file($hashfile);
        $i=0;
        foreach ($newfile as $line_num => $line) {
            ++$i;
            $filetxt[]=['lineno' =>(int)$i,
                          'linetxt'=>htmlspecialchars($line), ];
        }
        $data['templn']=$templn;
        $data['hashfile']=$hashfile;
        $data['items']=$filetxt;
        return $data;
    }

    xarResponse::Redirect(xarController::URL('sitetools', 'admin', 'cacheview'));
    /*  Return */
    return true;
}
