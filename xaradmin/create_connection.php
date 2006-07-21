<?php
/**
 * Reports module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage reports
 * @link http://xaraya.com/index.php/release/4704.html
 * @author Marcel van der Boom <marcel@hsdev.com>
 */
/**
 * Gather entered info and let admin api process creation of new connection
 */
function reports_admin_create_connection($args)
{
    list($conn_name, $conn_desc,$conn_type,$conn_server,$conn_database,$conn_user,$conn_password) =
        xarVarCleanFromInput('conn_name','conn_desc','conn_type','conn_server','conn_database','conn_user','conn_password');
    extract($args);

    // Only desc, user and password may be empty, rest must have values

    // Confirm authorization key
    if (!xarSecConfirmAuthKey()) {
        // TODO: exception?
        return false;
    }

    if (!xarModAPIFunc('reports','admin','create_connection',array('conn_name'=>$conn_name,'conn_desc'=>$conn_desc,
                                                                   'conn_type'=>$conn_type,'conn_server'=>$conn_server,
                                                                   'conn_database'=>$conn_database,'conn_user'=>$conn_user,
                                                                   'conn_password'=>$conn_password))) {
        // Create failed
        // TODO: exception
        xarSessionSetVar('errormsg', xarML("Report creation failed"));
    }

    // Redisplay the connection screen (thus showing the newly added connection
    xarResponseRedirect(xarModUrl('reports','admin','view_connections',array()));
    return true;
}

?>