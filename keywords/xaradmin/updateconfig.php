<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

/**
 * Update configuration
 */
function keywords_admin_updateconfig()
{ 
    
    // Get parameters
    xarVarFetch('restricted','isset',$restricted,'', XARVAR_DONT_SET);
    xarVarFetch('keywords','isset',$keywords,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);
    xarVarFetch('delimiters','isset',$delimiters,'', XARVAR_DONT_SET);

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 

    if (!xarSecurityCheck('AdminKeywords')) return; 

    if (isset($restricted)) {
        xarModSetVar('keywords','restricted',$restricted);

    if (isset($keywords) && is_array($keywords)) {

    xarModAPIFunc('keywords',
                  'admin',
                  'resetlimited');

    foreach ($keywords as $modname => $value) {
        if ($modname == 'default') {
            $moduleid='0';
        } else {
            $moduleid = xarModGetIDFromName($modname,'module');
            }
         if ($value <> '') {
             xarModAPIFunc('keywords',
                           'admin',
                           'limited',
                            array('moduleid' => $moduleid,
                                  'keyword'  => $value));
            } 
        } 
    } 
    }

    if (empty($isalias)) {
        xarModSetVar('keywords','SupportShortURLs',0);
    } else {
        xarModSetVar('keywords','SupportShortURLs',1);
    }
    if (isset($delimiters)) {
        xarModSetVar('keywords','delimiters',trim($delimiters));
    }

    xarResponseRedirect(xarModURL('keywords', 'admin', 'modifyconfig'));

    return true;
}

?>
