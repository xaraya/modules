<?php
/**
 * Overview for xmlrpcserver
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @link http://xaraya.com/index.php/release/743.html
 * @author Marcel van der Boom <marcel@xaraya.com>
 */


/**
 * Overview displays standard Overview page
 */
function xmlrpcserver_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xmlrpcserver', 'admin', 'main', $data, 'main');
}

?>