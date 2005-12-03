<?php
/**
 * File: $Id:
 *
 * Standard function to update a current text
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('bible','admin','modify') to update a current text
 *
 * @param  $ 'tid' the id of the item to be updated
 * @param  $ 'sname' short name of the text to be updated
 * @param  $ 'lname' long name of the text to be updated
 */
function bible_admin_update($args)
{
    extract($args);

    if (!xarVarFetch('tid', 'int:1:', $tid, $tid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, $sname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lname', 'str:1:', $lname, $lname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, xarServerGetVar('HTTP_REFERER'), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid)) $tid = $objectid;
    if (empty($return)) $return = '';

    if (!xarSecConfirmAuthKey()) return;

    $invalid = array();
    if (empty($sname) || !is_string($sname)) {
        $invalid['sname'] = 1;
        $sname = '';
    }
    if (empty($lname) || !is_string($lname)) {
        $invalid['lname'] = 1;
        $lname = '';
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_new function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('bible', 'admin', 'modify',
                          array('sname'     => $sname,
                                'lname'     => $lname,
                                'invalid'  => $invalid));
    }

    // call API function to do the updating
    if (!xarModAPIFunc('bible',
                       'admin',
                       'update',
                       array('tid'   => $tid,
                             'sname'   => $sname,
                             'lname' => $lname))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarML('Text was successfully updated!'));

    // now send to the page where we create the index
    if (empty($return)) $return = xarModURL('bible', 'admin', 'view');
    xarResponseRedirect($return);

    // Return
    return true;
}

?>
