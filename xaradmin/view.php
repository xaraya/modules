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
function referer_admin_view()
{ 
    // Get parameters
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('sort', 'int:1:', $sort, '1', XARVAR_NOT_REQUIRED)) return; 
    // Initialise the variable that will hold the items
    $data['items'] = array(); 
    // Specify some labels for display
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Referer URL'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Frequency')); 
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('referer', 'user', 'countitems'),
        xarModURL('referer', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('referer', 'itemsperpage')); 
    // Security Check
    if (!xarSecurityCheck('EditReferer')) return; 
    // The user API function is called.
    if ($sort == 1){
        $items = xarModAPIFunc('referer',
            'user',
            'getall',
            array('startnum' => $startnum,
                'numitems' => xarModGetVar('referer',
                    'itemsperpage'))); 
        $data['sort'] = 1;
    } else {
        $items = xarModAPIFunc('referer',
            'user',
            'getallbytime',
            array('startnum' => $startnum,
                'numitems' => xarModGetVar('referer',
                    'itemsperpage'))); 
        $data['sort'] = 2;
    }
    // Check for exceptions
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
     
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['url']           = xarVarPrepForDisplay($item['url']);
        $items[$i]['frequency']     = xarVarPrepForDisplay($item['frequency']);
        $items[$i]['urldisplay']    = xarVarPrepForDisplay(substr($item['url'], 0, 60));
    } 
    // Add the array of items to the template variables
    $data['items'] = $items; 
    // substr($url,0,strpos($url,'/',strpos($url,'/',strpos($url,'/')+1)+1));  :-)))

    // Generate a one-time authorisation code for this operation
    $authid = xarSecGenAuthKey();
    $data['javascript'] = "return xar_base_confirmLink(this, '" . xarML('Delete all referer data') . " ?')";
    $data['deleteurl'] = xarModUrl('referer', 'admin', 'delete', array('authid' => $authid));
    return $data;
}
?>