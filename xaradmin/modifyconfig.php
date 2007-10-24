<?php
/*
 * File: $Id: $
 *
 * Navigator Configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author Carl Corliss <Rabbitt : rabbitt@xaraya.com>
*/

/**
 * Modify configuration
 *
 * @author Carl Corliss
 * @returns array
 * @return $data
 */
function navigator_admin_modifyconfig()
{
    // Security check
    if (!xarSecurityCheck('AdminNavigator')) return;

    $matrix     = xarModGetVar('navigator', 'style.matrix');
    $rootcids   = xarModGetVar('navigator', 'categories.roots');
    $secDef     = xarModGetVar('navigator', 'categories.secondary.default');

    if (!isset($secDef) || empty($secDef)) {
        $secDef = 0;
    }

    if (!isset($matrix) || empty($matrix)) {
        $data['matrix'] = 0;
    } else {
        $data['matrix'] = 1;
    }

    if (isset($rootcids) && !empty($rootcids)) {
        $cids = explode(';', $rootcids);
    }

    $priCid = (isset($cids[0])) ? $cids[0] : 0;
    $secCid = (isset($cids[1])) ? $cids[1] : 0;

    $selpri[$priCid]     = 1;
    $selsec[$secCid]     = 1;
    $selsecdef[$secDef]  = 1;

    $data['authid'] = xarSecGenAuthKey();
    $data['primarySelect'] =
            xarModAPIFunc('categories', 'visual', 'makeselect',
                           array('values' => $selpri,
                                 'name_prefix' => 'primary_',
                                 'maximum_depth' => 2,
                                 'javascript' => 'onchange="javascript:this.form.submit();"',
                                 'show_edit' => false));

    $data['secondarySelect'] =
            xarModAPIFunc('categories', 'visual', 'makeselect',
                           array('values' => $selsec,
                                 'name_prefix' => 'secondary_',
                                 'maximum_depth' => 2,
                                 'javascript' => 'onchange="javascript:this.form.submit();"',
                                 'show_edit' => false,
                                 'eid' => $priCid));

    if (!empty($secCid)) {
        $data['secDefSelect'] =
                xarModAPIFunc('categories', 'visual', 'makeselect',
                               array('values' => $selsecdef,
                                     'name_prefix' => 'secdef_',
                                     'show_edit' => false,
                                     'javascript' => 'onchange="javascript:this.form.submit();"',
                                     'cid' => $secCid));
    }

    $primary_list      = xarModGetVar('navigator', 'categories.list.primary');
    $secondary_list    = xarModGetVar('navigator', 'categories.list.secondary');
    $secondary_start   = xarModGetVar('navigator', 'categories.secondary.default');

    if (!empty($primary_list)) {
        $data['primary_list'] = @unserialize($primary_list);
    } else {
        $data['primary_list'] = array();
    }

    if (empty($secondary_start)) {
        $data['secondary_default_start'] = 0;
    } else {
        $data['secondary_default_start'] = $secondary_start;
    }

    if (!empty($secondary_list)) {
        $data['secondary_list'] = @unserialize($secondary_list);
        xarModAPIFunc('navigator', 'user', 'set_startpoint',
                       array('tree' => &$data['secondary_list'],
                             'startpoint' => $data['secondary_default_start']));
    } else {
        $data['secondary_list'] = array();
    }

    // Return the template variables defined in this function
    return $data;
}


?>
