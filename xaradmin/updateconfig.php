<?php
/**
 * Update configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function accessmethods_admin_updateconfig($args)
{
    extract($args);
    
    if (!xarVarFetch('webmastergroup',  'int::', $webmastergroup, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage',  'int::', $itemsperpage, false, XARVAR_NOT_REQUIRED)) return;
    
    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AdminAccessMethods')) return;

    
    xarModSetVar('accessmethods', 'webmastergroup', $webmastergroup);
    
    xarModSetVar('accessmethods', 'itemsperpage', $itemsperpage);
// TODO: fix xarResponseRedirect so that it doesn't exit anymore,
//       and move this below the update function itself again
    xarModCallHooks('module','updateconfig','accessmethods',
                    array('module' => 'accessmethods'));

//    if (!xarModFunc('dynamicdata','admin','update')) return; // throw back

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('accessmethods', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
