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