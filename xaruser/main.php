<?php
/**
 * External page entry point
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 * @author Yassen Yotov (CyberOto)
 * @author  Shawn McKenzie (AbraCadaver)
 */

/**
 * External page entry point
 *
 * @return  data on success or void on falure
 * @throws  XAR_SYSTEM_EXCEPTION, 'NOT_ALLOWED'
*/
function window_user_main($args) 
{
    extract($args);

    $allow_local_only = xarModGetVar('window', 'allow_local_only');
    $use_buffering = xarModGetVar('window', 'use_buffering');
    $reg_user_only = xarModGetVar('window', 'reg_user_only');
    $no_user_entry = xarModGetVar('window', 'no_user_entry');
    $open_direct = xarModGetVar('window', 'open_direct');
    $use_fixed_title = xarModGetVar('window', 'use_fixed_title');
    $auto_resize = xarModGetVar('window', 'auto_resize');
    $vsize = xarModGetVar('window', 'vsize');
    $hsize = xarModGetVar('window', 'hsize');
    $security = xarModGetVar('window', 'security');

    if (!xarVarFetch('page', 'str', $page, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str', $title, xarML('External Application'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hsize', 'str', $hsize, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('vsize', 'str', $vsize, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('auto_resize', 'str', $auto_resize, NULL, XARVAR_NOT_REQUIRED)) return;

    if ($security) {
        if (!xarSecurityCheck('ReadWindow')) return;

        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $urltable = $xartable['window'];

        $result = $dbconn->Execute("SELECT * FROM $urltable");
        if(!$result) return;

        $db_checked = 0;

        if(!$result->EOF) {
            while(list($id, $name, $alias, $reg_user_only1, $open_direct1, $use_fixed_title1, $auto_resize1, $vsize1, $hsize1) = $result->fields) {

                // Check if URL is in DB
                if (($alias == $page) || ($name == $page) || ($name == "http://".$page)) {
                    $db_checked = 1;
                    $page = $name;
                    // Override global settings
                    $reg_user_only = $reg_user_only1;
                    $open_direct = $open_direct1;
                    $use_fixed_title = $use_fixed_title1;
                    $auto_resize = $auto_resize1;
                    $vsize = $vsize1;
                    $hsize = $hsize1;
                    break;
                }
                $result->MoveNext();
            }
        }

        // Nope - display a message and quit
        if(!$db_checked) {
            $msg = xarML('No URLs in the database that match your page.',
                'window');
            xarErrorSet(XAR_USER_EXCEPTION,
                'NOT_ALLOWED',
                new DefaultUserException($msg));
            return;
        }
    }

    // Store URL parts in array
    $url_parts = parse_url($page);


    // Check that a page was specified
    if(!isset($page) || ($page == '')) {
        $msg = xarML('No page to display was specified.',
            'window');
        xarErrorSet(XAR_USER_EXCEPTION,
            'NOT_ALLOWED',
            new DefaultUserException($msg));
        return;
    }

    // Check for not entered in browser location window if set
    if (!$_SERVER['REMOTE_ADDR'] && !$no_user_entry) {
        $msg = xarML('You cannot access this page via a link.',
            'window');
        xarErrorSet(XAR_USER_EXCEPTION,
            'NOT_ALLOWED',
            new DefaultUserException($msg));
        return;
    }

    // Check for not local page if set
    if($allow_local_only &&
        ($url_parts['host'] != $_SERVER['SERVER_NAME']) &&
        ($url_parts['host'] != $_SERVER['HTTP_HOST'])) {
        $msg = xarML('Only pages off your local server can be displayed.', 'window');
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_ALLOWED', new DefaultUserException($msg));
        return;
    }

    // Check that user is registered and logged in if set
    if(!xarUserIsLoggedIn() && ($reg_user_only)) {
        $msg = xarML('Only registered users can view this page.',
            'window');
        xarErrorSet(XAR_USER_EXCEPTION,
            'NOT_ALLOWED',
            new DefaultUserException($msg));
        return;
    }

    // Everything is good - ready to display

    // Check for fixed title and use it
    // Check if title was passed in URL
    if(!$title) {
        if($use_fixed_title) {
            $title = 'External Application';
        }
        else {
            $title = '';
        }
    }
    else {
        $end_title = '';
    }

    // Add the Open Direct link if set
    if ($open_direct) {
        if($use_fixed_title) {
            $title .= "<br />[ <a href=\"$page\" target=\"_blank\">".xarML("Open application")."</a> ]";
        }
        else {
            $title .= "[ <a href=\"$page\" target=\"_blank\">".xarML("Open application")."</a> ]";
        }
    }

    // Check if height, width or resize were passed in URL
    if (isset($height)) {
        $vsize = $height;
        $auto_resize = false; 
    }

    if(isset($width)) {
        $hsize = $width;
    } elseif (!$hsize) {
        $hsize = '100%'; 
    }

    if (isset($resize) && $resize == 1) {
        $auto_resize = true; 
    }

    if (isset($id)) {
        $data['hooks'] = xarModCallHooks('item', 'display', $id, array('itemtype'  => $id,
                                                                       'returnurl' => xarModURL('window', 'user', 'main', array('page' => $page, 'id' => $id))),
                                                                'window');
    }

    $data['page'] = $page;
    $data['title'] = $title;
    $data['hsize'] = $hsize;
    $data['vsize'] = $vsize;
    $data['auto_resize'] = $auto_resize;
    $data['open_direct'] = $open_direct;

    return xarTplModule('window','user','display', $data);
}
?>