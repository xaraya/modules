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
 * The standard overview screen on entry to the Multisite module.
 * @ returns output
 * @return output with Multisite Overview and Menu information
 */
function multisites_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminMultisites')) {

    }
   // we only really need to show the default view (overview in this case)
     if (xarModGetVar('adminpanels', 'overview') == 0) {
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('multisites', 'admin', 'modifyconfig'));
    }
   // success
    return true;
}
?>
