<?php

/**
 * File: $Id$
 *
 * main admin function for bkview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * the main administration function
 */
function bkview_admin_main()
{
    // Security check, in admin at least edit is necessary
    if (!xarSecurityCheck('AdminAllRepositories')) return;
    
    $data['welcome'] = xarML('Welcome to the administration part of the bkview module...');
    $data['pageinfo']= xarML('BkView overview');
    return $data;
}

?>