<?php
/*
 * File: $Id: $
 *
 * CHSF Content Navigation Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author Carl Corliss <Rabbitt : rabbitt@xaraya.com>
*/

/**
 * Update configuration
 *
 * @author Carl Corliss
 * @returns array
 * @return $data
 */
function navigator_admin_updateconfig()
{
//    if (!xarSecurityCheck('AdminNavigator')) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Get parameters from input
    if (!xarVarFetch('matrix',   'checkbox',    $matrix,        1)) return;
    if (!xarVarFetch('primary_cids',   'list:int:0:', $primary_cid,   0, XARVAR_GET_OR_POST, 0)) return;
    if (!xarVarFetch('secondary_cids', 'list:int:0:', $secondary_cid, 0, XARVAR_NOT_REQUIRED, 0)) return;
    if (!xarVarFetch('secdef_cids',    'list:int:0:', $secDef,        0, XARVAR_NOT_REQUIRED, 0)) return;
//    xarDerefData('$_POST', $_POST, TRUE);


    if (!isset($matrix) || empty($matrix)) {
        $secondary_cid = NULL;
    }

    if (!empty($primary_cid)) {
        if (!empty($secondary_cid)) {
            $rootcids = implode(';', array($primary_cid[0], $secondary_cid[0]));
        } else {
            $rootcids = $primary_cid[0];
        }
    } else {
        $rootcids = '';
    }

    if (isset($secDef[0])) {
        $secDef = $secDef[0];
    } else {
        $secDef = 0;
    }

    // die("ok - made it here...");
    xarModSetVar('navigator', 'style.matrix', (isset($matrix) && $matrix) ? 1 : 0);
    xarModSetVar('navigator', 'categories.roots', $rootcids);


    if (!empty($rootcids)) {
        $cids = explode(';', $rootcids);
        $primary_list = xarModAPIFunc('categories', 'user', 'getcat',
                                       array('cid' => $cids[0], 'getchildren' => TRUE));

        if (isset($cids[1])) {
            $secondary_list = xarModAPIFunc('categories', 'user', 'getcat',
                                             array('cid' => $cids[1], 'getchildren' => TRUE));
        } else {
            $secondary_list = array();
            $secDef = 0;
        }

        if (!is_array($primary_list)) {
            $primary_list = array();
        }

        xarModAPIFunc('navigator', 'user', 'nested_tree_create', array('tree' => &$primary_list));
        xarModAPIFunc('navigator', 'user', 'nested_tree_create', array('tree' => &$secondary_list));

        xarModSetVar('navigator', 'categories.list.primary',   serialize($primary_list));
        xarModSetVar('navigator', 'categories.list.secondary', serialize($secondary_list));
    }

    // xarDerefData('$primary_list', $primary_list);
    // xarDerefDatA('$secondary_list', $secondary_list, TRUE);

    // Set default parents
    xarModSetVar('navigator', 'categories.secondary.default', $secDef);

    // Redirect to modifyconfig template
    xarResponseRedirect(xarModURL('navigator', 'admin', 'modifyconfig'));

    // Return
    return TRUE;
}

?>
