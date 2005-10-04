<?php
/**
 * show input fields for filemanager module (used in DD properties)
 *
 * @param  $args ['id'] string id of the upload field(s)
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload' (future ?)
 * @param  $args ['multiple'] boolean allow multiple filemanager or not
 * @param  $args ['methods'] array of allowed methods 'trusted', 'external', 'stored' and/or 'upload'
 * @returns string
 * @return string containing the input fields
 */
function filemanager_adminapi_showinput($args)
{
    extract($args);

    // if we didn't get a value for $value, make it an empty string
    if (empty($value)) {
        $value = '';
    }

    if (isset($id)) {
        $prefix = $id . '_';
        $data['prefix'] = $prefix;
        $varName = 'files.selected.' . $prefix;
    } else {
        $prefix = '';
        $varName = 'files.selected';
    }

    $selectedFiles = array_filter(explode(';', $value),'trim');

    // prep a destination URL for the pop up    [1] => 236 window: file selector
    $data['destination_url']  = xarModURL('filemanager', 'user', 'file_selector', array('prefix' => $prefix));
    $data['totalAttachments'] = count($selectedFiles);
    $data['attachment_list']  = $value;

    // set the array of ID's so we have access to it on a different page: the file selector pop up window
    if (!xarModGetVar('filemanager', $varName)) {
        xarModSetVar('filemanager', $varName, serialize(array()));
    }
    xarModSetUserVar('filemanager', $varName, serialize($selectedFiles));
    
    // return the concatenated strign with all the info we prepped above.
    return xarTplModule('filemanager', 'user', 'showinput', $data, NULL);
}

?>
