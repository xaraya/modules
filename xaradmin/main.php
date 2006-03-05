<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Add a standard screen upon entry to the module.
 * @return array Empty array
 */
function stats_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminStats')) return;
    return array();
//        xarResponseRedirect(xarModURL('stats', 'admin', 'modifyconfig'));
}

?>