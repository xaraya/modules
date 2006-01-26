<?php
/**
 * Polls table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function polls_xartables()
{
    // Initialise table array
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();

    $polls = $prefix . '_polls';
    $xartable['polls'] = $polls;

    $pollsinfo = $prefix . '_polls_info';
    $xartable['polls_info'] = $pollsinfo;

    // Return the table information
    return $xartable;
}

?>