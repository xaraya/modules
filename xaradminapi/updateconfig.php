<?php
/**
 * AddressBook admin functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
 * @throws GLOBALPROTECTERROR
 *        GRANTERROR
 *        SORTERROR_1
 *        SORTERROR_2
 *        SPECIAL_CHARS_ERROR
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
                        new abUserException(xarML('Corrected: In personal address book mode guests have no and registered user have full access rights!!!')));
        }
    }
    if ($guestmode > $usermode) {
        $usermode = $guestmode;
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(xarML('Corrected: The access rights of guest were higher than for registered users!!!')));
    }

    xarModSetVar('addressbook', 'guestmode',       $guestmode);
    xarModSetVar('addressbook', 'usermode',        $usermode);

    xarModSetVar('addressbook', 'abtitle',         $abtitle);

    if ($sortdata_1 == $sortdata_2) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(xarML('Equal columns selected / Default sort order was not changed!')));
    }
    else {
        $s_1 = $sortdata_1.','.$sortdata_2;
        xarModSetVar('addressbook', 'sortorder_1', $s_1);
    }
    if ($sortdata_3 == $sortdata_4) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(xarML('Equal columns selected / Alternate sort order was not changed!')));
    }
    else {
        $s_2 = $sortdata_3.','.$sortdata_4;
        xarModSetVar('addressbook', 'sortorder_2', $s_2);
    }

    xarModSetVar('addressbook', 'name_order',      $name_order);

    if (strlen($special_chars_1) != strlen($special_chars_2)) {
        xarErrorSet(XAR_USER_EXCEPTION,
                    _AB_ERR_WARN,
                    new abUserException(xarML('Both fields must contain the same number of characters - Special character replacement was NOT saved!')));
    }
    else {
        xarModSetVar('addressbook', 'special_chars_1', $special_chars_1);
        xarModSetVar('addressbook', 'special_chars_2', $special_chars_2);
    }

    // Admin Message
    if (!empty($rptErrAdminEmail)) {
        if (!xarModAPIFunc('addressbook','util','is_email',array('email'=>$rptErrAdminEmail))) {
            xarErrorSet(XAR_USER_EXCEPTION,
                        _AB_ERR_WARN,
                        new abUserException(_AB_BAD_ADMIN_EMAIL));
        } else {
            xarModSetVar('addressbook', 'rptErrAdminEmail',   $rptErrAdminEmail);
        }
    }

    xarModSetVar('addressbook', 'globalprotect',   $globalprotect);
    xarModSetVar('addressbook', 'use_prefix',      $use_prefix);
    xarModSetVar('addressbook', 'display_prefix',  $display_prefix);
    xarModSetVar('addressbook', 'use_img',         $use_img);
    xarModSetVar('addressbook', 'menu_off',        $menu_off);
    xarModSetVar('addressbook', 'menu_semi',       $menu_semi);
    xarModSetVar('addressbook', 'zipbeforecity',   $zipbeforecity);
    xarModSetVar('addressbook', 'itemsperpage',    $itemsperpage);
    xarModSetVar('addressbook', 'hidecopyright',   $hidecopyright);

    xarModSetVar('addressbook', 'custom_tab',      $custom_tab);
    xarModSetVar('addressbook', 'textareawidth',   $textareawidth);
    xarModSetVar('addressbook', 'dateformat',      $dateformat);
    xarModSetVar('addressbook', 'numformat',       $numformat);

    xarModSetVar('addressbook', 'rptErrAdminFlag', $rptErrAdminFlag);
    xarModSetVar('addressbook', 'rptErrDevFlag',   $rptErrDevFlag);


//FIXME: <garrett> we want to say SUCCESS while at the same time
//      printing additional informational messages. how can we
//      prioritize them as done here?
//    $msg = xarVarPrepHTMLDisplay();
//    if (isset($error)) { $msg .= ' - '.$error; }
    xarErrorSet(XAR_USER_EXCEPTION,
                _AB_ERR_INFO,
                new abUserException(xarML('Configuration saved!')));
// END FIXME

    // Return
    return TRUE;

} // END updateconfig

?>