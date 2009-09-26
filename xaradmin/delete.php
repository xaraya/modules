<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * Delete hit counts of module items
 * @param int modid
 * @param int itemtype
 * @param int itemid
 * @param str confirm When empty the confirmation page is shown
 * @return bool True on success of deletion
 */
function hitcount_admin_delete()
{
    // Security Check
    if(!xarSecurityCheck('AdminHitcount')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Check for confirmation.
    if (empty($confirm)) {
        $data = array();
        $data['modid'] = $modid;
        $data['itemtype'] = $itemtype;
        $data['itemid'] = $itemid;

        $what = '';
        if (!empty($modid)) {
            $modinfo = xarModGetInfo($modid);
            if (empty($itemtype)) {
                $data['modname'] = ucwords($modinfo['displayname']);
            } else {
                // Get the list of all item types for this module (if any)
                $mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                } else {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                }
            }
        }
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        // Return the template variables defined in this function
        return $data;
    }

    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }        
    if (!xarMod::apiFunc('hitcount','admin','delete',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'confirm' => $confirm))) {
        return;
    }
    xarResponse::Redirect(xarModURL('hitcount', 'admin', 'view'));
    return true;
}

?>