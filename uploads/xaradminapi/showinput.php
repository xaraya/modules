<?php
/**
 * show input fields for uploads module (used in DD properties)
 *
 * @param  $args ['id'] string id of the upload field(s)
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload' (future ?)
 * @param  $args ['multiple'] boolean allow multiple uploads or not
 * @param  $args ['methods'] array of allowed methods 'trusted', 'external', 'stored' and/or 'upload'
 * @returns string
 * @return string containing the input fields
 */
function uploads_adminapi_showinput($args)
{
    extract($args);
    if (empty($id)) {
        $id = null;
    }
    if (empty($value)) {
        $value = null;
    }
    if (empty($multiple)) {
        $multiple = false;
    } else {
        $multiple = true;
    }
    if (empty($format)) {
        $format = 'fileupload';
    }
    if (empty($methods)) {
        $methods = null;
    }

    // Check to see if an old value is present. Old values just file names
    // and do not start with a semicolon (our delimiter)
    if (xarModAPIFunc('uploads', 'admin', 'dd_value_needs_conversion', $value)) {
        $newValue = xarModAPIFunc('uplodas', 'admin', 'dd_convert_value', array('value' =>$value));

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

    $data = array();

    xarModAPILoad('uploads','user');

    $trusted_dir = xarModGetVar('uploads', 'path.imports-directory');
    $descend = TRUE;

    $data['getAction']['LOCAL']       = _UPLOADS_GET_LOCAL;
    $data['getAction']['EXTERNAL']    = _UPLOADS_GET_EXTERNAL;
    $data['getAction']['UPLOAD']      = _UPLOADS_GET_UPLOAD;
    $data['getAction']['STORED']      = _UPLOADS_GET_STORED;
    $data['getAction']['REFRESH']     = _UPLOADS_GET_REFRESH_LOCAL;
    $data['id']                       = $id;
    $data['file_maxsize'] = xarModGetVar('uploads', 'file.maxsize');;
    $data['fileList']     = xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                           array('descend' => $descend, 'fileLocation' => $trusted_dir));
    $data['storedList']   = xarModAPIFunc('uploads', 'user', 'db_getall_files');

    // used to allow selection of multiple files
    $data['multiple_' . $id] = $multiple;

    if (isset($methods) && count($methods) > 0) {
        $data['methods'] = array(
            'trusted' => in_array('trusted',$methods) ? TRUE : FALSE,
            'external' => in_array('external',$methods) ? TRUE : FALSE,
            'upload' => in_array('upload',$methods) ? TRUE : FALSE,
            'stored' => in_array('stored',$methods) ? TRUE : FALSE,
        );
    } else {
        $data['methods'] = array(
            'trusted' => xarModGetVar('uploads', 'dd.fileupload.trusted') ? TRUE : FALSE,
            'external' => xarModGetVar('uploads', 'dd.fileupload.external') ? TRUE : FALSE,
            'upload' => xarModGetVar('uploads', 'dd.fileupload.upload') ? TRUE : FALSE,
            'stored' => xarModGetVar('uploads', 'dd.fileupload.stored') ? TRUE : FALSE,
        );
    }

    if (!empty($value)) {
        // We use array_filter to remove any values from
        // the array that are empty, null, or false
        $aList = array_filter(explode(';', $value));

        if (is_array($aList) && count($aList)) {
            $data['inodeType']['DIRECTORY']   = _INODE_TYPE_DIRECTORY;
            $data['inodeType']['FILE']        = _INODE_TYPE_FILE;
            $data['Attachments'] = xarModAPIFunc('uploads', 'user', 'db_get_file',
                                                  array('fileId' => $aList));
            $list = xarModAPIFunc('uploads','user','showoutput',
                                  array('value' => $value, 'style' => 'icon'));

            foreach ($aList as $fileId) {
                if (isset($data['storedList'][$fileId])) {
                    $data['storedList'][$fileId]['selected'] = TRUE;
                }
            }
        }
    }


// TODO: different formats ?
    return (isset($list) ? $list : '') . xarTplModule('uploads', 'user', 'attach_files', $data, NULL);
}

?>
