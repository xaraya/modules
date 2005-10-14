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
 * This is a standard function to modify the configuration parameters of the
 * module
 * 
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @returns mixed output array, or string containing formated output
 */ 
function blacklist_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('BlackList-Admin')) {
	    return;
    }

	$numitems = xarModGetVar('blacklist', 'paging.numitems');
	if (empty($numitems)) {
		xarModSetVar('blacklist', 'paging.numitems', 25);
	}

    $output['authid'] = xarSecGenAuthKey();
    return $output;
}
?>
