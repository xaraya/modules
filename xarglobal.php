<?php
/**
 * AddressBook utility functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Global define function
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
 * Custom Field Defines
 */
define('_AB_HTML_LINEBREAK',         '<br />');
define('_AB_HTML_HORIZRULE',         '<hr />');

/**
 * which part of the Field Type should be retrieved
 */
define('_AB_CUST_ALLFIELDINFO',     1);
define('_AB_CUST_UDCOLNAMESONLY',   2); // user data column names only
define('_AB_CUST_ALLINFO',          4);

define('_AB_DATEFORMAT_1',      'MM.DD.YYYY');
define('_AB_DATEFORMAT_2',      'DD.MM.YYYY');
define('_AB_NULL_DATE',         '0000-00-00');

/* end custom field defines */

/**
 * Display name ordering
 */
define('_AB_NO_LAST_FIRST',      0);
define('_AB_NO_FIRST_LAST',      1);

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

define('_AB_NUM_COLS',         4);
define('_AB_TEMPLATE_ADDR',    1);
define('_AB_TEMPLATE_CONTACT', 2);
define('_AB_TEMPLATE_CUST',    3);
define('_AB_TEMPLATE_NAME',    0);
define('_AB_TEMPLATE_NOTE',    4);

/**
 * Defaults used in the module
 */

//Default Custom Field Types
define('_AB_CUSTOM_TEXT_SHORT', 'varchar(60) default NULL');
define('_AB_CUSTOM_TEXT_MEDIUM','varchar(120) default NULL');
define('_AB_CUSTOM_TEXT_LONG',  'varchar(240) default NULL');
define('_AB_CUSTOM_INTEGER',    'int default NULL');
define('_AB_CUSTOM_DECIMAL',    'decimal(10,2) default NULL');
define('_AB_CUSTOM_CHECKBOX',   'int(1) default NULL');
define('_AB_CUSTOM_DATE',       'int(11) default NULL');
define('_AB_CUSTOM_BLANKLINE',  'tinyint default NULL');
define('_AB_CUSTOM_HORIZ_RULE', 'smallint default NULL');

//Custom Tield tests
define('_AB_CUST_COLPREFIX',        'custom_');
define('_AB_CUST_TEST_STRING',      'varchar');

/**
 * Developer QA Contact information
 */
define('_AB_DEVQA_NAME',    'addressbook'."QualityControl");
define('_AB_DEVQA_EMAIL',    'addressbook'."@blacktower.com");

////////////////
//
// Class definitions
//
////////////////

class abUserException extends DefaultUserException
{

    var $errCollection = array();

    /**
     * Class definitions
     */
    function abUserException ($exc)
    {

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

    /**
     * Class definitions
     */
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