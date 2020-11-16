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

    if (!xarVar::fetch('ptid', 'id', $ptid, 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Allow the optional pre-selected drop-downs to take precedence.
    xarVar::fetch('name_list', 'pre:lower:ftoken:str:1:100', $name, '', xarVar::NOT_REQUIRED);
    if (empty($name)) {
        unset($name);
    }

    if (!xarVar::fetch('name', 'pre:lower:ftoken:str:1:100', $name)) {
        return;
    }

    if (!xarVar::fetch('desc', 'str:0:200', $desc)) {
        return;
    }

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Pass to API
    if (!empty($ptid)) {
        if (!xarMod::apiFunc(
            'xarpages',
            'admin',
            'updatetype',
            array(
                'ptid'  => $ptid,
                'name'  => $name,
                'desc'  => $desc
            )
        )) {
            return;
        }
    } else {
        // Pass to API
        $ptid = xarMod::apiFunc(
            'xarpages',
            'admin',
            'createtype',
            array(
                'name'  => $name,
                'desc'  => $desc
            )
        );
        if (!$ptid) {
            return;
        }
    }

    xarController::redirect(xarController::URL('xarpages', 'admin', 'viewtypes'));

    return true;
}
