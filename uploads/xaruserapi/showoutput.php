<?php
/**
 * show output for uploads module (used in DD properties)
 *
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload'
 * @returns string
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

    $data = array();

    // Check to see if an old value is present. Old values just file names
    // and do not start with a semicolon (our delimiter)
    if (xarModAPIFunc('uploads', 'adminapi', 'dd_value_needs_conversion', $value)) {
        $newValue = xarModAPIFunc('uplodas', 'adminapi', 'dd_convert_value', array('value' =>$value));

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
    if (!$this->multiple) {
        $value = array(current($value));
    }

    // make sure to remove any indices which are empty
    $value = array_filter($value);

/*
    // FIXME: Quick Fix - Forcing return of raw array of fileId's with their metadata for now
    // The stuff below has been commented out until time is available for a more permanent fix
    // Rabbitt :: March 29th, 2004

    if (is_array($value) && count($value)) {
        $data['Attachments'] = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $value));
    } else {
        $data['Attachments'] = '';
    }

    $data['format'] = $format;
    return xarTplModule('uploads', 'user', 'attachment-list', $data, NULL);
*/
    return xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $value));
}

?>
