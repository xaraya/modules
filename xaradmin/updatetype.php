<?php

/**
 * File: $Id$
 *
 * Create or update a page type - form handler.
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_updatetype($args)
{
    extract($args);

    if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_NOT_REQUIRED)) return;

    // Allow the optional pre-selected drop-downs to take precedence.
    xarVarFetch('name_list', 'pre:lower:ftoken:str:1:100', $name, '', XARVAR_NOT_REQUIRED);
    if (empty($name)) unset($name);

    if (!xarVarFetch('name', 'pre:lower:ftoken:str:1:100', $name)) return;

    if (!xarVarFetch('desc', 'str:0:200', $desc)) return;

    sys::import('modules.dynamicdata.class.properties.master');
    $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
    $isvalid = $accessproperty->checkInput('type_add_access');
    $info = serialize(array('add_access' => $accessproperty->getValue()));

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {return;}

    // Pass to API
    if (!empty($ptid)) {
        if (!xarModAPIFunc(
            'xarpages', 'admin', 'updatetype',
            array(
                'id'           => $ptid,
                'name'         => $name,
                'description'  => $desc,
                'info'         => $info,
            )
        )) {return;}
    } else {
        // Pass to API
        $ptid = xarModAPIFunc(
            'xarpages', 'admin', 'createtype',
            array(
                'name'         => $name,
                'description'  => $desc,
                'info'         => $info,
            )
        );
        if (!$ptid) {return;}
    }

    xarResponse::Redirect(xarModUrl('xarpages', 'admin', 'viewtypes'));

    return true;
}

?>