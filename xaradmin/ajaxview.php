<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */

/**
 * view items
 */
function referer_admin_ajaxview()
{ 
    // Get parameters
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('sort', 'int:1:', $sort, '1', XARVAR_NOT_REQUIRED)) return; 
    // Initialise the variable that will hold the items
    $data['items'] = array(); 

    // Security Check
    if (!xarSecurityCheck('EditReferer')) return; 
    // The user API function is called.
    if ($sort == 1){
        $items = xarModAPIFunc('referer',
            'user',
            'getall',
            array('startnum' => $startnum,
                  'numitems' => xarModGetVar('referer', 'itemsperpage'))); 
        $data['sort'] = 1;
    } else {
        $items = xarModAPIFunc('referer',
            'user',
            'getallbytime',
            array('startnum' => $startnum,
                  'numitems' => xarModGetVar('referer', 'itemsperpage'))); 
        $data['sort'] = 2;
    }
    // Check for exceptions
    if (!isset($items)) return; // throw back
     
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['url']           = xarVarPrepForDisplay($item['url']);
        $items[$i]['frequency']     = xarVarPrepForDisplay($item['frequency']);
        $items[$i]['urldisplay']    = xarVarPrepForDisplay(substr($item['url'], 0, 60));
    } 
    // Add the array of items to the template variables
    $data['items'] = $items; 

    return $data;
}
?>