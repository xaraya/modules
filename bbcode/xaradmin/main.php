<?php
/**
 * File: $Id$
 * 
 * Xaraya BBCode
 * Based on pnBBCode Hook from larseneo
 * Converted to Xaraya by John Cox
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage BBCode Module
 * @author larseneo
*/
/**
 * Add a standard screen upon entry to the module.
 *
 * @public
 * @author John Cox 
 * @returns output
 * @return output with censor Menu information
 */
function bbcode_admin_main()
{
    // Security Check
	if(!xarSecurityCheck('EditBBCode')) return;
	if (xarModGetVar('adminpanels', 'overview') == 0) {
		// Return the output
		return array();
	} else {
		xarResponseRedirect(xarModURL('bbcode', 'admin', 'modifyconfig'));
	} 
	// success
	return true;
}
?>