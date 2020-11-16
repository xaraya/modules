<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

function uploads_adminapi_dd_value_needs_conversion($value)
{
    // if the value is empty or it has a value starting with ';'
    // Then it doesn't need to be converted - so return false.
    if (empty($value) || (strlen($value) && ';' == $value{0})) {
        // conversion not needed
        return false;
    } else {
        // conversion needed
        return true;
    }
}
