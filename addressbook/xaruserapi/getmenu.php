<?php
/**
 * File: $Id: xaradminapi.php,v 1.3 2003/06/30 04:37:08 garrett Exp $
 *
 * AddressBook user getMenu
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
 * Displays user menu
 */
function AddressBook_userapi_getMenu($args) {
    extract ($args);
    if (!is_array ($data)) { $data = array (); }

    $data['authid'] = xarSecGenAuthKey();

    if (xarSecurityCheck('EditAddressBook',0)) {
        $data['show_menu_off'] = false;
    }
    else {
        $m = xarModGetVar(__ADDRESSBOOK__, 'menu_off');
        switch ($m) {
            case 0:
                $data['show_menu_off'] = FALSE;
                break;
            case 1:
                $data['show_menu_off'] = TRUE;
                break;
            case 2:
                if (xarUserIsLoggedIn()) {
                    $data['show_menu_off'] = FALSE;
                } else {
                    $data['show_menu_off'] = TRUE;
                }
                break;
            default:
                $data['show_menu_off'] = TRUE;
                break;
        }
    }
    if ($data['show_menu_off']) {
        return $data;
    }
    else {
        // Start Category
        $data['cats'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getCategories');

        $sortby_1 = xarModAPIFunc(__ADDRESSBOOK__,'user','getSortBy',array('sort'=>1));
        $sortby_2 = xarModAPIFunc(__ADDRESSBOOK__,'user','getSortBy',array('sort'=>2));
        $data['sortby'][] = array('id'=>0,'name'=>xarVarPrepHTMLDisplay($sortby_1));
        $data['sortby'][] = array('id'=>1,'name'=>xarVarPrepHTMLDisplay($sortby_2));

        if ((xarModGetVar(__ADDRESSBOOK__, 'menu_semi') != 1) || (xarSecurityCheck('EditAddressBook',0))) {

            // Removed display stuff

            /**
             * Do we show the private menu link
             */
            if (xarUserIsLoggedIn()) {

                if (((xarModGetVar(__ADDRESSBOOK__, 'globalprotect'))==1) && (!xarSecurityCheck('EditAddressBook',0))) {
                    $menuprivate_fl = 1;
                }
                else {
                    if ($data['menuprivate'] == 1) {
                        $menuprivate_fl = 0;
                    }
                    else {
                        $menuprivate_fl = 1;
                    }
                }

                $data['menuprivateParams'] =
                        array('authid'=>xarSecGenAuthKey()
                             ,'sortview'=>$data['sortview']
                             /*,'total'=>$data['total']*/
                             ,'catview'=>$data['catview']
                             ,'menuprivate'=>$menuprivate_fl
                             ,'all'=> $data['all']
                             ,'char'=>$data['char']);

                $data['viewPrivateTEXT'] = xarML(_AB_VIEWPRIVATE);
                if ($data['menuprivate']) {
                    $data['privateIndicator'] = 'X';
                } else {
                    $data['privateIndicator'] = '&nbsp;';
                }


            }
            // end private

            $data['azMenuTEXT'] = xarML(_AB_MENU_AZ);
            $data['allMenuTEXT'] = xarML(_AB_MENU_ALL);

            // Start A-Z/All
            if ($data['all'] == 1) {
                $data['azInidcator'] = '&nbsp;';
                $data['allInidcator']= 'X';
            }
            else {
                $data['azInidcator'] = 'X';
                $data['allInidcator']= '&nbsp;';
            }
            $data['azParams'] = array('authid'=>xarSecGenAuthKey()
                                ,'sortview'=>$data['sortview']
                                ,'catview'=>$data['catview']
                                ,'menuprivate'=>$data['menuprivate']
                                ,'all'=>0
                                ,'char'=>$data['char']);

            $data['allParams'] = array('authid'=>xarSecGenAuthKey()
                                ,'sortview'=>$data['sortview']
                                ,'catview'=>$data['catview']
                                ,'menuprivate'=>$data['menuprivate']
                                ,'all'=>1
                                /*,'total'=>$data['total']*/
                                ,'page'=>$data['page']
                                ,'char'=>$data['char']);

            // End A-Z/all

            /**
             * New Address Link processing
             */

            // URL query params passed by new address link
            $data['menuValues']=array('formcall'=>'insert',
                                        'authid'=>xarSecGenAuthKey(),
                                        'catview'=>$data['catview'],
                                        'menuprivate'=>$data['menuprivate'],
                                        'all'=>$data['all'],
                                        'sortview'=>$data['sortview'],
                                        'page'=>$data['page'],
                                        'char'=>$data['char'],
                                        'total'=>$data['total']);

            $data['newAddrLinkTEXT'] = xarVarPrepHTMLDisplay (xarML(_AB_MENU_ADD));

            // Set our flag / template uses to determine if it should display the link
            $data['addNewAddress'] = xarModAPIFunc(__ADDRESSBOOK__,'user','checkAccessLevel',array('option'=>'create'));

            // End New Record

        }
    }

    return $data;
} // END getMenu

?>