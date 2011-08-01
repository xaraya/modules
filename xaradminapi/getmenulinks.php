<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
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
    return xarMod::apiFunc('base','admin','loadmenuarray',array('modname' => 'uploads', 'modtype' => 'admin'));
}
?>
