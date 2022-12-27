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
 * Update configuration
 */
function ratings_admin_updateconfig()
{
    // Get parameters
    if (!xarVar::fetch('ratingsstyle', 'array', $ratingsstyle, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('seclevel', 'array', $seclevel, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('shownum', 'array', $shownum, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        return;
    }
    // Security Check
    if (!xarSecurity::check('AdminRatings')) {
        return;
    }

    $settings = ['default'];

    $hookedmodules = xarMod::apiFunc(
        'modules',
        'admin',
        'gethookedmodules',
        ['hookModName' => 'ratings']
    );

    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarMod::apiFunc(
                    $modname,
                    'user',
                    'getitemtypes',
                    // don't throw an exception if this function doesn't exist
                    [],
                    0
                );
                foreach ($value as $itemtype => $val) {
                    $settings[] = "$modname.$itemtype";
                }
            } else {
                $settings[] = $modname;
            }
        }
    }

    foreach ($settings as $modname) {
        if ($modname == 'default') {
            if (isset($ratingsstyle['default'])) {
                xarModVars::set('ratings', 'defaultratingsstyle', $ratingsstyle['default']);
            }
            if (isset($seclevel['default'])) {
                xarModVars::set('ratings', 'seclevel', $seclevel['default']);
            }
            if (!isset($shownum['default']) || $shownum['default'] != 1) {
                xarModVars::set('ratings', 'shownum', 0);
            } else {
                xarModVars::set('ratings', 'shownum', 1);
            }
        } else {
            if (isset($ratingsstyle[$modname])) {
                xarModVars::set('ratings', "ratingsstyle.$modname", $ratingsstyle[$modname]);
            }
            if (isset($seclevel[$modname])) {
                xarModVars::set('ratings', "seclevel.$modname", $seclevel[$modname]);
            }
            if (!isset($shownum[$modname]) || $shownum[$modname] != 1) {
                xarModVars::set('ratings', "shownum.$modname", 0);
            } else {
                xarModVars::set('ratings', "shownum.$modname", 1);
            }
        }
    }

    xarController::redirect(xarController::URL('ratings', 'admin', 'modifyconfig'));

    return true;
}
