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
    if (!xarSecurity::check('AdminCrispBB')) {
        return;
    }

    if (!xarVar::fetch('modid', 'isset', $modid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'isset', $itemtype, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'str:1:', $confirm, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    $now = time();

    $pageTitle = xarML('Delete Associations');

    $data = [];

    // Check for confirmation.
    if (empty($confirm)) {
        $data['modid'] = $modid;
        $data['itemtype'] = $itemtype;
        $data['itemid'] = $itemid;

        if (!empty($modid)) {
            $modinfo = xarMod::getInfo($modid);
            if (empty($itemtype)) {
                $data['modname'] = ucwords($modinfo['displayname']);
            } else {
                // Get the list of all item types for this module (if any)
                $mytypes = xarMod::apiFunc(
                    $modinfo['name'],
                    'user',
                    'getitemtypes',
                                         // don't throw an exception if this function doesn't exist
                                         [],
                    0
                );
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                } else {
                    $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                }
            }
        }
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSec::genAuthKey();
        $data['menulinks'] = xarMod::apiFunc(
            'crispbb',
            'admin',
            'getmenulinks',
            [
                'current_module' => 'crispbb',
                'current_type' => 'admin',
                'current_func' => 'unlinkhooks',
                'current_sublink' => '',
            ]
        );
        xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));
        // Return the template variables defined in this function
        return $data;
    }

    if (!xarSec::confirmAuthKey()) {
        return;
    }
    if (!xarMod::apiFunc(
        'crispbb',
        'admin',
        'unlinkhooks',
        ['modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'confirm' => $confirm, ]
    )) {
        return;
    }
    xarController::redirect(xarController::URL(
        'crispbb',
        'admin',
        'modifyhooks',
        ['modid' => $modid, 'itemtype' => $itemtype]
    ));
    return true;
}
