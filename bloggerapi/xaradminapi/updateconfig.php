<?php

/**
 * File: $Id$
 *
 * Update configuration of bloggerapi
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * update bloggerapi configuration
 */
function bloggerapi_adminapi_updateconfig()
{

    // TODO: define & process config vars for bloggerapi

    if(!xarVarFetch('sitename',        'isset', $sitename,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('slogan',          'isset', $slogan,           NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('metakeywords',    'isset', $metakeywords,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('metadescription', 'isset', $metadescription,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('adminmail',       'isset', $adminmail,        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('adminname',       'isset', $adminname,        NULL, XARVAR_DONT_SET)) {return;}


    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new item');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                        new SystemException($msg));
        return;
    }

    xarResponseRedirect(xarModURL('bloggerapi', 'admin', 'main'));

    return true;
}
?>