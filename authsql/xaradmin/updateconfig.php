<?php
/**
 * File: $Id: updateconfig.php,v 1.2 2003/12/17 04:00:51 roger Exp $
 *
 * AuthSQL Administrative Display Functions
 * 
 * @copyright (C) 2003 ninthave
 * @author James Cooper jbt_cooper@bigpond.com
*/

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function authsql_admin_updateconfig()
{
    // Get parameters
    list($sqldbhost,
         $sqldbport,
         $sqldbtype,
         $sqldbname,
         $sqldbuser,
         $sqldbpass,
         $sqldbpasswordtablename,
         $sqldbusernamefield,
         $sqldbpasswordfield,
         $sqldbpasswordencryptionmethod,
         $sqlwhere,
         $adduser,
         $storepassword,
         $defaultgroup ) = xarVarCleanFromInput('sqldbhost',
                                                'sqldbport',
                                                'sqldbtype',
                                                'sqldbname',
                                                'sqldbuser',
                                                'sqldbpass',
                                                'sqldbpasswordtablename',
                                                'sqldbusernamefield',
                                                'sqldbpasswordfield',
                                                'sqldbpasswordencryptionmethod',
                                                'sqlwhere',
                                                'adduser', 
                                                'storepassword', 
                                                'defaultgroup');

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

    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('authsql', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
