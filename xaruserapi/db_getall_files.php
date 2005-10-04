<?php

/**
 * Retrieve the metadata stored for all files in the database
 *
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 *
 * @returns array   All of the metadata stored for the particular file
 */

function filemanager_userapi_db_getall_files( /* VOID */ )
{

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // table and column definitions
    $fileEntry_table = $xartable['file_entry'];

    $sql = "SELECT xar_fileEntry_id,
                   xar_user_id,
                   xar_filename,
                   xar_location,
                   xar_filesize,
                   xar_status,
                   xar_store_type,
                   xar_mime_type,
                   xar_extrainfo
              FROM $fileEntry_table";

    $result = $dbconn->Execute($sql);

    if (!$result)  {
        return;
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return array();
    }

    $trustedDir = eregi_replace('/$', '', xarModGetVar('filemanager','path.imports-directory'));
    $untrustDir = eregi_replace('/$', '', xarModGetVar('filemanager','path.filemanager-directory'));

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        if (isset($file)) unset($file); // Make sure $file is unset

        $file['id']                 = $row['xar_fileentry_id'];
        $file['name']               = $row['xar_filename'];

        $file['size']['value']      = $row['xar_filesize'];
        $file['size']['text']       = xarModAPIFunc('filemanager', 'user', 'normalize_filesize', $row['xar_filesize']);

        $file['owner']['id']        = $row['xar_user_id'];
        $file['owner']['name']      = xarUserGetVar('name', $row['xar_user_id']);

        $file['status']['value']       = $row['xar_status'];
        switch($row['xar_status']) {
            case _FILEMANAGER_STATUS_REJECTED:
                $file['status']['text'] = xarML('Rejected');
                break;
            case _FILEMANAGER_STATUS_APPROVED:
                $file['status']['text'] = xarML('Approved');
                break;
            case _FILEMANAGER_STATUS_SUBMITTED:
                $file['status']['text'] = xarML('Submitted');
                break;
            default:
                $file['status']['text'] = xarML('Unknown!');
                break;
        }

        $file['storetype']['value'] = $row['xar_store_type'];
        $storeTypeText = 'Database File Entry';

        if (_FILEMANAGER_STORE_FILESYSTEM & $row['xar_store_type']) {
            if (!empty($storeTypeText)) {
                $storeTypeText .= ' / ';
            }
            $storeTypeText .= 'File System Store';
        } elseif (_FILEMANAGER_STORE_DB_DATA & $row['xar_store_type']) {
            if (!empty($storeTypeText)) {
                $storeTypeText = ' / ';
            }
            $storeTypeText .= 'Database Store';
        }
        $file['storetype']['text']  = $storeTypeText;
        $file['mimetype']              = xarModAPIFunc('mime', 'user', 'get_rev_mimetype', array('mimeType' => $row['xar_mime_type']));
        $file['mimetype']['text']      = $row['xar_mime_type'];
        $file['mimetype']['imagepath'] = xarModAPIFunc('mime', 'user', 'get_mime_image', array('mimeType' => $row['xar_mime_type']));
        $file['link']['url']  = xarModURL('filemanager', 'user', 'download', array('fileId' => $file['id']));
        $file['link']['label']     = xarML('Download file: #(1)', $file['name']);
        $file['link']['link']      = '<a href="'.$file['link']['url'].'" alt="'.$file['link']['label'].'">'
                                   . $file['name'] . '</a>';

        $file['location']['uri']   = $row['xar_location'];

        $pathInfo = parse_url($row['xar_location']);

        // Make sure we have a url scheme
        if (!isset($pathInfo['scheme']) || empty($pathInfo['scheme'])) {
            $pathInfo['scheme'] = 'file';
        }

        switch ($pathInfo['scheme']) {
            case 'trust':
                $file['location']['real']    = $trustedDir . $pathInfo['path'];
                $file['location']['virtual'] = 'trusted/' . $file['name'];
                break;
            case 'untrust':
                $file['location']['real']    = $untrustDir . $pathInfo['path'];
                $file['location']['virtual'] = 'untrust/' . $file['name'];
                break;
            case 'file':
                $file['location']['real']    = $pathInfo['path'];
                $file['location']['virtual'] = $pathInfo['path'];
                break;
        }

        /**
         *
         * DEPRECATED STRUCTURE - WILL NOT BE HERE IN THE 1.0 RELEASE OF UPLOADS
         *
         */
        $file['fileId']        = &$file['id'];
        $file['userId']        = &$file['owner']['id'];
        $file['userName']      = &$file['owner']['name'];
        $file['fileName']      = &$file['name'];
        $file['fileSize']      = &$file['size']['value'];
        $file['fileStatus']    = &$file['status']['value'];
        $file['fileType']      = &$file['mimetype']['text'];
        $file['fileTypeInfo']  = &$file['mimetype'];
        $file['storeType']     = &$file['storetype']['value'];
        $file['mimeImage']     = &$file['mimetype']['imagepath'];
        $file['fileDownload']  = &$file['link']['url'];
        $file['fileURL']       = &$file['link']['url'];
        $file['DownloadLabel'] = &$file['link']['lable'];
        $file['fileLocation']  = &$file['location']['real'];
        $file['fileDirectory'] = dirname($file['fileLocation']);
        $file['fileHash']      = basename($pathInfo['path']);
        $file['fileHashName']     = $file['fileDirectory'] . '/' . $file['fileHash'];
        $file['fileHashRealName'] = $file['fileDirectory'] . '/' . $file['fileName'];
        $file['fileStatusName'] = &$file['status']['text'];
        /**
         *
         * END OF DEPRECATED STRUCTURE
         *
         */

        if (!empty($row['xar_extrainfo'])) {
            $file['extrainfo'] = @unserialize($row['xar_extrainfo']);
        }

        $fileList[$file['id']] = $file;
        $result->MoveNext();
    }

    $result->Close();

    return $fileList;
}

?>
