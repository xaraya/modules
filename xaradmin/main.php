<?php
/**
 * Search main administration function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * The main administration function
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @return bool true
 *
 */
function search_admin_main()
{
    if (!xarSecurityCheck('AdminSearch')) return;

    // If docs are turned off, then we just return the view page, or whatever
    // function seems to be the most fitting.
    xarResponseRedirect(xarModURL('search', 'admin', 'modifyconfig'));
    // success
    return true;
}

?>
