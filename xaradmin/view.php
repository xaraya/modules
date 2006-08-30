<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
   View items

   @param $itemtype - type of item that is being viewed (required)
   @param $startnum - id of item to start the page with (optional)
   @return array data used in a template
*/
function helpdesk_admin_view()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if( !Security::check(SECURITY_ADMIN, 'helpdesk') ){ return false; }

    // Get Vars
    xarVarFetch('itemtype', 'int', $itemtype,  10, XARVAR_NOT_REQUIRED);
    xarVarFetch('startnum', 'int', $data['startnum'],  NULL, XARVAR_NOT_REQUIRED);
    $data['itemsperpage'] = xarModGetVar('helpdesk','itemsperpage');
    $data['itemtype'] = $itemtype;

    if (empty($data['itemtype'])){
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'view', 'helpdesk');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // The Generic helpdesk Menu
    $data['menu']      = xarModFunc('helpdesk','admin','menu');
    $data['menutitle'] = xarModAPIFunc('helpdesk','admin','menu');

    // Return the template variables defined in this function
    return $data;
}
?>