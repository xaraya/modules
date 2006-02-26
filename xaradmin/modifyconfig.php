<?php
/**
 * Modify the configuration of Julian
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Developers Team
 */
/**
 * Function that allows modification of configurable variables for the julian calendar
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author Michelv <MichelV@xarayahosting.nl>
 * @return array data for template
 */

function julian_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminJulian')) {
        return;
    }

    $data=array();
    $data['share_group'] = xarModGetVar('julian','share_group');
    $data['dateformat']  = xarModGetVar('julian','dateformat');
    //$data['timeform']  = xarModGetVar('julian','timeform');
    // The form of the bullet in lists
    $data['BulletForm'] = xarModGetVar('julian', 'BulletForm');
    // Number of items per page
    $data['numitems']   = xarModGetVar('julian','numitems');
    // Standards
    $data['from_name']  = xarModGetVar('julian','from_name');
    $data['from_email'] = xarModGetVar('julian','from_email');
    // Duration minute interval
    $data['DurMinInterval'] = xarModGetVar('julian', 'DurMinInterval');
    // Starttime minute interval
    $data['StartMinInterval'] = xarModGetVar('julian', 'StartMinInterval');

    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('julian', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('julian','aliasname');

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Call hooks
    $hooks = xarModCallHooks('module', 'modifyconfig', 'julian', array('module' => 'julian'));
    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }
    return $data;
}

?>
