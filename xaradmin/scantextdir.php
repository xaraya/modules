<?php
/**
 * File: $Id:
 *
 * Scan Bible text directory and update list of texts
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
 * scan directory and update text states
 *
 * @author curtisdf
 * @returns array
 * @return list of texts
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_admin_scantextdir()
{
    // security check
    if (!xarSecurityCheck('EditBible', 1)) return;

    // let API function do the scanning
    if (!xarModAPIFunc('bible', 'admin', 'scantextdir')) return;

    // set status message and redirect
    xarSessionSetVar('statusmsg', xarML('Text scan successfully completed!'));
    xarResponseRedirect(xarModURL('bible', 'admin', 'view'));

    return true;
}

?>
