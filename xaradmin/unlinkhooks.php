<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http//www.gnu.org/licenses/gpl.html}
 * @link http//www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http//xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Delete association of module items
 * @param modid
 * @param itemtype
 * @param itemid
 * @param string confirm
 * @return bool True on success of redirect
 */
function crispbb_admin_unlinkhooks()
{
    // Security Check
    if(!xarSecurityCheck('AdminCrispBB')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $pageTitle = xarML('Delete Associations');

    $data = array();

    // Check for confirmation.
    if (empty($confirm)) {
        $data['modid'] = $modid;
        $data['itemtype'] = $itemtype;
        $data['itemid'] = $itemid;

        if (!empty($modid)) {
            $modinfo = xarModGetInfo($modid);
            if (empty($itemtype)) {
                $data['modname'] = ucwords($modinfo['displayname']);
            } else {
                // Get the list of all item types for this module (if any)
                $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         array(), 0);
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                } else {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                }
            }
        }
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
            array(
                'current_module' => 'crispbb',
                'current_type' => 'admin',
                'current_func' => 'unlinkhooks',
                'current_sublink' => '',
            ));
        xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));
        // Return the template variables defined in this function
        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('crispbb','admin','unlinkhooks',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'confirm' => $confirm))) {
        return;
    }
    xarResponseRedirect(xarModURL('crispbb', 'admin', 'modifyhooks',
        array('modid' => $modid, 'itemtype' => $itemtype)));
    return true;
}

?>
