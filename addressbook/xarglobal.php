<?php
/**
 * File: $Id: xarglobal.php,v 1.8 2003/07/19 06:18:28 garrett Exp $
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

/**
 * User Messages
 */
define('_AB_NOAUTH_FUNCTION',       'You are not authorized to perform this function');

//from admin.php
//////////////////////////////
define('_AB_NORECORDS',             'There are no records to show in this view');
define('_ADDRESSBOOK_NOAUTH',       'Not authorised to access the AddressBook module.');
define('_ADDRESSBOOK_TITLE',        'Title of this Address Book');
define('_AB_INSERT_AB_SUCCESS',     'Address Book Entry saved!');
define('_AB_INSERT_CHKMSG',         'An Address Book Entry must contain data in at least one field of the Name tab!');
define('_AB_INSERT_ERROR',          'An Error ocurred. The Address Book Entry could not be saved!');

define('_AB_ADDRESSBOOKUPDATE', 'Commit Changes');
define('_AB_CAT_DELETE',            'Delete');
define('_AB_CAT_NAME',          'Category');
define('_AB_CAT_NEW',               '<= New');
define('_AB_CONF_AB_SUCCESS',   'Configuration saved!');
define('_AB_CREATE',                'Create');
define('_AB_CREATE_CONTACT',        'Add');
define('_AB_CUSTOMLABEL',           'Custom Label');
define('_AB_CUSTOM_DATEFORMAT', 'Format for date entries');
define('_AB_CUSTOM_NUMFORMAT',  'Format for numeric values');
define('_AB_CUSTOM_TAB',            'Custom tab (if empty, no custom fields are displayed');
define('_AB_CUSTOM_TEXTAREAWIDTH','Width of TEXTAREA fields');
define('_AB_DATATYPE',          'Data Type');
define('_AB_DOWN',              'down');
define('_AB_EDITDELETE',            'Edit/Delete');
define('_AB_EDIT_AB_CATEGORY',  'Categories');
define('_AB_EDIT_CONFIG',       'Settings');
define('_AB_EDIT_CUSTOM',           'Custom Fields');
define('_AB_EDIT_LABEL',            'Labels');
define('_AB_EDIT_PREFIX',           'Prefix');
define('_AB_GLOBALPROTECT',     'Personal address book mode');
define('_AB_GLOBALPROTECTERROR',    'Corrected: In personal address book mode guests have no and registered user have full access rights!!!');
define('_AB_GRANTERROR',            'Corrected: The access rights of guest were higher than for registered users!!!');
define('_AB_GRANTLEVEL',            'Access level');
define('_AB_GRANTNOTE',             'Note: Users can ONLY edit/delete records which they have created!');
define('_AB_GUESTMODE',             'Grant rights for an unregistered user');
define('_AB_HIDEALL'    ,           'Disabled for all');
define('_AB_HIDECOPYRIGHT',     'Hide the Copyright and Version Note');
define('_AB_HIDEGUESTS',            'Disabled only for guests');
define('_AB_HIDENOTHING',           'Enabled for all');
define('_AB_ITEMSPERPAGE',      'Records viewed per page');
define('_AB_LAB_DELETE',            'Delete');
define('_AB_LAB_NAME',          'Contact Label');
define('_AB_LAB_NEW',               '<= New');
define('_AB_MENU_OFF',          'Disable the menu');
define('_AB_MENU_SEMI',         'Disable the second line of the main menu');
define('_AB_NAME_ORDER',            'Name display in list view and sort order for name information');
define('_AB_NOPREFIX',          'No Prefix/Title');
define('_AB_ORDER',             'Order');
define('_AB_OTHERSTUFF',            'Other settings');
define('_AB_SAVE_RECORD',           'Save Record');
define('_AB_SHOWIMG',               'Do you want to use images/logos?');
define('_AB_SHOWPREFIX',            'Do you want to use a prefix field?');
define('_AB_SORTERROR_1',           'Equal columns selected / Default sort order was not changed!');
define('_AB_SORTERROR_2',           'Equal columns selected / Alternate sort order was not changed!');
define('_AB_SORTORDER_1',           'Default sort order / List View');
define('_AB_SORTORDER_2',           'Alternate sortorder / List View');

define('_AB_SO_FIRSTNAME',          'First name');
define('_AB_SO_LASTNAME',           'Last name');
define('_AB_SPECIAL_CHARS',         'Special character (Umlauts) replacement for sort columns');
define('_AB_SPECIAL_CHARS_ERROR',   'Both fields must contain the same number of characters - Special character replacement was NOT saved!');

define('_AB_SUCCESS',               'successful');
define('_AB_UP',                    'up');
define('_AB_USERMODE',          'Grant right for registered users');
define('_AB_VIEW',              'View');
define('_AB_ZIPBEFORECITY',     'Show zip before city');

////////////////
// User Defines
define('_AB_ALLCATEGORIES',     'All Categories');
define('_AB_CANCEL',            "Cancel");
define('_AB_CONFIRMDELETE',     "Delete this Address Book item?");
define('_AB_CONTACTINFO',       "General Information");
define('_AB_COPY',              "Copy to clipboard");
define('_AB_DELETE',            "Delete");
define('_AB_DELETENOSUCCESS',   "Deletion of this record failed. Please contact your administrator!");
define('_AB_GOBACK',            "Back to list");
define('_AB_INSERT_RECORD',     " Save ");
define('_AB_LASTCHANGED',       'Last changed ');
define('_AB_NOIMAGE',           'No Image');
define('_AB_NUM_COLS',          4);
define('_AB_REGONLY',           "This website require it's users to be registered to use the address book.<br /> Register for free <a href=\"user.php\">here</a>, or <a href=\"user.php\">log in</a> if you are already registered.");
define('_AB_LABEL_NAME',        "NAME");
define('_AB_TEMPLATE_ADDR',   1);
define('_AB_TEMPLATE_CONTACT', 2);
define('_AB_TEMPLATE_CUST',   3);
define('_AB_TEMPLATE_NAME',   0);
define('_AB_TEMPLATE_NOTE',   4);
define('_AB_UPDATE_ERROR',  'An Error ocurred. The Address Book Entry could not be updated!');
define('_AB_UPDATE_RECORD',  "Update");

define('_AB_ERRMSG_MISFIELDS_NAME_TAB', 'An Address Book Entry must contain data in at least one field of the Name tab!');
define('_AB_ERRMSG_FALSENUM_CUST_TAB',  'There is a false numeric value in the '.xarModGetVar(__ADDRESSBOOK__,'custom_tab').' tab.');
define('_AB_ERRMSG_INVALNUM_CUST_TAB',  'In the '.xarModGetVar(__ADDRESSBOOK__,'custom_tab').' tab there are characters in a digit-only field.');
define('_AB_ERRMSG_INVALDATE_CUST_TAB', 'In the '.xarModGetVar(__ADDRESSBOOK__,'custom_tab').' tab there is a false date format.');



////////////////

/**
 * Navigation Labels
 */
define('_AB_VIEWPRIVATE',           'Show private contacts only');
define('_AB_MENU_AZ',               'Show A - Z');
define('_AB_MENU_ALL',              'Show all records');
define('_AB_MENU_ADD',              'Add new address');
define('_AB_MENU_SEARCH',           'Search');
define('_AB_SORTBY',                'Sort&nbsp;by');
define('_AB_LABEL_DELETE',          "Delete");
define('_AB_LABEL_EDIT',            "Edit");
define('_AB_LABEL_SHOWDETAIL',      "Details");

/**
 * Results list headings
 */
define('_AB_LABEL_ACTION',          "ACTION");
define('_AB_LABEL_COMPANY',         "COMPANY");
define('_AB_LABEL_CONTACT',         "CONTACT");

/**
 * Form Labels
 */
// Name page
define('_AB_NAME',              'Name');
define('_AB_PREFIXLABEL',       'Prefix/Title');
define('_AB_LABEL_FIRSTNAME',   'First&nbsp;name'); //&nbsp; keeps label from being split up
define('_AB_LABEL_LASTNAME',    'Last&nbsp;name');
define('_AB_TITLE',             'Title');
define('_AB_COMPANY',           'Company');
define('_AB_IMAGE',             'Image');
define('_AB_ALLCOMPANIES',      'Enter new company name or select a company...');

// Address page
define('_AB_ADDRESS',           'Address');
define('_AB_CITY',              'City');
define('_AB_STATE',             'State');
define('_AB_ZIP',               'Zip');
define('_AB_COUNTRY',           'Country');

// Contact page
define('_AB_CONTACT',           'Contact');

// Note page
define('_AB_NOTETAB',           'Note');

// Fields displayed across pages
define('_AB_CATEGORY',          'Category');
define('_AB_UNFILED',           'Unfiled'); // unfiled category
define('_AB_PRIVATE',           'Private');

/**
 * Defaults used in the module
 */
//Default Custom Field Labels
define('_AB_CUSTOM_1',          'Custom Label 1'); //admin
define('_AB_CUSTOM_2',          'Custom Label 2'); //admin
define('_AB_CUSTOM_3',          'Custom Label 3'); //admin
define('_AB_CUSTOM_4',          'Custom Label 4'); //admin

// Default Contact Labels
define('_AB_EMAIL',             'E-Mail');  //admin
define('_AB_FAX',               'Fax');     //admin
define('_AB_HOME',              'Home');    //admin
define('_AB_MOBILE',            'Mobile');  //admin
define('_AB_OTHER',             'Other');   //admin
define('_AB_URL',               'URL');     //admin
define('_AB_WORK',              'Work');    //admin

// Default Categories
define('_AB_BUSINESS',          'Business');    //admin
define('_AB_PERSONAL',          'Personal');    //admin
define('_AB_QUICKLIST',         'Quicklist');   //admin;

// Default Prefixes
define('_AB_MR',                'Mr.');
define('_AB_MRS',               'Mrs.');

/**
 * Developer QA Contact information
 */
define('_AB_DEVQA_NAME',	__ADDRESSBOOK__."QualityControl");
define('_AB_DEVQA_EMAIL',	__ADDRESSBOOK__."@blacktower.com");
// I use this to track individual builds in the event I push files that
// do not require the user to upgrade
define('_AB_BUILD_VER',	  '1.2.1.4'); 

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
