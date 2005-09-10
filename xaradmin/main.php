<?php
/**
 * File: $Id:
 * 
 * Search main administration function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search
 * @author Jo Dalle Nogare
 */
/**
 * The main administration function
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @return true
 *
 */
function search_admin_main()
{
    if (!xarSecurityCheck('AdminSearch')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0) {
        //no main search menu - comment out
        // $data = xarModAPIFunc('search', 'admin', 'menu');
        xarResponseRedirect(xarModURL('search', 'admin', 'overview'));
 
    } else {
        // If docs are turned off, then we just return the view page, or whatever
        // function seems to be the most fitting.
        xarResponseRedirect(xarModURL('search', 'admin', 'modifyconfig'));
    }
    // success
    return true;
} 

?>
