<?php
/**
 * File: $Id$
 *
 * AddressBook utility functions
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

//FIXME: until we figure out module globals
include_once ('modules/addressbook/xarglobal.php');

//=========================================================================
//  the main administration function
//=========================================================================
function AddressBook_admin_main() {

    /**
     * Check if we want to display our overview panel.
     */
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // If you want to go directly to some default function, instead of
        // having a separate main function, you can simply call it here, and
        // use the same template for admin-main.xard as for admin-view.xard
        // return example_admin_view();

        // Initialise the $data variable that will hold the data to be used in
        // the blocklayout template, and get the common menu configuration - it
        // helps if all of the module pages have a standard menu at the top to
        // support easy navigation
        $data = AddressBook_admin_menu();

        // Specify some other variables used in the blocklayout template
        $data['welcome'] = xarML('Welcome to the administration part of this Example module...');

        // Return the template variables defined in this function
        return $data;

        // Note : instead of using the $data variable, you could also specify
        // the different template variables directly in your return statement :
        //
        // return array('menutitle' => ...,
        //              'welcome' => ...,
        //              ... => ...);
    }

} // END main

function AddressBook_admin_menu()
{
    // Initialise the array that will hold the menu configuration
    $menu = array();

    // Specify the menu title to be used in your blocklayout template
    $menu['menutitle'] = xarML('Address Book Administration');

    // Specify the menu labels to be used in your blocklayout template

    // Preset some status variable
    $menu['status'] = '';

    $menu['menuitems'][] = array ('label','#');

    // Note : you could also specify the menu links here, and pass them
    // on to the template as variables
    // $menu['menulink_view'] = xarModURL('example','admin','view');

    // Note : you could also put all menu items in a $menu['menuitems'] array
    //
    // Initialise the array that will hold the different menu items
    // $menu['menuitems'] = array();
    //
    // Define a menu item
    // $item = array();
    // $item['menulabel'] = _EXAMPLEVIEW;
    // $item['menulink'] = xarModURL('example','user','view');
    //
    // Add it to the array of menu items
    // $menu['menuitems'][] = $item;
    //
    // Add more menu items to the array
    // ...
    //
    // Then you can let the blocklayout template create the different
    // menu items *dynamically*, e.g. by using something like :
    //
    // <xar:loop name="menuitems">
    //    <td><a href="&xar-var-menulink;">&xar-var-menulabel;</a></td>
    // </xar:loop>
    //
    // in the templates of your module. Or you could even pass an argument
    // to the admin_menu() function to turn links on/off automatically
    // depending on which function is currently called...
    //
    // But most people will prefer to specify all this manually in each
    // blocklayout template anyway :-)

    // Return the array containing the menu configuration
    return $menu;
}

//=========================================================================
//  Modify the settings
//=========================================================================
function AddressBook_admin_modifyconfig() {

    // Security check
    if (!xarSecurityCheck('AdminAddressBook',0)) {
        return;
    }

    // Set values that will be displayed in the template
    switch (xarModGetVar(__ADDRESSBOOK__, 'guestmode')) {
        case '0':
            $output['guestmode_1'] = 0;
            $output['guestmode_2'] = 0;
            $output['guestmode_3'] = 0;
            break;
        case '1':
            $output['guestmode_1'] = 0;
            $output['guestmode_2'] = 0;
            $output['guestmode_3'] = 1;
            break;
        case '2':
            $output['guestmode_1'] = 0;
            $output['guestmode_2'] = 1;
            $output['guestmode_3'] = 0;
            break;
        case '3':
            $output['guestmode_1'] = 0;
            $output['guestmode_2'] = 1;
            $output['guestmode_3'] = 1;
            break;
        case '4':
            $output['guestmode_1'] = 1;
            $output['guestmode_2'] = 0;
            $output['guestmode_3'] = 0;
            break;
        case '5':
            $output['guestmode_1'] = 1;
            $output['guestmode_2'] = 0;
            $output['guestmode_3'] = 1;
            break;
        case '6':
            $output['guestmode_1'] = 1;
            $output['guestmode_2'] = 1;
            $output['guestmode_3'] = 0;
            break;
        case '7':
            $output['guestmode_1'] = 1;
            $output['guestmode_2'] = 1;
            $output['guestmode_3'] = 1;
            break;
    }

    switch (xarModGetVar(__ADDRESSBOOK__, 'usermode')) {
        case '0':
            $output['usermode_1'] = 0;
            $output['usermode_2'] = 0;
            $output['usermode_3'] = 0;
            break;
        case '1':
            $output['usermode_1'] = 0;
            $output['usermode_2'] = 0;
            $output['usermode_3'] = 1;
            break;
        case '2':
            $output['usermode_1'] = 0;
            $output['usermode_2'] = 1;
            $output['usermode_3'] = 0;
            break;
        case '3':
            $output['usermode_1'] = 0;
            $output['usermode_2'] = 1;
            $output['usermode_3'] = 1;
            break;
        case '4':
            $output['usermode_1'] = 1;
            $output['usermode_2'] = 0;
            $output['usermode_3'] = 0;
            break;
        case '5':
            $output['usermode_1'] = 1;
            $output['usermode_2'] = 0;
            $output['usermode_3'] = 1;
            break;
        case '6':
            $output['usermode_1'] = 1;
            $output['usermode_2'] = 1;
            $output['usermode_3'] = 0;
            break;
        case '7':
            $output['usermode_1'] = 1;
            $output['usermode_2'] = 1;
            $output['usermode_3'] = 1;
            break;
    }

    // User Title for Address Book
    $output['abtitle'] = xarModGetVar(__ADDRESSBOOK__, 'abtitle');


    // Default Sort Order
    $output['defSortCols'] = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_1'));

    /**
     * Inclue custom fields in sorts & ordering
     */
    $custom_tab = xarModGetVar(__ADDRESSBOOK__,'custom_tab');
    $custFieldTypes = array();
    if (!empty($custom_tab)) {
//        $custFieldLabels = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldLabels');
        $custFieldLabels = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldInfo');
    }

    // [Primary]
    $output['sortdata_1'][] = array('id'=>'sortname',   'name'=>xarML(_AB_NAME));
    $output['sortdata_1'][] = array('id'=>'title',      'name'=>xarML(_AB_TITLE));
    $output['sortdata_1'][] = array('id'=>'sortcompany','name'=>xarML(_AB_COMPANY));
    $output['sortdata_1'][] = array('id'=>'zip',        'name'=>xarML(_AB_ZIP));
    $output['sortdata_1'][] = array('id'=>'city',       'name'=>xarML(_AB_CITY));
    $output['sortdata_1'][] = array('id'=>'state',      'name'=>xarML(_AB_STATE));
    $output['sortdata_1'][] = array('id'=>'country',    'name'=>xarML(_AB_COUNTRY));
    if ((!empty($custom_tab)) && ($custom_tab != '')) {
        foreach($custFieldLabels as $custFieldLabel) {
            $output['sortdata_1'][] = array('id'=>$custFieldLabel['colName'], 'name'=>xarVarPrepHTMLDisplay($custFieldLabel['label']));
        }
    }

    // [Secondary]
    $output['sortdata_2'][] = array('id'=>'sortname',   'name'=>xarML(_AB_NAME));
    $output['sortdata_2'][] = array('id'=>'title',      'name'=>xarML(_AB_TITLE));
    $output['sortdata_2'][] = array('id'=>'sortcompany','name'=>xarML(_AB_COMPANY));
    $output['sortdata_2'][] = array('id'=>'zip',        'name'=>xarML(_AB_ZIP));
    $output['sortdata_2'][] = array('id'=>'city',       'name'=>xarML(_AB_CITY));
    $output['sortdata_2'][] = array('id'=>'state',      'name'=>xarML(_AB_STATE));
    $output['sortdata_2'][] = array('id'=>'country',    'name'=>xarML(_AB_COUNTRY));
    if ((!empty($custom_tab)) && ($custom_tab != '')) {
        foreach($custFieldLabels  as $custFieldLabel) {
            $output['sortdata_2'][] = array('id'=>$custFieldLabel['colName'], 'name'=>xarVarPrepHTMLDisplay($custFieldLabel['label']));
        }
    }

    // Alternate Sort Order
    $output['altSortCols'] = explode(',',xarModGetVar(__ADDRESSBOOK__, 'sortorder_2'));

    // Primary
    $output['sortdata_3'][] = array('id'=>'sortname',   'name'=>xarML(_AB_NAME));
    $output['sortdata_3'][] = array('id'=>'title',      'name'=>xarML(_AB_TITLE));
    $output['sortdata_3'][] = array('id'=>'sortcompany','name'=>xarML(_AB_COMPANY));
    $output['sortdata_3'][] = array('id'=>'zip',        'name'=>xarML(_AB_ZIP));
    $output['sortdata_3'][] = array('id'=>'city',       'name'=>xarML(_AB_CITY));
    $output['sortdata_3'][] = array('id'=>'state',      'name'=>xarML(_AB_STATE));
    $output['sortdata_3'][] = array('id'=>'country',    'name'=>xarML(_AB_COUNTRY));
    if ((!empty($custom_tab)) && ($custom_tab != '')) {
        foreach($custFieldLabels  as $custFieldLabel) {
            $output['sortdata_3'][] = array('id'=>$custFieldLabel['colName'], 'name'=>xarVarPrepHTMLDisplay($custFieldLabel['label']));
        }
    }

    // Secondary
    $output['sortdata_4'][] = array('id'=>'sortname',   'name'=>xarML(_AB_NAME));
    $output['sortdata_4'][] = array('id'=>'title',      'name'=>xarML(_AB_TITLE));
    $output['sortdata_4'][] = array('id'=>'sortcompany','name'=>xarML(_AB_COMPANY));
    $output['sortdata_4'][] = array('id'=>'zip',        'name'=>xarML(_AB_ZIP));
    $output['sortdata_4'][] = array('id'=>'city',       'name'=>xarML(_AB_CITY));
    $output['sortdata_4'][] = array('id'=>'state',      'name'=>xarML(_AB_STATE));
    $output['sortdata_4'][] = array('id'=>'country',    'name'=>xarML(_AB_COUNTRY));
    if ((!empty($custom_tab)) && ($custom_tab != '')) {
        foreach($custFieldLabels  as $custFieldLabel) {
            $output['sortdata_4'][] = array('id'=>$custFieldLabel['colName'], 'name'=>xarVarPrepHTMLDisplay($custFieldLabel['label']));
        }
    }

    // Name display in list view & sort order
    $temp1 = xarVarPrepHTMLDisplay(_AB_SO_LASTNAME).', '.xarVarPrepHTMLDisplay(_AB_SO_FIRSTNAME);
    $temp2 = xarVarPrepHTMLDisplay(_AB_SO_FIRSTNAME).' '.xarVarPrepHTMLDisplay(_AB_SO_LASTNAME);
    $output['name_order'][] = array('id'=>0, 'name'=>$temp1);
    $output['name_order'][] = array('id'=>1, 'name'=>$temp2);
    $output['name_order_selected'] = xarModGetVar(__ADDRESSBOOK__, 'name_order');

    // Additional Settings
    $output['special_chars_1']  = xarModGetVar(__ADDRESSBOOK__, 'special_chars_1');
    $output['special_chars_2']  = xarModGetVar(__ADDRESSBOOK__, 'special_chars_2');

    $output['globalprotect']    = xarModGetVar(__ADDRESSBOOK__, 'globalprotect');
    $output['use_prefix']       = xarModGetVar(__ADDRESSBOOK__, 'use_prefix');
    $output['use_img']          = xarModGetVar(__ADDRESSBOOK__, 'use_img');

    // Disable / enable menu options
    $output['menu_off'][] = array('id'=>0, 'name'=>xarVarPrepHTMLDisplay(_AB_HIDENOTHING));
    $output['menu_off'][] = array('id'=>1, 'name'=>xarVarPrepHTMLDisplay(_AB_HIDEALL));
    $output['menu_off'][] = array('id'=>2, 'name'=>xarVarPrepHTMLDisplay(_AB_HIDEGUESTS));
    $output['menu_off_selected'] = (int) xarModGetVar(__ADDRESSBOOK__, 'menu_off');

    $output['menu_semi']        = xarModGetVar(__ADDRESSBOOK__, 'menu_semi');
    $output['zipbeforecity']    = xarModGetVar(__ADDRESSBOOK__, 'zipbeforecity');
    $output['itemsperpage']     = xarModGetVar(__ADDRESSBOOK__, 'itemsperpage');
    $output['hidecopyright']    = xarModGetVar(__ADDRESSBOOK__, 'hidecopyright');
    $output['custom_tab']       = xarModGetVar(__ADDRESSBOOK__, 'custom_tab');
    $output['textareawidth']    = xarModGetVar(__ADDRESSBOOK__, 'textareawidth');

    $output['dateformat'][] = array('id'=>0, 'name'=>xarVarPrepForDisplay(_AB_DATEFORMAT_1));
    $output['dateformat'][] = array('id'=>1, 'name'=>xarVarPrepForDisplay(_AB_DATEFORMAT_2));
    $output['dateformat_selected'] = xarModGetVar(__ADDRESSBOOK__, 'dateformat');

    $output['numformat'][] = array('id'=>'9,999.99', 'name'=>'9,999.99');
    $output['numformat'][] = array('id'=>'9.999,99', 'name'=>'9.999,99');
    $output['numformat_selected'] = xarModGetVar(__ADDRESSBOOK__, 'numformat');

    // Generate a one-time authorisation code for this operation
    $output['authid'] = xarSecGenAuthKey();

    // Submit button
    $output['btnCommitText'] = xarVarPrepHTMLDisplay(_AB_ADDRESSBOOKUPDATE);

    return $output;

} // END modifyconfig

//=========================================================================
//  Update the settings
//=========================================================================
function AddressBook_admin_updateconfig() {

    if (!xarSecConfirmAuthKey()) return;

    // Security check
    if (!xarSecurityCheck('AdminAddressBook',0)) {
        return;
    }

    // Security Settins
    if (!xarVarFetch ('guestmode_1','checkbox',$guestmode_1, 0)) return;
    if (!xarVarFetch ('guestmode_2','checkbox',$guestmode_2, 0)) return;
    if (!xarVarFetch ('guestmode_3','checkbox',$guestmode_3, 0)) return;
    $guestmode = 0;
    if ($guestmode_1 == 1) ($guestmode += 4);
    if ($guestmode_2 == 1) ($guestmode += 2);
    if ($guestmode_3 == 1) ($guestmode += 1);

    if (!xarVarFetch ('usermode_1','checkbox',$usermode_1, 0)) return;
    if (!xarVarFetch ('usermode_2','checkbox',$usermode_2, 0)) return;
    if (!xarVarFetch ('usermode_3','checkbox',$usermode_3, 0)) return;
    $usermode = 0;
    if ($usermode_1 == 1) ($usermode += 4);
    if ($usermode_2 == 1) ($usermode += 2);
    if ($usermode_3 == 1) ($usermode += 1);

    // Other Settings
    if (!xarVarFetch ('abtitle','str::60',$abtitle, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch ('sortdata_1','str:1:',$sortdata_1)) return;
    if (!xarVarFetch ('sortdata_2','str:1:',$sortdata_2)) return;
    if (!xarVarFetch ('sortdata_3','str:1:',$sortdata_3)) return;
    if (!xarVarFetch ('sortdata_4','str:1:',$sortdata_4)) return;
    if (!xarVarFetch ('name_order','str:1:',$name_order, 0)) return;
    if (!xarVarFetch ('special_chars_1','str:1:24',$special_chars_1, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch ('special_chars_2','str:1:24',$special_chars_2, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch ('globalprotect','checkbox',$globalprotect, 0)) return;
    if (!xarVarFetch ('use_prefix',   'checkbox',$use_prefix,    0)) return;
    if (!xarVarFetch ('use_img',      'checkbox',$use_img,       0)) return;
    if (!xarVarFetch ('menu_off',     'str:1:',  $menu_off,      0)) return;
    if (!xarVarFetch ('menu_semi',    'checkbox',$menu_semi,     0)) return;
    if (!xarVarFetch ('zipbeforecity','checkbox',$zipbeforecity, 0)) return;
    if (!xarVarFetch ('itemsperpage','int:1:100',$itemsperpage,  30)) return;
    if (!xarVarFetch ('hidecopyright','checkbox',$hidecopyright, 0)) return;

    // Custom Labels
    if (!xarVarFetch ('custom_tab',  'str::60',      $custom_tab, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch ('textareawidth','int:4:100',   $textareawidth, 60)) return;
    if (!xarVarFetch ('dateformat',  'str:1:',       $dateformat,    0)) return;
    if (!xarVarFetch ('numformat',   'str:1:',       $numformat,     '9,999.99')) return;

    // Custom formating
    if ($globalprotect == 1) {
        if (($guestmode != 0) || ($usermode != 7)) {
            $guestmode = 0;
            $usermode = 7;
            $error = xarVarPrepHTMLDisplay(_AB_GLOBALPROTECTERROR);
        }
    }
    if ($guestmode > $usermode) {
        $usermode = $guestmode;
        $error = xarVarPrepHTMLDisplay(_AB_GRANTERROR);
    }

    xarModSetVar(__ADDRESSBOOK__, 'guestmode',       $guestmode);
    xarModSetVar(__ADDRESSBOOK__, 'usermode',        $usermode);

    xarModSetVar(__ADDRESSBOOK__, 'abtitle',         $abtitle);

    if ($sortdata_1 == $sortdata_2) {
        if (isset($error)) {
            $error .= '<br>'.xarVarPrepHTMLDisplay(_AB_SORTERROR_1);
        }
        else {
            $error = xarVarPrepHTMLDisplay(_AB_SORTERROR_1);
        }
    }
    else {
        $s_1 = $sortdata_1.','.$sortdata_2;
        xarModSetVar(__ADDRESSBOOK__, 'sortorder_1', $s_1);
    }
    if ($sortdata_3 == $sortdata_4) {
        if (isset($error)) {
            $error .= '<br>'.xarVarPrepHTMLDisplay(_AB_SORTERROR_2);
        }
        else {
            $error = xarVarPrepHTMLDisplay(_AB_SORTERROR_2);
        }
    }
    else {
        $s_2 = $sortdata_3.','.$sortdata_4;
        xarModSetVar(__ADDRESSBOOK__, 'sortorder_2', $s_2);
    }

    xarModSetVar(__ADDRESSBOOK__, 'name_order',      $name_order);

    if (strlen($special_chars_1) != strlen($special_chars_2)) {
        $error .= '<br>'.xarVarPrepHTMLDisplay(_AB_SPECIAL_CHARS_ERROR);
    }
    else {
        xarModSetVar(__ADDRESSBOOK__, 'special_chars_1', $special_chars_1);
        xarModSetVar(__ADDRESSBOOK__, 'special_chars_2', $special_chars_2);
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


    $msg = xarVarPrepHTMLDisplay(_AB_CONF_AB_SUCCESS);
    if (isset($error)) { $msg .= ' - '.$error; }

    $args=array('msg'=>$msg);

    xarSessionDelVar('statusmsg');
    // Lets Explain why the form was sent back
//    xarSessionSetVar('statusmsg', xarMLByKey('EXAMPLEERRORINFORM'));
    xarSessionSetVar('statusmsg', xarML($msg));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
//    pnRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'main',$args));
    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifyconfig'));

    // Return
    return true;

} // END updateconfig

//=========================================================================
//  Category form
//=========================================================================
function AddressBook_admin_modifycategories($args) {

    // Security check
    if (!xarSecurityCheck('ModifyCategories',0)) {
        return;
    }

    /**
     * How are we handling display of app messages & exceptions?? / should go here @ top
     */

    // get the list of categories
    $output['categories'] = xarModAPIFunc(__ADDRESSBOOK__,'admin','getItems',array('tablename'=>'categories'));

    // Generate a one-time authorisation code for this operation
    $output['authid'] = xarSecGenAuthKey();

    // Submit button
    $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML(_AB_ADDRESSBOOKUPDATE));

    return $output;

} // END modifycategories

//=========================================================================
//  Category update
//=========================================================================
function AddressBook_admin_updatecategories() {

    if (!xarSecConfirmAuthKey()) return;

    // Security check
    if (!xarSecurityCheck('ModifyCategories',0)) {
        return;
    }

    $id = xarVarCleanFromInput ('id'); //gehQ - how do we use xarVarFetch with arrays?
    $del = xarVarCleanFromInput ('del'); //gehQ - how do we use xarVarFetch with arrays?
    $name = xarVarCleanFromInput ('name'); //gehQ - how do we use xarVarFetch with arrays?

    if (!xarVarFetch ('newname','str::40',$newname, XARVAR_NOT_REQUIRED)) return;

    if(is_array($del)) {
        $dels = implode(',',$del);
    }

    $modID = $modName = array();

    if(isset($id)) {
        foreach($id as $k=>$i) {
            $found = false;
            if(count($del)) {
                foreach($del as $d) {
                    if($i == $d) {
                        $found = true;
                        break;
                    }
                }
            }
            if(!$found) {
                array_push($modID,$i);
                array_push($modName,$name[$k]);
            }
        }
    }

    $xarTables = xarDBGetTables();
    $cat_table = $xarTables['addressbook_categories'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,"UPDATE $cat_table
                            SET name ='".xarVarPrepForStore($modName[$k])."'
                          WHERE nr = $id");
    }

    $error = '';

//    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateCategories',array('updates'=>$updates))) {
    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateItems',array('tablename'=>'categories','updates'=>$updates))) {
        if (empty($error)) { $error .= 'UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        else { $error .= ' - UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
    }

    if(isset($dels)) {
        $delete = "DELETE FROM $cat_table WHERE nr IN ($dels)";
//        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteCategories',array('delete'=>$delete))) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteItems',array('tablename'=>'categories','delete'=>$delete))) {
            if (empty($error)) { $error .= 'DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
            else { $error .= ' - DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','addItems',array('tablename'=>'categories','name'=>$newname))) {
            if (empty($error)) { $error .= 'INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
            else { $error .= ' - INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        }
    }

    $args=array('msg'=>$error);

    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifycategories',$args));

    // Return
    return true;

} // END updatecategories

//=========================================================================
//  Custom field form
//=========================================================================
function AddressBook_admin_modifycustomfields($args) {

    // Security check
    if (!xarSecurityCheck('ModifyCustomFields',0)) {
        return;
    }

// where messages go / should handle as exceptions?? geh
//    $msg = xarVarCleanFromInput('msg');
//    if ($msg) { $output->Text('<div align="center">'.$msg.'</div>');}
//    else {$output->Linebreak(1); }

    $output['modName'] = __ADDRESSBOOK__; //gehDEBUG - better way to display modName??

    $output['custfields'] = xarModAPIFunc(__ADDRESSBOOK__,'admin','getCustomfields');
    if(!is_array($output['custfields'])) {
        return $output;
    }

    //gehINFO - this should be in a table & configurable
    $output['datatypes'][] = array('id'=>'varchar(60) default NULL',    'name'=>' Text, 60 chars, 1 line');
    $output['datatypes'][] = array('id'=>'varchar(120) default NULL',   'name'=>'Text, 120 chars, 2 lines');
    $output['datatypes'][] = array('id'=>'varchar(240) default NULL',   'name'=>'Text, 240 chars, 4 lines');
    $output['datatypes'][] = array('id'=>'int default NULL',            'name'=>'Integer numbers');
    $output['datatypes'][] = array('id'=>'decimal(10,2) default NULL',  'name'=>'Decimal numbers');
    $output['datatypes'][] = array('id'=>'int(1) default NULL',         'name'=>'Checkbox');
    $output['datatypes'][] = array('id'=>'date default NULL',           'name'=>'Date');
    $output['datatypes'][] = array('id'=>'tinyint default NULL',        'name'=>'Blank line');
    $output['datatypes'][] = array('id'=>'smallint default NULL',       'name'=>'Horizontal rule');
/*
    foreach($custs as $cust) {
        $output->TableRowStart();
        $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
        $output->FormText('name[]',$cust['name'],20,30);
        $output->TableColEnd();
        $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
        $output->FormSelectMultiple('cus_type[]',$formdata, 0, 1, $cust['type'], '');
        $output->TableColEnd();
        $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
        $output->FormHidden('id[]',$cust['nr']);
        if ($cust['nr'] < 5) {
            $output->FormHidden('del[]',false);
        }
        else {
            $output->FormCheckbox('del[]',false,$cust['nr']);
        }
        $output->TableColEnd();
        $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
        if ($cust['position'] == '1') {
            $down = $output->URL(xarModURL(__ADDRESSBOOK__,'admin','decCustomfields',array('id' => $cust['nr'],'authid' => $authid)),'<img src="modules/'.__ADDRESSBOOK__.'/pnimages/down.gif" alt="'.xarVarPrepHTMLDisplay(_AB_DOWN).'" border="0" hspace="4">');

        }
        else {
            if ($cust['position'] == sizeof($custs)) {
                $up = $output->URL(xarModURL(__ADDRESSBOOK__,'admin','incCustomfields',array('id' => $cust['nr'],'authid' => $authid)),'<img src="modules/'.__ADDRESSBOOK__.'/pnimages/up.gif" alt="'.xarVarPrepHTMLDisplay(_AB_UP).'" border="0" hspace="4">');
            }
            else {
                $up = $output->URL(xarModURL(__ADDRESSBOOK__,'admin','incCustomfields',array('id' => $cust['nr'],'authid' => $authid)),'<img src="modules/'.__ADDRESSBOOK__.'/pnimages/up.gif" alt="'.xarVarPrepHTMLDisplay(_AB_UP).'" border="0" hspace="4">');
                $down = $output->URL(xarModURL(__ADDRESSBOOK__,'admin','decCustomfields',array('id' => $cust['nr'],'authid' => $authid)),'<img src="modules/'.__ADDRESSBOOK__.'/pnimages/down.gif" alt="'.xarVarPrepHTMLDisplay(_AB_DOWN).'" border="0" hspace="4">');
            }
        }
        $output->TableColEnd();
        $output->TableRowEnd();
    }

    $output->TableRowStart();
    $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
    $output->FormText('newname','',20,30);
    $output->TableColEnd();
    $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
    $output->FormSelectMultiple('newtype',$formdata, 0, 1,'', '');
    $output->TableColEnd();
    $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'">');
    $output->Text(_AB_CAT_NEW);
    $output->TableColEnd();
    $output->Text('<td align="center" valign="middle" bgcolor="'.$bc1.'"><br>');
    $output->TableColEnd();
    $output->TableRowEnd();
*/

    // Generate a one-time authorisation code for this operation
    $output['authid'] = xarSecGenAuthKey();

    // Submit button
    $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML(_AB_ADDRESSBOOKUPDATE));

    return $output;

} // END modifycustomfields

//=========================================================================
//  Custom field update
//=========================================================================

function AddressBook_admin_updatecustomfields() {

    if (!xarSecConfirmAuthKey()) return;

    // Security check
    if (!xarSecurityCheck('AdminAddressBook',0)) {
        return;
    }

	list($id,$del,$name,$cust_type,$newname,$newtype) = xarVarCleanFromInput('id','del','name','cust_type','newname','newtype');
	if(is_array($del)) {
        $dels = implode(',',$del);
    }
	$modID = $modName = array();
	$modType = array();
	$modDel = array();
	$modDelType = array();

	if(isset($id)) {
		foreach($id as $k=>$datatype) {
        	$found = false;
        	if(count($del)) {
            	foreach($del as $d) {
                    if($datatype == $d) {
                    	$found = true;
                        array_push($modDel,$datatype);
						array_push($modDelType,$cust_type[$k]);
                    	break;
                	}
            	}
        	}
        	if(!$found) {
                array_push($modID,$datatype);
            	array_push($modName,$name[$k]);
				array_push($modType,$cust_type[$k]);
            }
    	}
	}
	$xarTables = xarDBGetTables();
	$cus_table = $xarTables['addressbook_customfields'];
	$adr_table = $xarTables['addressbook_address'];

	$updates = array();

	foreach($modID as $k=>$id) {
    	array_push($updates,"UPDATE $cus_table
                                SET label='".xarVarPrepForStore($modName[$k])."',
							        type='".xarVarPrepForStore($modType[$k])."'
                              WHERE nr=$id");
		if (($modType[$k] != 'smallint default NULL') && ($modType[$k] != 'tinyint default NULL')) {
			array_push($updates,"ALTER TABLE $adr_table CHANGE custom_".$id." custom_".$id." ".xarVarPrepForStore($modType[$k]));
		}
	}

	$error = '';

	if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateCustomfields',array('updates'=>$updates))) {
    	if (empty($error)) { $error .= 'UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
		else { $error .= ' - UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
	}

	if (count($modDel)) {
		$deletes = array();
		foreach($modDel as $k=>$id) {
				array_push($deletes,"DELETE FROM $cus_table WHERE nr = $id");
				if (($modDelType[$k] != 'smallint default NULL') && ($modDelType[$k] != 'tinyint default NULL')) {
					array_push($deletes,"ALTER TABLE $adr_table DROP custom_".$id);
				}
		}
    	if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteCustomfields',array('deletes'=>$deletes))) {
    		if (empty($error)) { $error .= 'DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
			else { $error .= ' - DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
    	}
	}
	if (isset($newtype) && ($newtype == 'tinyint default NULL')) {
		$newname = '[      ]';
	}
	if (isset($newtype) && ($newtype == 'smallint default NULL')) {
		$newname = '[------]';
	}
	if( (isset($newname)) && ($newname != '') ) {
		list($dbconn) = xarDBGetConn();
		$result = $dbconn->Execute("SELECT MAX(nr) FROM $cus_table");
		list($nextID) = $result->fields;
		$nextID++;
        $result->Close();
		$inserts = array();
		array_push($inserts,"INSERT INTO $cus_table (nr,name,type,position)
                              VALUES ($nextID,'".xarVarPrepForStore($newname)."','".xarVarPrepForStore($newtype)."',9999999999)");
		if (($newtype != 'smallint default NULL') && ($newtype != 'tinyint default NULL')) {
			array_push($inserts,"ALTER TABLE $adr_table ADD custom_".$nextID." ".xarVarPrepForStore($newtype));
		}

        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','addCustomfields',array('inserts'=>$inserts))) {
            if (empty($error)) { $error .= 'INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
			else { $error .= ' - INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
		}
    }

	$args=array('msg'=>$error);

    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifycustomfields',$args));
	return true;
}
//=========================================================================
//  Decrement position for a custom field
//=========================================================================
function AddressBook_admin_decCustomfields()
{
    // Get parameters
    $id = xarVarCleanFromInput('id');

    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc(__ADDRESSBOOK__, 'admin', 'decCustomFields', array('id' => $id))) {
        // Success
        //pnSessionSetVar('statusmsg', _BLOCKLOWER);
    }

    // Redirect
    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifycustomfields'));
    return true;
}
//=========================================================================
//  Increment position for a custom field
//=========================================================================
function AddressBook_admin_incCustomfields()
{
    // Get parameters
    $id = xarVarCleanFromInput('id');

    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (xarModAPIFunc(__ADDRESSBOOK__, 'admin', 'incCustomFields', array('id' => $id))) {
        // Success
        //pnSessionSetVar('statusmsg', _BLOCKLOWER);
    }

    // Redirect
    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifycustomfields'));
    return true;
}
//=========================================================================
//  Prefix field form
//=========================================================================
function AddressBook_admin_modifyprefixes($args) {

    // Security check
    if (!xarSecurityCheck('ModifyLabels',0)) {
        return;
    }

    /**
     * How are we handling display of app messages & exceptions?? / should go here @ top
     */

    // get the list of categories
    $output['prefixes'] = xarModAPIFunc(__ADDRESSBOOK__,'admin','getItems',array('tablename'=>'prefixes'));

    // Generate a one-time authorisation code for this operation
    $output['authid'] = xarSecGenAuthKey();

    // Submit button
    $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML(_AB_ADDRESSBOOKUPDATE));

    return $output;

} // END modifylabels

function AddressBook_admin_updateprefixes() {

    if (!xarSecConfirmAuthKey()) return;

    // Security check
    if (!xarSecurityCheck('ModifyCategories',0)) {
        return;
    }

    $id = xarVarCleanFromInput ('id'); //gehQ - how do we use xarVarFetch with arrays?
    $del = xarVarCleanFromInput ('del'); //gehQ - how do we use xarVarFetch with arrays?
    $name = xarVarCleanFromInput ('name'); //gehQ - how do we use xarVarFetch with arrays?

    if (!xarVarFetch ('newname','str::40',$newname, XARVAR_NOT_REQUIRED)) return;

    if(is_array($del)) {
        $dels = implode(',',$del);
    }

    $modID = $modName = array();

    if(isset($id)) {
        foreach($id as $k=>$i) {
            $found = false;
            if(count($del)) {
                foreach($del as $d) {
                    if($i == $d) {
                        $found = true;
                        break;
                    }
                }
            }
            if(!$found) {
                array_push($modID,$i);
                array_push($modName,$name[$k]);
            }
        }
    }

    $xarTables = xarDBGetTables();
    $cat_table = $xarTables['addressbook_prefixes'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,"UPDATE $cat_table
                            SET name ='".xarVarPrepForStore($modName[$k])."'
                          WHERE nr = $id");
    }

    $error = '';

    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateItems',array('tablename'=>'prefixes','updates'=>$updates))) {
        if (empty($error)) { $error .= 'UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        else { $error .= ' - UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
    }

    if(isset($dels)) {
        $delete = "DELETE FROM $cat_table WHERE nr IN ($dels)";
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteItems',array('tablename'=>'prefixes','delete'=>$delete))) {
            if (empty($error)) { $error .= 'DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
            else { $error .= ' - DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','addItems',array('tablename'=>'prefixes','name'=>$newname))) {
            if (empty($error)) { $error .= 'INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
            else { $error .= ' - INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        }
    }

    $args=array('msg'=>$error);

    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifyprefixes',$args));

    // Return
    return true;

} // END updateprefixes

//=========================================================================
//  Label form
//=========================================================================
function AddressBook_admin_modifylabels($args) {

    // Security check
    if (!xarSecurityCheck('ModifyLabels',0)) {
        return;
    }

    /**
     * How are we handling display of app messages & exceptions?? / should go here @ top
     */

    // get the list of categories
    $output['labels'] = xarModAPIFunc(__ADDRESSBOOK__,'admin','getItems',array('tablename'=>'labels'));

    // Generate a one-time authorisation code for this operation
    $output['authid'] = xarSecGenAuthKey();

    // Submit button
    $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML(_AB_ADDRESSBOOKUPDATE));

    return $output;

} // END modifylabels

function AddressBook_admin_updatelabels() {

    if (!xarSecConfirmAuthKey()) return;

    // Security check
    if (!xarSecurityCheck('ModifyCategories',0)) {
        return;
    }

    $id = xarVarCleanFromInput ('id'); //gehQ - how do we use xarVarFetch with arrays?
    $del = xarVarCleanFromInput ('del'); //gehQ - how do we use xarVarFetch with arrays?
    $name = xarVarCleanFromInput ('name'); //gehQ - how do we use xarVarFetch with arrays?

    if (!xarVarFetch ('newname','str::40',$newname, XARVAR_NOT_REQUIRED)) return;

    if(is_array($del)) {
        $dels = implode(',',$del);
    }

    $modID = $modName = array();

    if(isset($id)) {
        foreach($id as $k=>$i) {
            $found = false;
            if(count($del)) {
                foreach($del as $d) {
                    if($i == $d) {
                        $found = true;
                        break;
                    }
                }
            }
            if(!$found) {
                array_push($modID,$i);
                array_push($modName,$name[$k]);
            }
        }
    }

    $xarTables = xarDBGetTables();
    $cat_table = $xarTables['addressbook_labels'];

    $updates = array();
    foreach($modID as $k=>$id) {
    array_push($updates,"UPDATE $cat_table
                            SET name ='".xarVarPrepForStore($modName[$k])."'
                          WHERE nr = $id");
    }

    $error = '';

    if(xarModAPIFunc(__ADDRESSBOOK__,'admin','updateItems',array('tablename'=>'labels','updates'=>$updates))) {
        if (empty($error)) { $error .= 'UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        else { $error .= ' - UPDATE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
    }

    if(isset($dels)) {
        $delete = "DELETE FROM $cat_table WHERE nr IN ($dels)";
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','deleteItems',array('tablename'=>'labels','delete'=>$delete))) {
            if (empty($error)) { $error .= 'DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
            else { $error .= ' - DELETE '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        }
    }

    if( (isset($newname)) && ($newname != '') ) {
        if(xarModAPIFunc(__ADDRESSBOOK__,'admin','addItems',array('tablename'=>'labels','name'=>$newname))) {
            if (empty($error)) { $error .= 'INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
            else { $error .= ' - INSERT '.xarVarPrepHTMLDisplay(_AB_SUCCESS); }
        }
    }

    $args=array('msg'=>$error);

    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'admin', 'modifylabels',$args));

    // Return
    return true;

} // END updatelabels

?>