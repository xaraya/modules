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
 * Displays the list of blacklist domain patterns, allowing the
 * administrator the option of editing/adding/deleting entries
 * 
 * @access public
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @returns mixed output array, or string containing formated output
 */ 
function blacklist_admin_view()
{

    // Security Check
    if(!xarSecurityCheck('BlackList-Admin'))
        return;

    return array();
}
?>
