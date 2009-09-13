<?php

/**
 * File: $Id$
 *
 * Delete a page
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_deletepage($args)
{
    extract($args);

    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('confirm', 'str:1', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url', 'str:0:200', $return_url, '', XARVAR_DONT_SET)) {return;}

    // Get page information
    $page = xarModAPIFunc(
        'xarpages', 'user', 'getpage',
        array('pid' => $pid)
    );

    if (empty($page)) {
        $msg = xarML('The page #(1) to be deleted does not exist', $pid);
        throw new BadParameterException(null,$msg);
    }

    // Security check
    if (!xarSecurityCheck('DeleteXarpagesPage', 1, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
        return false;
    }

    // Check for confirmation
    if (empty($confirm)) {
        $data = array('page' => $page, 'return_url' => $return_url);
        $data['authkey'] = xarSecGenAuthKey();

        $data['count'] = xarModAPIfunc(
            'xarpages', 'user', 'getpages',
            array('count' => true, 'left_range' => array($page['left']+1, $page['right']-1))
        );

        // Return output
        return $data;
    }

    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) {
        return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
    }        

    // Pass to API
    if (!xarModAPIFunc(
        'xarpages', 'admin', 'deletepage',
        array('pid' => $pid))
    ) return;

    if (!empty($return_url)) {
        xarResponse::Redirect($return_url);
    } else {
        xarResponse::Redirect(xarModURL('xarpages', 'admin', 'viewpages'));
    }

    return true;
}

?>
