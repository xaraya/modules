<?php
/**
 * Display the entire menutree templated for the user side
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/eid/1162
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  
 */
function menutree_user_test() {
    // Check to see the current user has edit access to the menutree module
    if (!xarSecurityCheck('ReadMenuTree')) return;

    return xarTplModule('menutree','user','test');

}

?>