<?php
/**
 * File: $Id: xarglobal.php,v 1.10 2003/09/15 00:29:58 garrett Exp $
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


// Gives a common control across the module in the strange event our external mod name needs to change
define('__ADDRESSBOOK__', 'addressbook');

// FIXME: <garrett> Waiting for xarLocale* funcs to be completed
// borrowed from PostNuke NS-Languages/api.php / Timezone Function by Fred B (fredb86)
define('_DAY_OF_WEEK_LONG','Sunday Monday Tuesday Wednesday Thursday Friday Saturday');
define('_DAY_OF_WEEK_SHORT','Sun Mon Tue Wed Thu Fri Sat');
define('_MONTH_LONG','January February March April May June July August September October November December');
define('_MONTH_SHORT','Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_TIMEZONES','IDLW NT HST YST PST MST CST EST AST GMT-3:30 GMT-3 AT WAT GMT CET EET BT GMT+3:30 GMT+4 GMT+4:30 GMT+5 GMT+5:30 GMT+6 WAST CCT JST ACS GST GMT+11 NZST');
define('_TZOFFSETS','0 1 2 3 4 5 6 7 8 8.5 9 10 11 12 13 14 15 15.5 16 16.5 17 17.5 18 19 20 21 21.5 22 23 24');
// END FIXME

/**
 * Used in xarinit
 */
define('_AB_INIT_CREATETABLEFAILED', 'Table creation failed');
define('_AB_INIT_UPDATETABLEFAILED', 'Table update failed');
define('_AB_INIT_DELETETABLEFAILED', 'Table deletion failed.');

// Module Variables: key = modvar name / value = modvar value
// must be set but xarModVarSet
$abModVars = array ('abtitle'           => 'Xaraya Address Book'
                   ,'guestmode'         => 1
                   ,'usermode'          => 7
                   ,'itemsperpage'      => 30
                   ,'globalprotect'     => 0
                   ,'menu_off'          => 0
                   ,'custom_tab'        => ''
                   ,'zipbeforecity'     => 0
                   ,'hidecopyright'     => 0
                   ,'use_prefix'        => 0
                   ,'use_img'           => 0
                   ,'textareawidth'     => 60
                   ,'dateformat'        => 0
                   ,'numformat'         => '9,999.99'
                   ,'sortorder_1'       => 'sortname,sortcompany'
                   ,'sortorder_2'       => 'sortcompany,sortname'
                   ,'menu_semi'         => 0
                   ,'name_order'        => 0
                   ,'special_chars_1'   => 'ÄÖÜäöüß'
                   ,'special_chars_2'   => 'AOUaous'
                   ,'SupportShortURLs'  => 0
                   ,'rptErrAdminFlag'   => 1
                   ,'rptErrAdminEmail'  => xarModGetVar('mail','adminmail')
                   ,'rptErrDevFlag'     => 1
                   );

/**
 * Custom Field Defines
 */
define('_AB_HTML_LINEBREAK',         '<br />');
define('_AB_HTML_HORIZRULE',         '<hr />');


/**
 * which part of the Field Type should be retrieved
 */
define('_AB_CUST_ALLFIELDINFO',     1);
define('_AB_CUST_UDCOLNAMESONLY',   2); // user data column names only
define('_AB_CUST_UDCOLANDLABELS',   3); // user data custom field labels & column names
define('_AB_CUST_ALLINFO',          4);

define('_AB_CUST_COLPREFIX',        'custom_');
define('_AB_CUST_TEST_LB',          'tinyint');
define('_AB_CUST_TEST_HR',          'smallint');
define('_AB_CUST_TEST_STRING',      'varchar');

define('_AB_DATEFORMAT_1',      'MM.DD.YYYY');
define('_AB_DATEFORMAT_2',      'DD.MM.YYYY');

/* end custom field defines */

/**
 * Exception Handling
 */
// User Exceptions:
define('_AB_ERR_NOERR',          0);
define('_AB_ERR_INFO',           1);
define('_AB_ERR_WARN',           2);
define('_AB_ERR_ERROR',          3);
define('_AB_ERR_DEBUG',          4);

/**
 * Control exception display format
 */
define('_AB_ERR_INFO_STYLE',     '');
define('_AB_ERR_WARN_STYLE',     '');
define('_AB_ERR_ERROR_STYLE',    '');
define('_AB_ERR_DEBUG_STYLE',    '');

/* end exception handling */

//from admin.php
//////////////////////////////
define('_AB_NORECORDS',             xarML('There are no records to show in this view'));
define('_ADDRESSBOOK_NOAUTH',       xarML('Not authorised to access the AddressBook module.'));
define('_ADDRESSBOOK_TITLE',        xarML('Title of this Address Book'));
define('_AB_INSERT_AB_SUCCESS',     xarML('Address Book Entry saved!'));
define('_AB_INSERT_CHKMSG',         xarML('An Address Book Entry must contain data in at least one field of the Name tab!'));
define('_AB_INSERT_ERROR',          xarML('An Error ocurred. The Address Book Entry could not be saved!'));

define('_AB_ADDRESSBOOKUPDATE', xarML('Commit Changes'));
define('_AB_CAT_DELETE',            xarML('Delete'));
define('_AB_CAT_NAME',          xarML('Category'));
define('_AB_CAT_NEW',               '<= ' . xarML('New'));
define('_AB_CONF_AB_SUCCESS',   xarML('Configuration saved!'));
define('_AB_CREATE',                xarML('Create'));
define('_AB_CREATE_CONTACT',        xarML('Add'));
define('_AB_CUSTOMLABEL',           xarML('Custom Label'));
define('_AB_CUSTOM_DATEFORMAT', xarML('Format for date entries'));
define('_AB_CUSTOM_NUMFORMAT',  xarML('Format for numeric values'));
define('_AB_CUSTOM_TAB',            xarML('Custom tab (if empty, no custom fields are displayed'));
define('_AB_CUSTOM_TEXTAREAWIDTH',xarML('Width of TEXTAREA fields'));
define('_AB_DATATYPE',          xarML('Data Type'));
define('_AB_DOWN',              xarML('down'));
define('_AB_EDITDELETE',            xarML('Edit/Delete'));
define('_AB_EDIT_AB_CATEGORY',  xarML('Categories'));
define('_AB_EDIT_CONFIG',       xarML('Settings'));
define('_AB_EDIT_CUSTOM',           xarML('Custom Fields'));
define('_AB_EDIT_LABEL',            xarML('Labels'));
define('_AB_EDIT_PREFIX',           xarML('Prefix'));
define('_AB_GLOBALPROTECT',     xarML('Personal address book mode'));
define('_AB_GLOBALPROTECTERROR',    xarML('Corrected: In personal address book mode guests have no and registered user have full access rights!!!'));
define('_AB_GRANTERROR',            xarML('Corrected: The access rights of guest were higher than for registered users!!!'));
define('_AB_GRANTLEVEL',            xarML('Access level'));
define('_AB_GRANTNOTE',             xarML('Note: Users can ONLY edit/delete records which they have created!'));
define('_AB_GUESTMODE',             xarML('Grant rights for an unregistered user'));
define('_AB_HIDEALL'    ,           xarML('Disabled for all'));
define('_AB_HIDECOPYRIGHT',     xarML('Hide the Copyright and Version Note'));
define('_AB_HIDEGUESTS',            xarML('Disabled only for guests'));
define('_AB_HIDENOTHING',           xarML('Enabled for all'));
define('_AB_ITEMSPERPAGE',      xarML('Records viewed per page'));
define('_AB_LAB_DELETE',            xarML('Delete'));
define('_AB_LAB_NAME',          xarML('Contact Label'));
define('_AB_LAB_NEW',               '<= ' . xarML('New'));
define('_AB_MENU_OFF',          xarML('Disable the menu'));
define('_AB_MENU_SEMI',         xarML('Disable the second line of the main menu'));
define('_AB_NAME_ORDER',            xarML('Name display in list view and sort order for name information'));
define('_AB_NOPREFIX',          xarML('No Prefix/Title'));
define('_AB_ORDER',             xarML('Order'));
define('_AB_OTHERSTUFF',            xarML('Other settings'));
define('_AB_SAVE_RECORD',           xarML('Save Record'));
define('_AB_SHOWIMG',               xarML('Do you want to use images/logos?'));
define('_AB_SHOWPREFIX',            xarML('Do you want to use a prefix field?'));
define('_AB_SORTERROR_1',           xarML('Equal columns selected / Default sort order was not changed!'));
define('_AB_SORTERROR_2',           xarML('Equal columns selected / Alternate sort order was not changed!'));
define('_AB_SORTORDER_1',           xarML('Default sort order / List View'));
define('_AB_SORTORDER_2',           xarML('Alternate sortorder / List View'));

define('_AB_SO_FIRSTNAME',          xarML('First name'));
define('_AB_SO_LASTNAME',           xarML('Last name'));
define('_AB_SPECIAL_CHARS',         xarML('Special character (Umlauts) replacement for sort columns'));
define('_AB_SPECIAL_CHARS_ERROR',   xarML('Both fields must contain the same number of characters - Special character replacement was NOT saved!'));

define('_AB_SUCCESS',               xarML('successful'));
define('_AB_UP',                    xarML('up'));
define('_AB_USERMODE',          xarML('Grant right for registered users'));
define('_AB_VIEW',              xarML('View'));
define('_AB_ZIPBEFORECITY',     xarML('Show zip before city'));

////////////////
// User Defines
define('_AB_ALLCATEGORIES',     xarML('All Categories'));
define('_AB_CANCEL',            xarML("Cancel"));
define('_AB_CONFIRMDELETE',     xarML("Delete this Address Book item?"));
define('_AB_CONTACTINFO',       xarML("General Information"));
define('_AB_COPY',              xarML("Copy to clipboard"));
define('_AB_DELETE',            xarML("Delete"));
define('_AB_DELETENOSUCCESS',   xarML( "Deletion of this record failed. Please contact your administrator!"));
define('_AB_GOBACK',            xarML("Back to list"));
define('_AB_INSERT_RECORD',     xarML(" Save "));
define('_AB_LASTCHANGED',       xarML('Last changed '));
define('_AB_NOIMAGE',           xarML('No Image'));
define('_AB_NUM_COLS',          4);
define('_AB_REGONLY',           xarML("This website require it's users to be registered to use the address book.<br /> Register for free <a href=\"user.php\">here</a>, or <a href=\"user.php\">log in</a> if you are already registered."));
define('_AB_LABEL_NAME',        xarML("NAME"));
define('_AB_TEMPLATE_ADDR',   1);
define('_AB_TEMPLATE_CONTACT', 2);
define('_AB_TEMPLATE_CUST',   3);
define('_AB_TEMPLATE_NAME',   0);
define('_AB_TEMPLATE_NOTE',   4);
define('_AB_UPDATE_ERROR',  xarML('An Error ocurred. The Address Book Entry could not be updated!'));
define('_AB_UPDATE_RECORD',  xarML("Update"));

define('_AB_ERRMSG_MISFIELDS_NAME_TAB', xarML('An Address Book Entry must contain data in at least one field of the Name tab!'));
define('_AB_ERRMSG_FALSENUM_CUST_TAB',  xarML('There is a false numeric value in the '.xarModGetVar(__ADDRESSBOOK__,'custom_tab').' tab.'));
define('_AB_ERRMSG_INVALNUM_CUST_TAB',  xarML('In the '.xarModGetVar(__ADDRESSBOOK__,'custom_tab').' tab there are characters in a digit-only field.'));
define('_AB_ERRMSG_INVALDATE_CUST_TAB', xarML('In the '.xarModGetVar(__ADDRESSBOOK__,'custom_tab').' tab there is a false date format.'));



////////////////

/**
 * Navigation Labels
 */
define('_AB_VIEWPRIVATE',           xarML('Show private contacts only'));
define('_AB_MENU_AZ',               xarML('Show A - Z'));
define('_AB_MENU_ALL',              xarML('Show all records'));
define('_AB_MENU_ADD',              xarML('Add new address'));
define('_AB_MENU_SEARCH',           xarML('Search'));
define('_AB_SORTBY',                xarML('Sort&nbsp;by'));
define('_AB_LABEL_DELETE',          xarML("Delete"));
define('_AB_LABEL_EDIT',            xarML("Edit"));
define('_AB_LABEL_SHOWDETAIL',      xarML("Details"));

/**
 * Results list headings
 */
define('_AB_LABEL_ACTION',          xarML("ACTION"));
define('_AB_LABEL_COMPANY',         xarML("COMPANY"));
define('_AB_LABEL_CONTACT',         xarML("CONTACT"));

/**
 * Form Labels
 */
// Name page
define('_AB_NAME',              xarML('Name'));
define('_AB_PREFIXLABEL',       xarML('Prefix/Title'));
define('_AB_LABEL_FIRSTNAME',   xarML('First&nbsp;name')); //&nbsp; keeps label from being split up
define('_AB_LABEL_LASTNAME',    xarML('Last&nbsp;name'));
define('_AB_TITLE',             xarML('Title'));
define('_AB_COMPANY',           xarML('Company'));
define('_AB_IMAGE',             xarML('Image'));
define('_AB_ALLCOMPANIES',      xarML('Enter new company name or select a company...'));

// Address page
define('_AB_ADDRESS',           xarML('Address'));
define('_AB_CITY',              xarML('City'));
define('_AB_STATE',             xarML('State'));
define('_AB_ZIP',               xarML('Zip'));
define('_AB_COUNTRY',           xarML('Country'));

// Contact page
define('_AB_CONTACT',           xarML('Contact'));

// Note page
define('_AB_NOTETAB',           xarML('Note'));

// Fields displayed across pages
define('_AB_CATEGORY',          xarML('Category'));
define('_AB_UNFILED',           xarML('Unfiled')); // unfiled category
define('_AB_PRIVATE',           xarML('Private'));

/**
 * Defaults used in the module
 */
//Default Custom Field Labels
define('_AB_CUSTOM_1',          xarML('Custom Label 1')); //admin
define('_AB_CUSTOM_2',          xarML('Custom Label 2')); //admin
define('_AB_CUSTOM_3',          xarML('Custom Label 3')); //admin
define('_AB_CUSTOM_4',          xarML('Custom Label 4')); //admin

// Default Contact Labels
define('_AB_EMAIL',             xarML('E-Mail'));  //admin
define('_AB_FAX',               xarML('Fax'));     //admin
define('_AB_HOME',              xarML('Home'));    //admin
define('_AB_MOBILE',            xarML('Mobile'));  //admin
define('_AB_OTHER',             xarML('Other'));   //admin
define('_AB_URL',               xarML('URL'));     //admin
define('_AB_WORK',              xarML('Work'));    //admin

// Default Categories
define('_AB_BUSINESS',          xarML('Business'));    //admin
define('_AB_PERSONAL',          xarML('Personal'));    //admin
define('_AB_QUICKLIST',         xarML('Quicklist'));   //admin;

// Default Prefixes
define('_AB_MR',                xarML('Mr.'));
define('_AB_MRS',               xarML('Mrs.'));

/**
 * Developer QA Contact information
 */
define('_AB_DEVQA_NAME',    __ADDRESSBOOK__."QualityControl");
define('_AB_DEVQA_EMAIL',   __ADDRESSBOOK__."@blacktower.com");

////////////////
//
// Class definitions
//
////////////////

class abUserException extends DefaultUserException {

    var $errCollection = array();

    function abUserException ($exc) {

        if (is_object($exc)) {
            if (strtolower(get_class($exc)) == 'errorcollection') {
                $this->errCollection = $exc;
            } else {
                $this->msg = $exc->msg;
                $this->link = $exc->link;
            }
        } else {
            $this->msg = $exc;
        }
    }

    function abExceptionRender ($format = '')
    {
        $text = '';

        if (is_object($this->msg)) {
            ob_start();
            print_r($this->msg);
            $dump = ob_get_contents();
            ob_end_clean();
            $text .= htmlspecialchars($dump);
        } elseif (!empty($this->errCollection)) {
            $text .= $this->errCollection->toString();
        } else {
            $text .= htmlspecialchars($this->msg);
        }

        if ($format == 'html') {
            $text = str_replace (' ','&nbsp;',$text);
            $text = nl2br($text);
        }

        return $text;
    }

} // end abUserException

?>
