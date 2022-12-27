<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * Delete ratings of module items
 */
function ratings_admin_delete()
{
    // Security Check
    if (!xarSecurity::check('DeleteRatings')) {
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

    // Check for confirmation.
    if (empty($confirm)) {
        $data = [];
        $data['modid'] = $modid;
        $data['itemtype'] = $itemtype;
        $data['itemid'] = $itemid;

        $what = '';
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
        // Return the template variables defined in this function
        return $data;
    }

    if (!xarSec::confirmAuthKey()) {
        return;
    }
    if (!xarMod::apiFunc(
        'ratings',
        'admin',
        'delete',
        ['modid' => $modid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid,
                             'confirm' => $confirm, ]
    )) {
        return;
    }
    xarController::redirect(xarController::URL('ratings', 'admin', 'view'));
    return true;
}
