<?php
/**
 * File: $Id$
 * 
 * Polls table definitions function
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
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

    $polls = xarConfigGetVar('prefix') . '_polls';
    $xartable['polls'] = $polls;

    $pollsinfo = xarConfigGetVar('prefix') . '_polls_info';
    $xartable['polls_info'] = $pollsinfo;

    // Return the table information
    return $xartable;
}

?>
