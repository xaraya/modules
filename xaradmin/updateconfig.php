<?php

/**
 * File: $Id$
 *
 * BlackList API 
 *
 * @package Modules
 * @copyright (C) 2002-2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BlackList
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/


/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * 
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @returns mixed output array, or string containing formated output
 */ 
function blacklist_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('BlackList-Admin')) return;

    if (!xarVarFetch('numitems', 'int', $numitems, 25, XARVAR_NOT_REQUIRED)) return;

    xarModSetVar('blacklist', 'paging.numitems', $numitems);
    xarResponseRedirect(xarModURL('blacklist', 'admin', 'modifyconfig'));
    return true;
}
?>
