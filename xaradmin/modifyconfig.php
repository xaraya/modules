<?php

/**
 * File: $Id$
 *
 * Modify module configuration
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_modifyconfig($args)
{
    extract($args);
    $data = array();

    // Need admin priv to modify config.
    if (!xarSecurityCheck('AdminXarpagesPage')) return;

    // Get the tree of all pages.
    $data['tree'] = xarModAPIfunc('xarpages', 'user', 'getpagestree', array('dd_flag' => false));

    // Implode the names for each page into a path for display.
    foreach ($data['tree']['pages'] as $key => $page) {
        $data['tree']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
    }

    // Check if we are receiving a submitted form.
    xarVarFetch('authid', 'str', $authid, '', XARVAR_NOT_REQUIRED);

    if (empty($authid)) {
        // First visit to this form (nothing being submitted).

        // Get the current module values.
        $data['defaultpage'] = xarModVars::get('xarpages', 'defaultpage');
        $data['errorpage'] = xarModVars::get('xarpages', 'errorpage');
        $data['notfoundpage'] = xarModVars::get('xarpages', 'notfoundpage');
        $data['noprivspage'] = xarModVars::get('xarpages', 'noprivspage');

        // Boolean (1/0) flags.
        $data['transformref'] = xarModVars::get('xarpages', 'transformref');
        $data['shortestpath'] = xarModVars::get('xarpages', 'shortestpath');
        $data['shorturl'] = xarModVars::get('xarpages', 'SupportShortURLs');

        // Text fields
        $data['transformfields'] = xarModVars::get('xarpages', 'transformfields');
    } else {
        // Form has been submitted.

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) {return;}

        // Get the special pages.
        foreach(array('defaultpage', 'errorpage', 'notfoundpage', 'noprivspage') as $special_name) {
            unset($special_id);
            if (!xarVarFetch($special_name, 'id', $special_id, 0, XARVAR_NOT_REQUIRED)) {return;}
            xarModVars::set('xarpages', $special_name, $special_id);

            // Save value for redisplaying in the form.
            $data[$special_name] = $special_id;
        }

        // Short URL flag.
        xarVarFetch('shorturl', 'int:0:1', $shorturl, 0, XARVAR_NOT_REQUIRED);
        xarModVars::set('xarpages', 'SupportShortURLs', $shorturl);
        $data['shorturl'] = $shorturl;

        // Shortest path flag.
        xarVarFetch('shortestpath', 'int:0:1', $shortestpath, 0, XARVAR_NOT_REQUIRED);
        xarModVars::set('xarpages', 'shortestpath', $shortestpath);
        $data['shortestpath'] = $shortestpath;

        // Enable internal references transform flag.
        // This transforms "#" anchors in content to an absolute URI for the page.
        xarVarFetch('transformref', 'int:0:1', $transformref, 0, XARVAR_NOT_REQUIRED);
        xarModVars::set('xarpages', 'transformref', $transformref);
        $data['transformref'] = $transformref;

        // Limit the DD fields that will be transformed.
        xarVarFetch('transformfields', 'strlist: ,;|:pre:trim:vtoken', $transformfields, '', XARVAR_NOT_REQUIRED);
        xarModVars::set('xarpages', 'transformfields', $transformfields);
        $data['transformfields'] = $transformfields;
    }

    // Check any problem aliases
    $problem_aliases = xarModAPIfunc('xarpages', 'user', 'getaliases', array('mincount' => 2));
    $data['problem_aliases'] = $problem_aliases;

    $data['authid'] = xarSecGenAuthKey();

    // Config hooks for all page types.

    // Get the itemtype of the page type.
    $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');

    $confighooks = xarModCallHooks(
        'module', 'modifyconfig', 'xarpages',
        array('module' => 'xarpages', 'itemtype' => $type_itemtype)
    );

    // Clear out any empty hooks.
    foreach($confighooks as $key => $confighook) {
        if (trim($confighook) == '') {
            unset($confighooks[$key]);
        } else {
            $confighooks[$key] = trim($confighook);
        }
    }
    $data['confighooks'] =& $confighooks;

    return $data;
}

?>
