<?php
// File: $Id$
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */

/**
 * update a subsite
 */
function multisites_adminapi_update($args)
{
    // Get arguments
    extract($args);

    // Array of column set statements.
    $set = array();

    // String parameters.
    foreach(array('mssite', 'msprefix', 'msdb', 'msshare') as $parameter)
    {
        if (isset($$parameter))
        {
            $set[] = "xar_$parameter = '" . xarVarPrepForStore($$parameter) . "'";
        }
    }
  // Numeric parameters.
    foreach(array('msstatus'=>0) as $parameter => $default)
    {
        if (isset($$parameter))
        {
            if ($$parameter == "0" || $$parameter == "1")
            {
                $set[] = "xar_$parameter = " . xarVarPrepForStore($$parameter) . "";
            } else {
                $set[] = "xar_$parameter = " . xarVarPrepForStore($default) . "";
            }
        }
    }
    // Argument check
    if (!isset($msid) || empty($set)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Create the 'set' statement.
    $set = implode(', ', $set);

    // The user API function is called
    $subsite = xarModAPIFunc('multisites',
                             'user',
                             'get',
                          array('msid' => $msid));

    if ($subsite == false) {
        $msg = xarML('No Such Subsite Exists',
                    'multisites');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    if (!xarSecurityCheck('EditMultisites')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $multisitestable = $xartable['multisites'];

    // Update the subsite
    $query = 'UPDATE ' . $multisitestable . ' SET ' . $set
            . ' WHERE xar_msid = ' . xarVarPrepForStore($msid);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    // Let the calling process know that we have finished successfully
    return true;
}
?>
