<?php
/**
 * File: $Id:
 * 
 * Update configuration parameters of the module with information passed back by the modification form
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
function bible_admin_updateconfig()
{
    if (!xarVarFetch('admin_textsperpage', 'int', $admin_textsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('user_searchversesperpage', 'int', $user_searchversesperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('user_lookupversesperpage', 'int', $user_lookupversesperpage, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('textdir', 'str', $textdir, 'var/bible', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdb', 'checkbox', $altdb, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbtype', 'str', $altdbtype, 'mysql', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbhost', 'str', $altdbhost, 'localhost', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbname', 'str', $altdbname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbuname', 'str', $altdbuname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbpass', 'str', $altdbpass, '', XARVAR_NOT_REQUIRED)) return;

    // confirm auth code
    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.
    xarModSetVar('bible', 'admin_textsperpage', $admin_textsperpage);
    xarModSetVar('bible', 'user_searchversesperpage', $user_searchversesperpage);
    xarModSetVar('bible', 'user_lookupversesperpage', $user_lookupversesperpage);
    xarModSetVar('bible', 'admin_textsperpage', $admin_textsperpage);
    xarModSetVar('bible', 'textdir', preg_replace("/\/\$/", '', $textdir));
    xarModSetVar('bible', 'SupportShortURLs', $shorturls);
    xarModSetVar('bible', 'altdb', $altdb);
    xarModSetVar('bible', 'altdbtype', $altdbtype);
    xarModSetVar('bible', 'altdbhost', $altdbhost);
    xarModSetVar('bible', 'altdbname', $altdbname);
    xarModSetVar('bible', 'altdbuname', $altdbuname);
    xarModSetVar('bible', 'altdbpass', $altdbpass);

    xarModCallHooks('module','updateconfig','bible',
                   array('module' => 'bible'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('bible', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
