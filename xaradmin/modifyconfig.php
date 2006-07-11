<?php
/**
 * AuthSQL Administrative Display Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSQL Module
 * @link http://xaraya.com/index.php/release/10512.html
 * @author Roger Keays and James Cooper
*/

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function authsql_admin_modifyconfig()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthSQL')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    $data['sqldbhostvalue'] = xarModGetVar('authsql','sqldbhost');
    $data['sqldbportvalue'] = xarModGetVar('authsql','sqldbport');
    $data['sqldbtypevalue'] = xarModGetVar('authsql','sqldbtype');
    $data['sqldbnamevalue'] = xarModGetVar('authsql','sqldbname');
    $data['sqldbuservalue'] = xarModGetVar('authsql','sqldbuser');
    $data['sqldbpassvalue'] = xarModGetVar('authsql','sqldbpass');
    $data['sqldbpasswordtablenamevalue'] = xarModGetVar('authsql','sqldbpasswordtablename');
    $data['sqldbusernamefieldvalue'] = xarModGetVar('authsql','sqldbusernamefield');
    $data['sqldbpasswordfieldvalue'] = xarModGetVar('authsql','sqldbpasswordfield');
    $data['sqldbpasswordencryptionmethodvalue'] = xarModGetVar('authsql','sqldbpasswordencryptionmethod');
    $data['sqlwherevalue'] = xarModGetVar('authsql','sqlwhere');

    // Add encryption methods
    $sqldbpasswordencryptionmethods[0]['name'] = xarVarPrepForDisplay("");
    $sqldbpasswordencryptionmethods[1]['name'] = xarVarPrepForDisplay("md5");
    $sqldbpasswordencryptionmethods[2]['name'] = xarVarPrepForDisplay("crypt");
    $sqldbpasswordencryptionmethods[3]['name'] = xarVarPrepForDisplay("encrypt");
    $sqldbpasswordencryptionmethods[4]['name'] = xarVarPrepForDisplay("decrypt");
    $data['sqldbpasswordencryptionmethods'] = $sqldbpasswordencryptionmethods;

    // Add user to xar_roles
    if (xarModGetVar('authsql','add_user') == 'true') {    
        $data['adduservalue'] = xarVarPrepForDisplay('checked="checked"');
    } else {
        $data['adduservalue'] = "";
    }
    
    // Store user's SQL password in Xaraya database?
    if (xarModGetVar('authsql','store_user_password') == 'true') {    
        $data['storepasswordvalue'] = xarVarPrepForDisplay('checked="checked"');
    } else {
        $data['storepasswordvalue'] = "";
    }
    
    // Get groups
    $data['defaultgroup'] = xarModGetVar('authsql', 'defaultgroup');

    // Get default users group
    if (!isset($data['defaultgroup'])) {
        // See if Users role exists
        if( xarFindRole("Users"))
            $data['defaultgroup'] = 'Users';
    } 

    // Get the list of groups
    if (!$groupRoles = xarGetGroups()) {
        return; // throw back
    }

    $i = 0;
    while (list($key,$group) = each($groupRoles)) {
        $groups[$i]['name'] = xarVarPrepForDisplay($group['name']);
        $i++;
    }
    $data['groups'] = $groups;

    // Submit button
    $data['submitbutton'] = xarVarPrepForDisplay(xarML('Submit'));
       
    // everything else happens in Template for now
    return $data;
}

?>