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

    if (!xarVarFetch('id', 'id', $id, 0, XARVAR_NOT_REQUIRED)) return;

    // Allow the optional pre-selected drop-downs to take precedence.
    xarVarFetch('name_list', 'pre:lower:ftoken:str:1:100', $name, '', XARVAR_NOT_REQUIRED);
    if (empty($name)) unset($name);

    if (!xarVarFetch('name', 'pre:lower:ftoken:str:1:100', $name)) return;

    if (!xarVarFetch('description', 'str:0:200', $description)) return;

    sys::import('modules.dynamicdata.class.properties.master');
    $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
    $isvalid = $accessproperty->checkInput('type_add_access');
    $info = array('add_access' => $accessproperty->getValue());

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }        

    // Pass to API
    if (!empty($id)) {
        if (!xarMod::apiFunc(
            'xarpages', 'admin', 'updatetype',
            array(
                'id'           => $id,
                'name'         => $name,
                'description'  => $description,
                'info'         => $info,
            )
        )) {return;}
    } else {
        // Pass to API
        $id = xarMod::apiFunc(
            'xarpages', 'admin', 'createtype',
            array(
                'name'         => $name,
                'description'  => $description,
                'info'         => $info,
            )
        );
        if (!$id) {return;}
    }

    xarResponse::Redirect(xarModUrl('xarpages', 'admin', 'viewtypes'));

    return true;
}

?>