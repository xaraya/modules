<?php
/**
 * File: $Id: updateconfig.php,v 1.3 2004/11/13 06:21:57 garrett Exp $
 *
 * AddressBook admin functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * update the primary module configuration settings
 *
 * @param passed in from modifyconfig api
 * @return redirects back to modifyconfig page
 * @raise _AB_GLOBALPROTECTERROR, _AB_GRANTERROR, _AB_SORTERROR_1,
 *        _AB_SORTERROR_2, _AB_SPECIAL_CHARS_ERROR
 */
function addressbook_adminapi_updateconfig($args) 
{

    /**
     * Security check
     */
    if (!xarSecurityCheck('AdminAddressBook',0)) return FALSE;

    extract($args);

    /**
     * @TODO: Validate parameters
     */
    // Configure default values
    $guestmode = 0;
    if ($guestmode_1 == 1) ($guestmode += 1);
    if ($guestmode_2 == 1) ($guestmode += 2);
    if ($guestmode_3 == 1) ($guestmode += 4);

    $usermode = 0;
    if ($usermode_1 == 1) ($usermode += 1);
    if ($usermode_2 == 1) ($usermode += 2);
    if ($usermode_3 == 1) ($usermode += 4);

    // Custom formating
    if ($globalprotect == 1) {
        if (($guestmode != 0) || ($usermode != 7)) {
            $guestmode = 0;
            $usermode = 7;
            xarErrorSet(XAR_USER_EXCEPTION,
                        _AB_ERR_WARN,
                        new abUserException(_AB_GLOBALPROTECTERROR));
        }
    }
    if ($guestmode > $usermode) {
        $usermode = $guestmode;
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(_AB_GRANTERROR));
    }

    xarModSetVar(__ADDRESSBOOK__, 'guestmode',       $guestmode);
    xarModSetVar(__ADDRESSBOOK__, 'usermode',        $usermode);

    xarModSetVar(__ADDRESSBOOK__, 'abtitle',         $abtitle);

    if ($sortdata_1 == $sortdata_2) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(_AB_SORTERROR_1));
    }
    else {
        $s_1 = $sortdata_1.','.$sortdata_2;
        xarModSetVar(__ADDRESSBOOK__, 'sortorder_1', $s_1);
    }
    if ($sortdata_3 == $sortdata_4) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(_AB_SORTERROR_2));
    }
    else {
        $s_2 = $sortdata_3.','.$sortdata_4;
        xarModSetVar(__ADDRESSBOOK__, 'sortorder_2', $s_2);
    }

    xarModSetVar(__ADDRESSBOOK__, 'name_order',      $name_order);

    if (strlen($special_chars_1) != strlen($special_chars_2)) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(_AB_SPECIAL_CHARS_ERROR));
    }
    else {
        xarModSetVar(__ADDRESSBOOK__, 'special_chars_1', $special_chars_1);
        xarModSetVar(__ADDRESSBOOK__, 'special_chars_2', $special_chars_2);
    }

    // Admin Message
    if (!empty($rptErrAdminEmail)) {
        if (!xarModAPIFunc(__ADDRESSBOOK__,'util','is_email',array('email'=>$rptErrAdminEmail))) {
            xarErrorSet(XAR_USER_EXCEPTION,
                        _AB_ERR_WARN,
                        new abUserException(_AB_BAD_ADMIN_EMAIL));
        } else {
            xarModSetVar(__ADDRESSBOOK__, 'rptErrAdminEmail',   $rptErrAdminEmail);
        }
    }

    xarModSetVar(__ADDRESSBOOK__, 'globalprotect',   $globalprotect);
    xarModSetVar(__ADDRESSBOOK__, 'use_prefix',      $use_prefix);
    xarModSetVar(__ADDRESSBOOK__, 'display_prefix',  $display_prefix);
    xarModSetVar(__ADDRESSBOOK__, 'use_img',         $use_img);
    xarModSetVar(__ADDRESSBOOK__, 'menu_off',        $menu_off);
    xarModSetVar(__ADDRESSBOOK__, 'menu_semi',       $menu_semi);
    xarModSetVar(__ADDRESSBOOK__, 'zipbeforecity',   $zipbeforecity);
    xarModSetVar(__ADDRESSBOOK__, 'itemsperpage',    $itemsperpage);
    xarModSetVar(__ADDRESSBOOK__, 'hidecopyright',   $hidecopyright);

    xarModSetVar(__ADDRESSBOOK__, 'custom_tab',      $custom_tab);
    xarModSetVar(__ADDRESSBOOK__, 'textareawidth',   $textareawidth);
    xarModSetVar(__ADDRESSBOOK__, 'dateformat',      $dateformat);
    xarModSetVar(__ADDRESSBOOK__, 'numformat',       $numformat);

    xarModSetVar(__ADDRESSBOOK__, 'rptErrAdminFlag', $rptErrAdminFlag);
    xarModSetVar(__ADDRESSBOOK__, 'rptErrDevFlag',   $rptErrDevFlag);


//FIXME: <garrett> we want to say SUCCESS while at the same time
//      printing additional informational messages. how can we
//      prioritize them as done here?
//    $msg = xarVarPrepHTMLDisplay();
//    if (isset($error)) { $msg .= ' - '.$error; }
    xarErrorSet(XAR_USER_EXCEPTION,
                _AB_ERR_INFO,
                new abUserException(_AB_CONF_AB_SUCCESS));
// END FIXME

    // Return
    return TRUE;

} // END updateconfig

?>