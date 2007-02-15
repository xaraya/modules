<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
function uploads_adminapi_dd_value_needs_conversion($value)
{
    // if the value is empty or it has a value starting with ';'
    // Then it doesn't need to be converted - so return false.
    if (empty($value) || (strlen($value) && ';' == $value{0})) {
        // conversion not needed
        return FALSE;
    } else {
        // conversion needed
        return TRUE;
    }
}

?>