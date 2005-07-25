<?php

/**
 *  Retrieve the metadata stored for a particular file based on either
 *  the file id or the file name.
 *
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 * @param  integer  fileId      (Optional) grab file with the specified file id
 * @param  string   fileName    (Optional) grab file(s) with the specified file name
 * @param  integer  fileStatus  (Optional) grab files with a specified status  (SUBMITTED, APPROVED, REJECTED)
 * @param  integer  userId      (Optional) grab files uploaded by a particular user
 * @param  integer  store_type  (Optional) grab files with the specified store type (FILESYSTEM, DATABASE)
 * @param  integer  fileType    (Optional) grab files with the specified mime type
 *
 * @return mixed    An array of files -> metadata, FALSE or null on error
 */

function uploads_userapi_db_get_file( $args )
{

    $files = xarModAPIFunc('uploads', 'user', 'db_get_file_entry', $args);

    if (is_null($files) || $files === FALSE || !is_array($files)) {
        return;
    } else {
        foreach ($files as $fileId => $file) {
            /**
            *
            * DEPRECATED STRUCTURE - WILL NOT BE HERE IN THE 1.0 RELEASE OF UPLOADS
            *
            */
            $files[$fileId]['fileId']           = &$files[$fileId]['id'];
            $files[$fileId]['userId']           = &$files[$fileId]['owner']['id'];
            $files[$fileId]['userName']         = &$files[$fileId]['owner']['name'];
            $files[$fileId]['fileName']         = &$files[$fileId]['name'];
            $files[$fileId]['fileSize']         = &$files[$fileId]['size']['value'];
            $files[$fileId]['fileStatus']       = &$files[$fileId]['status']['value'];
            $files[$fileId]['fileType']         = &$files[$fileId]['mimetype']['text'];
            $files[$fileId]['fileTypeInfo']     = &$files[$fileId]['mimetype'];
            $files[$fileId]['storeType']        = &$files[$fileId]['storetype']['value'];
            $files[$fileId]['mimeImage']        = &$files[$fileId]['mimetype']['imagepath'];
            $files[$fileId]['fileDownload']     = &$files[$fileId]['link']['url'];
            $files[$fileId]['fileURL']          = &$files[$fileId]['link']['url'];
            $files[$fileId]['DownloadLabel']    = &$files[$fileId]['link']['lable'];
            $files[$fileId]['fileLocation']     = &$files[$fileId]['location']['real'];
            $files[$fileId]['fileDirectory']    = dirname($files[$fileId]['fileLocation']);
            $files[$fileId]['fileHash']         = basename($files[$fileId]['location']['real']);
            $files[$fileId]['fileHashName']     = $files[$fileId]['fileDirectory'] . '/' . $files[$fileId]['fileHash'];
            $files[$fileId]['fileHashRealName'] = $files[$fileId]['fileDirectory'] . '/' . $files[$fileId]['fileName'];
            $files[$fileId]['fileStatusName']   = &$files[$fileId]['status']['text'];

            // Note: extrainfo remains as is
        }
    }

    return $files;
}

?>
