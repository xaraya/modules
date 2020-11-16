<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Update the allowed HTML
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args['tags'] an array of the ids and allowed value of the html tags
 * @throws MISSING_DATA
 */
function html_admin_updateset()
{
    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Security Check
    if (!xarSecurity::check('AdminHTML')) {
        return;
    }

    // Get parameters from the input
    if (!xarVar::fetch('tags', 'array:1:', $tags)) {
        $msg = xarML('No HTML tags were selected.');
        throw new BadParameterException(null, $msg);
    }

    // Initialize array for config vars
    $allowedhtml = array();

    // Update HTML tags
    foreach ($tags as $id=>$allowed) {
        // Get the id of the htmltag
        $thistag = xarMod::apiFunc(
            'html',
            'user',
            'gettag',
            array('id' => $id)
        );

        if ($thistag) {
            $tag = $thistag['tag'];

            // Check if update is necessary
            if ($thistag['allowed'] != $allowed) {
                // Update
                if (!xarMod::apiFunc(
                    'html',
                    'admin',
                    'update',
                    array('id' => $id,
                                         'allowed' => $allowed)
                )) {
                    return false;
                }
            }

            // If this is an html tag, then
            // also update the config vars array
            if ($thistag['type'] == 'html') {
                $allowedhtml[$tag] = $allowed;
            }
        }
    }

    // Set config vars
    xarConfigVars::set(null, 'Site.Core.AllowableHTML', $allowedhtml);

    // Redirect back to set
    xarController::redirect(xarController::URL('html', 'admin', 'set'));

    return true;
}
