<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
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

    $data = array();

    // Check to see if an old value is present. Old values just file names
    // and do not start with a semicolon (our delimiter)
    if (xarModAPIFunc('uploads', 'admin', 'dd_value_needs_conversion', $value)) {
        $newValue = xarModAPIFunc('uploads', 'admin', 'dd_convert_value', array('value' =>$value));

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
        $value = array(current($value));
    }

    // make sure to remove any indices which are empty
    $value = array_filter($value);

    if (empty($value)) {
        return array();
    }


    // FIXME: Quick Fix - Forcing return of raw array of fileId's with their metadata for now
    // Rabbitt :: March 29th, 2004

    if (isset($style) && $style = 'icon') {
        if (is_array($value) && count($value)) {
            $data['Attachments'] = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $value));
        } else {
            $data['Attachments'] = '';
        }

        $data['format'] = $format;
        return xarTplModule('uploads', 'user', 'attachment-list', $data, NULL);
    } else {
        // return a raw array for now
        return xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $value));
    }
}

?>
