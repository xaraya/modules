<?php

/**
 * File: $Id$
 *
 * Main admin gui function for bloggerapi
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Main admin entry function
 *
*/
function bloggerapi_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminBloggerAPI')) return;
        xarResponseRedirect(xarModURL('bloggerapi', 'admin', 'modifyconfig'));
    // success
    return true;
}
?>