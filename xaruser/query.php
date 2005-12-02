<?php
/**
 * File: $Id:
 * 
 * Evaluate a query and direct to appropriate function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
function bible_user_query()
{
    // grab passed variables
    if (!xarVarFetch('query', 'str:0:', $query, $query, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, $sname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastlimits', 'str', $lastlimits, '', XARVAR_NOT_REQUIRED)) return;

    // decode parameters
    if (!empty($query)) $query = urldecode($query);

    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    // figure out what kind of request it is for
    $function = xarModAPIFunc('bible', 'user', 'getquerytype',
                              array('sname' => $sname,
                                    'query' => $query));

    // redirect to the appropriate function
    $args = array();
    if (!empty($sname)) $args['sname'] = $sname;
    if (!empty($query)) $args['query'] = $query;
    if (!empty($lastlimits)) $args['lastlimits'] = $lastlimits;
    xarResponseRedirect(xarModURL('bible', 'user', $function, $args));

    return true;


} 

?>
