<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * utility function pass individual menu items to the main menu
 *
 * @return array
 * @return array containing the menulinks for the main menu items.
 */
function uploads_adminapi_getmenulinks()
{
    return xarMod::apiFunc('base', 'admin', 'loadmenuarray', ['modname' => 'uploads', 'modtype' => 'admin']);
}
