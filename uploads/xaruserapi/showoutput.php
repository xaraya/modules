<?php
/**
 * show output for uploads module (used in DD properties)
 * 
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload' (future ?)
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

    $value = array_filter(explode(';', $value));

    if (is_array($value) && count($value)) {
        $data['Attachments'] = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $value));    
    } else {
        $data['Attachments'] = '';
    } 

// TODO: different formats ?
    return xarTplModule('uploads', 'user', 'attachment-list', $data, NULL);        
}

?>
