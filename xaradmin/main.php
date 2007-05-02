<?php

/**
 * File: $Id$
 *
 * Main admin function for xmlrpcserver
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage xmlrpcserver
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Main admin entry function
 *
*/
function xmlrpcserver_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminXmlRpcServer')) return;

    //return the output
    return array();
}
?>