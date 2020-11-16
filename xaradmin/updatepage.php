<?php

/**
 * File: $Id$
 *
 * Update or create a page - form handler.
 *
 * @package Xaraya
 * @copyright (C) 2004-2010 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_updatepage($args)
{
    extract($args);

    // Get parameters
    if (!xarVar::fetch('batch', 'bool', $batch, false, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('creating', 'bool', $creating)) {
        return;
    }

    if ($creating) {
        xarVar::fetch('ptid', 'id', $ptid, 0, xarVar::NOT_REQUIRED);
    } else {
        if (!xarVar::fetch('pid', 'id', $pid)) {
            return;
        }
    }

    if (!xarVar::fetch('name', 'pre:lower:ftoken:str:1:100', $name, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('desc', 'str:0:255', $desc)) {
        return;
    }
    if (!xarVar::fetch('theme', 'str:0:100', $theme)) {
        return;
    }

    if (!xarVar::fetch('template', 'str:0:100', $template_default)) {
        return;
    }
    if (!xarVar::fetch('template_select', 'str:1:100', $template, $template_default, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('page_template', 'str:0:100', $page_template_default)) {
        return;
    }
    if (!xarVar::fetch('page_template_select', 'str:1:100', $page_template, $page_template_default, xarVar::NOT_REQUIRED)) {
        return;
    }

    // The function/encode_url/decode_url come from form variables of
    // the same name, but may be over-ridden if any of *_select form
    // fields contain a value.
    if (!xarVar::fetch('function', 'str:0:100', $function_default)) {
        return;
    }
    if (!xarVar::fetch('function_select', 'str:1:100', $function, $function_default, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('encode_url', 'str:0:100', $encode_url_default)) {
        return;
    }
    if (!xarVar::fetch('encode_url_select', 'str:1:100', $encode_url, $encode_url_default, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('decode_url', 'str:0:100', $decode_url_default)) {
        return;
    }
    if (!xarVar::fetch('decode_url_select', 'str:1:100', $decode_url, $decode_url_default, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('alias', 'int:0:1', $alias, 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    if (!xarVar::fetch('return_url', 'str:0:200', $return_url, '', xarVar::DONT_SET)) {
        return;
    }

    // Validate the status against the list available.
    $statuses = xarMod::apiFunc('xarpages', 'user', 'getstatuses');
    if (!xarVar::fetch('status', 'pre:upper:enum:' . implode(':', array_keys($statuses)), $status, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Allow the admin to propagate the status to all child pages (when ACIVE or INACTIVE).
    if (!xarVar::fetch('status_recurse', 'bool', $status_recurse, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Bug 4495: ensure sensible defaults here, since these items may be suppressed in
    // the update form for some users.
    if (!xarVar::fetch('moving', 'bool', $moving, false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('movepage', 'bool', $movepage, false, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('refpid', 'pre:field:refpid:int:0', $refpid, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('position', 'enum:before:after:firstchild:lastchild', $position, 'before', xarVar::NOT_REQUIRED)) {
        return;
    }

    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Pass to API
    if (!$creating) {
        if (!xarMod::apiFunc(
            'xarpages',
            'admin',
            'updatepage',
            array(
                'pid'           => $pid,
                'name'          => $name,
                'desc'          => $desc,
                'template'      => $template,
                'page_template' => $page_template,
                'theme'         => $theme,
                'function'      => $function,
                'encode_url'    => $encode_url,
                'decode_url'    => $decode_url,
                'moving'        => ($movepage && $moving),
                'insertpoint'   => $refpid,
                'offset'        => $position,
                'alias'         => $alias,
                'status'        => $status,
                'status_recurse' => $status_recurse
            )
        )) {
            return;
        }
    } else {
        // Pass to API
        $pid = xarMod::apiFunc(
            'xarpages',
            'admin',
            'createpage',
            array(
                'name'          => $name,
                'desc'          => $desc,
                'template'      => $template,
                'page_template' => $page_template,
                'theme'         => $theme,
                'function'      => $function,
                'encode_url'    => $encode_url,
                'decode_url'    => $decode_url,
                'itemtype'      => $ptid,
                'insertpoint'   => $refpid,
                'offset'        => $position,
                'alias'         => $alias,
                'status'        => $status
            )
        );
        if (!$pid) {
            return;
        }
    }

    if ($creating) {
        if ($batch) {
            // If there are more to create, then go to the create page.
            xarController::redirect(
                xarController::URL(
                    'xarpages',
                    'admin',
                    'modifypage',
                    array(
                        'batch' => 1,
                        'creating' => 1,
                        'ptid' => $ptid,
                        'insertpoint' => $refpid,
                        'position' => $position
                    )
                )
            );
        } else {
            xarController::redirect(xarController::URL('xarpages', 'admin', 'modifypage', array('pid' => $pid)));
        }
    } else {
        if (!empty($return_url)) {
            xarController::redirect($return_url);
        } else {
            xarController::redirect(xarController::URL('xarpages', 'admin', 'viewpages'));
        }
    }

    return true;
}
