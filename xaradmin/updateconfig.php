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
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authsql_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('sqldbhost', 'str:1:', $sqldbhost, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbport', 'str:1:', $sqldbport, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbtype', 'str:1:', $sqldbtype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbname', 'str:1:', $sqldbname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbuser', 'str:1:', $sqldbuser, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbpass', 'str:1:', $sqldbpass, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbpasswordtablename', 'str:1:', $sqldbpasswordtablename, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbusernamefield',     'str:1:', $sqldbusernamefield,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbpasswordfield',     'str:1:', $sqldbpasswordfield,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqldbpasswordencryptionmethod', 'str:1:', $sqldbpasswordencryptionmethod, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sqlwhere',      'str:1:',   $sqlwhere,      '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultgroup',  'str:1:',   $defaultgroup,  '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('activate',      'checkbox', $activate,      false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adduser',       'checkbox', $adduser,       false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('storepassword', 'checkbox', $storepassword, false, XARVAR_NOT_REQUIRED)) return;


    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Update the authsql settings
    xarModSetVar('authsql', 'sqldbhost', $sqldbhost);
    xarModSetVar('authsql', 'sqldbport', $sqldbport);
    xarModSetVar('authsql', 'sqldbtype', $sqldbtype);
    xarModSetVar('authsql', 'sqldbname', $sqldbname);
    xarModSetVar('authsql', 'sqldbuser', $sqldbuser);
    xarModSetVar('authsql', 'sqldbpass', $sqldbpass);
    xarModSetVar('authsql', 'sqldbpasswordtablename', $sqldbpasswordtablename);
    xarModSetVar('authsql', 'sqldbusernamefield', $sqldbusernamefield);
    xarModSetVar('authsql', 'sqldbpasswordfield', $sqldbpasswordfield);
    xarModSetVar('authsql', 'sqldbpasswordencryptionmethod', $sqldbpasswordencryptionmethod);
    xarModSetVar('authsql', 'sqlwhere', $sqlwhere);

    if(!$adduser) {
        xarModSetVar('authsql', 'add_user', 'false');
    } else {
        xarModSetVar('authsql', 'add_user', 'true');
    }

    if(!$storepassword) {
        xarModSetVar('authsql', 'store_user_password', 'false');
    } else {
        xarModSetVar('authsql', 'store_user_password', 'true');
    }

    // Get default users group
    if (!isset($defaultgroup)) {
        // See if Users role exists
        if( xarFindRole("Users")) {
            $defaultgroup = 'Users';
        }
    } 
    xarModSetVar('authsql', 'defaultgroup', $defaultgroup);

    $authmodules = xarConfigGetVar('Site.User.AuthenticationModules');
    if (empty($activate) && in_array('authsql', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module != 'authsql') {
                $newauth[] = $module;
            }
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    } elseif (!empty($activate) && !in_array('authsql', $authmodules)) {
        $newauth = array();
        foreach ($authmodules as $module) {
            if ($module == 'authsystem') {
                $newauth[] = 'authsql';
            }
            $newauth[] = $module;
        }
        xarConfigSetVar('Site.User.AuthenticationModules', $newauth);
    }

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authsql', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>