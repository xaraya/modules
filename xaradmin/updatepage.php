<?php

/**
 * File: $Id$
 *
 * Update or create a page - form handler.
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_updatepage($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('batch', 'bool', $batch, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('creating', 'bool', $creating)) return;

    if ($creating) {
        xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_NOT_REQUIRED);
    } else {
        if (!xarVarFetch('pid', 'id', $pid)) return;
    }

    if (!xarVarFetch('name', 'pre:lower:ftoken:str:1:100', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc', 'str:0:255', $desc)) return;
    if (!xarVarFetch('theme', 'str:0:100', $theme)) return;

    if (!xarVarFetch('template', 'str:0:100', $template_default)) return;
    if (!xarVarFetch('template_select', 'str:1:100', $template, $template_default, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('page_template', 'str:0:100', $page_template_default)) return;
    if (!xarVarFetch('page_template_select', 'str:1:100', $page_template, $page_template_default, XARVAR_NOT_REQUIRED)) return;

    // The function/encode_url/decode_url come from form variables of
    // the same name, but may be over-ridden if any of *_select form
    // fields contain a value.
    if (!xarVarFetch('function', 'str:0:100', $function_default)) return;
    if (!xarVarFetch('function_select', 'str:1:100', $function, $function_default, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('encode_url', 'str:0:100', $encode_url_default)) return;
    if (!xarVarFetch('encode_url_select', 'str:1:100', $encode_url, $encode_url_default, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('decode_url', 'str:0:100', $decode_url_default)) return;
    if (!xarVarFetch('decode_url_select', 'str:1:100', $decode_url, $decode_url_default, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('alias', 'int:0:1', $alias, 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('return_url', 'str:0:200', $return_url, '', XARVAR_DONT_SET)) {return;}

    // Validate the status against the list available.
    $statuses = xarModAPIfunc('xarpages', 'user', 'getstatuses');
    if (!xarVarFetch('status', 'pre:upper:enum:' . implode(':', array_keys($statuses)), $status, NULL, XARVAR_NOT_REQUIRED)) return;

    // Allow the admin to propagate the status to all child pages (when ACIVE or INACTIVE).
    if (!xarVarFetch('status_recurse', 'bool', $status_recurse, NULL, XARVAR_NOT_REQUIRED)) return;

    // Bug 4495: ensure sensible defaults here, since these items may be suppressed in
    // the update form for some users.
    if (!xarVarFetch('moving', 'bool', $moving, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('movepage', 'bool', $movepage, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('refpid', 'pre:field:refpid:int:0', $refpid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('position', 'enum:before:after:firstchild:lastchild', $position, 'before', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (!$creating) {
        if (!xarModAPIFunc(
            'xarpages', 'admin', 'updatepage',
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
        )) {return;}
    } else {
        // Pass to API
        $pid = xarModAPIFunc(
            'xarpages', 'admin', 'createpage',
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
        if (!$pid) {return;}
    }

    if ($creating) {
        if ($batch) {
            // If there are more to create, then go to the create page.
            xarResponse::Redirect(
                xarModUrl(
                    'xarpages', 'admin', 'modifypage',
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
            xarResponse::Redirect(xarModURL('xarpages', 'admin', 'modifypage', array('pid' => $pid)));
        }
    } else {
        if (!empty($return_url)) {
            xarResponse::Redirect($return_url);
        } else {
            xarResponse::Redirect(xarModURL('xarpages', 'admin', 'viewpages'));
        }
    }

    return true;
}

?>