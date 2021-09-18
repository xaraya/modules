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

/**
 * show output for uploads module (used in DD properties)
 *
 * @param string $value The current value(s)
 * @param string $format Format specifying 'fileupload', 'textupload' or 'upload'
 * @return string containing the uploads output
 */
function uploads_userapi_showoutput($args)
{
    extract($args);
    if (empty($value)) {
        $value = null;
    }
    if (empty($format)) {
        $format = 'fileupload';
    }
    if (empty($multiple)) {
        $multiple = false;
    }

    $data = [];

    // Check to see if an old value is present. Old values just file names
    // and do not start with a semicolon (our delimiter)
    if (xarMod::apiFunc('uploads', 'admin', 'dd_value_needs_conversion', $value)) {
        $newValue = xarMod::apiFunc('uploads', 'admin', 'dd_convert_value', ['value' =>$value]);

        // if we were unable to convert the value, then go ahead and and return
        // an empty string instead of processing the value and bombing out
        if ($newValue == $value) {
            $value = null;
            unset($newValue);
        } else {
            $value = $newValue;
            unset($newValue);
        }
    }

    // The explode will create an empty indice,
    // so we get rid of it with array_filter :-)
    $value = array_filter(explode(';', $value));
    if (!$multiple) {
        $value = [current($value)];
    }

    // make sure to remove any indices which are empty
    $value = array_filter($value);

    if (empty($value)) {
        return [];
    }


    // FIXME: Quick Fix - Forcing return of raw array of fileId's with their metadata for now
    // Rabbitt :: March 29th, 2004

    if (isset($style) && $style = 'icon') {
        if (is_array($value) && count($value)) {
            $data['Attachments'] = xarMod::apiFunc('uploads', 'user', 'db_get_file', ['fileId' => $value]);
        } else {
            $data['Attachments'] = '';
        }

        $data['format'] = $format;
        return xarTpl::module('uploads', 'user', 'attachment-list', $data, null);
    } else {
        // return a raw array for now
        return xarMod::apiFunc('uploads', 'user', 'db_get_file', ['fileId' => $value]);
    }
}
