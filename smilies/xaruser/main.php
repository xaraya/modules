<?php
/**
 * File: $Id$
 * 
 * Xaraya Smilies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with smilies Menu information
 */
function smilies_user_main()
{
    // Security Check
	if(!xarSecurityCheck('OverviewSmilies')) return;
    // Get parameters from whatever input we need
    if(!xarVarFetch('startnum', 'isset',    $startnum, 1,     XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    // Specify some labels for display
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('smilies', 'user', 'countitems'),
                                    xarModURL('smilies', 'user', 'main', array('startnum' => '%%')),
                                    xarModGetVar('smilies', 'itemsperpage'));
    // The user API function is called
    $links = xarModAPIFunc('smilies',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('smilies',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no smilies registered');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Add the array of items to the template variables
    $data['items'] = $links;
    // Return the template variables defined in this function
    return $data;
}
?>