<?php
/**
 * File: $Id: updateconfig.php,v 1.9 2003/12/22 07:12:50 garrett Exp $
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
     * Validate parameters
     */
//FIXME: Need to figure out how Xar determines int vs. bool
// leave out until figured out
//  $invalid = array();
//  if(!isset($guestmode_1) || !is_bool($guestmode_1)) {
//      $invalid[] = 'guestmode_1';
//  }
//  if(!isset($guestmode_2) || !is_bool($guestmode_2)) {
//      $invalid[] = 'guestmode_2';
//  }
//  if(!isset($guestmode_3) || !is_bool($guestmode_3)) {
//      $invalid[] = 'guestmode_3';
//  }
//  if(!isset($usermode_1) || !is_bool($usermode_1)) {
//      $invalid[] = 'usermode_1';
//  }
//  if(!isset($usermode_2) || !is_bool($usermode_2)) {
//      $invalid[] = 'usermode_2';
//  }
//  if(!isset($usermode_3) || !is_bool($usermode_3)) {
//      $invalid[] = 'usermode_3';
//  }
//  if(!isset($abtitle) || !is_string($abtitle)) {
//      $invalid[] = 'abtitle';
//  }
//  if(!isset($sortdata_1) || !is_string($sortdata_1)) {
//      $invalid[] = 'sortdata_1';
//  }
//  if(!isset($sortdata_2) || !is_string($sortdata_2)) {
//      $invalid[] = 'sortdata_2';
//  }
//  if(!isset($sortdata_3) || !is_string($sortdata_3)) {
//      $invalid[] = 'sortdata_3';
//  }
//  if(!isset($sortdata_4) || !is_string($sortdata_4)) {
//      $invalid[] = 'sortdata_4';
//  }
//  if(!isset($name_order) || !is_string($name_order)) {
//      $invalid[] = 'name_order';
//  }
//  if(!isset($special_chars_1) || !is_string($special_chars_1)) {
//      $invalid[] = 'special_chars_1';
//  }
//  if(!isset($special_chars_2) || !is_string($special_chars_2)) {
//      $invalid[] = 'special_chars_2';
//  }
//  if(!isset($globalprotect) || !is_bool($globalprotect)) {
//      $invalid[] = 'globalprotect';
//  }
//  if(!isset($use_prefix) || !is_bool($use_prefix)) {
//      $invalid[] = 'use_prefix';
//  }
//  if(!isset($use_img) || !is_bool($use_img)) {
//      $invalid[] = 'use_img';
//  }
//  if(!isset($menu_off) || !is_string($menu_off)) {
//      $invalid[] = 'menu_off';
//  }
//  if(!isset($menu_semi) || !is_bool($menu_semi)) {
//      $invalid[] = 'menu_semi';
//  }
//  if(!isset($zipbeforecity) || !is_bool($zipbeforecity)) {
//      $invalid[] = 'zipbeforecity';
//  }
//  if(!isset($itemsperpage) || !is_int($itemsperpage)) {
//      $invalid[] = 'itemsperpage';
//  }
//  if(!isset($hidecopyright) || !is_bool($hidecopyright)) {
//      $invalid[] = 'hidecopyright';
//  }
//  if(!isset($custom_tab) || !is_string($custom_tab)) {
//      $invalid[] = 'custom_tab';
//  }
//  if(!isset($textareawidth) || !is_int($textareawidth)) {
//      $invalid[] = 'textareawidth';
//  }
//  if(!isset($dateformat) || !is_string($dateformat)) {
//      $invalid[] = 'dateformat';
//  }
//  if(!isset($numformat) || !is_string($numformat)) {
//      $invalid[] = 'numformat';
//  }
//    if (count($invalid) > 0) {
//        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
//                     join(', ', $invalid), 'admin', 'updateItems', __ADDRESSBOOK__);
//        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
//                    new SystemException($msg));
//      return; //???gehDEBUG
//    } else {
//    }

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