<?php
/**
 * Search main administration function
 *
 * @package modules
 * @copyright (C) 2005-2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search
 * @author Jo Dalle Nogare
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
