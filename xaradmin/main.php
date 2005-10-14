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
 * Displays the overview menu if adminpanels.overview is 
 * turned on otherwise, it displays the blacklist editing page
 * 
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @returns mixed output array, or string containing formated output
 */ 
function blacklist_admin_main()
{
    if(!xarSecurityCheck('BlackList-Admin')){
        return;
    }
	
    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0){
        return array();
    } else {
		xarResponseRedirect(xarModURL('blacklist', 'admin', 'view'));
    }
    // success
    return true;
}
?>
