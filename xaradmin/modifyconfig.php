<?php
/**
 * Window Module Modify Configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window Module
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Window Module Development Team
 */
 
/*
 * Modify the general configuration settings in Window module
 */
function window_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminWindow')) return;

    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    switch ($data['tab']) {
        case 'general':
            break;
        case 'display':
            // this is sort of stupid, but I don't think there is a better way at present
            $windowid=xarModGetIDFromName('window');
            $info = xarModGetInfo($windowid);
            $urls = xarModAPIFunc('window','user','getall',array('status' => 1)); // get all active URLS
            if (is_array($urls)) {
                $data['urllist']= $urls;
            } else {
                $data['urllist']= '';
            }
            if (!xarVarFetch('showusermenu','int', $data['showusermenu'],$info['usercapable'],XARVAR_NOT_REQUIRED)) return;
            
            $data['showusermenu'] =$info['usercapable'];

            break;
        default:
            break;
    }

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}
?>
