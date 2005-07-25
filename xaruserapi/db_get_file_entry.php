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
 * @returns array   All of the metadata stored for the particular file
 */

function uploads_userapi_db_get_file_entry( $args )
{
    extract($args);

    if (!isset($fileId) && !isset($fileName) && !isset($fileStatus) && !isset($fileLocation) &&
        !isset($userId)  && !isset($fileType) && !isset($store_type) && !isset($fileHash)) {
        $msg = xarML('Missing parameters for function [#(1)] in module [#(2)]', 'db_get_file', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $where    = array();
    $bindvars = array();

    if (!isset($inverse)) {
        $inverse = FALSE;
    }

    $fileCache = xarVarGetCached('uploads', 'file.list');

    if (!isset($fileCache) || empty($fileCache)) {
        $fileCache = array();
    }

    if (isset($fileId)) {
        if (is_array($fileId) && count($fileId)) {

            $list = array();

            foreach ($fileId as $id) {
                if (in_array($id, array_keys($fileCache))) {
                    $fileList[$id] = $fileCache[$id];
                } else {
                    $list[]     = '?';
                    $bindvars[] = (int) $id;
                }
            }

            if (empty($list)) {
                return $fileList;
            } else {
                $where[] = 'xar_fileEntry_id IN (' . implode(',', $list) . ')';
            }
        } elseif (!empty($fileId)) {

            if (in_array($fileId, array_keys($fileCache))) {
                $fileList[$fileId] = $fileCache[$fileId];
                return $fileList;
            } else {
                $where[] = "xar_fileEntry_id = ?";
                $bindvars[] = (int) $fileId;
            }
        } else {
            // fileId == 0 so return an empty array.
            return array();
        }
    }


    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

        // table and column definitions
    $fileEntry_table = $xartable['file_entry'];

    if (isset($fileName) && !empty($fileName)) {
        $where[] = "(xar_filename LIKE ?)";
        $bindvars[] = (string) $fileName;
    }

    if (isset($fileStatus) && !empty($fileStatus)) {
        $where[] = "(xar_status = ?)";
        $bindvars[] = (int) $fileStatus;
    }

    if (isset($userId) && !empty($userId)) {
        $where[] = "(xar_user_id = ?)";
        $bindvars[] = (int) $userId;
    }

    if (isset($store_type) && !empty($store_type)) {
        $where[] = "(xar_store_type = ?)";
        $bindvars[] = (int) $store_type;
    }

    if (isset($fileType) && !empty($fileType)) {
        $where[] = "(xar_mime_type LIKE ?)";
        $bindvars[] = (string) $fileType;
    }

    if (isset($fileLocation) && !empty($fileLocation)) {
        $where[] = "(xar_location LIKE ?)";
        $bindvars[] = (string) $fileLocation;
    }

    if (isset($fileHash) && !empty($fileHash)) {
        $where[] = '(xar_location LIKE ' . $dbconn->qstr("%/$fileHash") . ')';
    #    $bindvars[] = (int) $fileHash;
    }

    if (count($where) > 1) {
        if ($inverse)  {
            $where = implode(' OR ', $where);
        } else {
            $where = implode(' AND ', $where);
        }
    } else {
        $where = implode('', $where);
    }

    if ($inverse) {
        $where = "NOT ($where)";
    }

    $sql = "SELECT xar_fileEntry_id,
                   xar_user_id,
                   xar_filename,
                   xar_location,
                   xar_filesize,
                   xar_status,
                   xar_store_type,
                   xar_mime_type,
                   xar_extrainfo
              FROM $fileEntry_table
             WHERE $where";

    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result)  {
        return;
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return array();
    }

    $trustedDir = eregi_replace('/$', '', xarModGetVar('uploads','path.trusted'));
    $untrustDir = eregi_replace('/$', '', xarModGetVar('uploads','path.untrust'));

    $mountlist = @unserialize(xarModGetVar('uploads', 'mount.list'));
    if (!is_array($mountlist)) {
        $mountlist = array();
    }

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        if (isset($file))  {
            unset($file); // Make sure file is unset
        }

        $file['id']                 = $row['xar_fileentry_id'];
        $file['name']               = $row['xar_filename'];
        $file['size']['value']      = $row['xar_filesize'];
        $file['size']['text']       = uploads_userapi__normalize_filesize($row['xar_filesize']);
        $file['owner']['id']        = $row['xar_user_id'];
        $file['owner']['name']      = xarUserGetVar('name', $row['xar_user_id']);
        $file['status']['value']       = $row['xar_status'];
        switch($row['xar_status']) {
            case _UPLOADS_STATUS_REJECTED:
                $file['status']['text'] = xarML('Rejected');
                break;
            case _UPLOADS_STATUS_APPROVED:
                $file['status']['text'] = xarML('Approved');
                break;
            case _UPLOADS_STATUS_SUBMITTED:
                $file['status']['text'] = xarML('Submitted');
                break;
            default:
                $file['status']['text'] = xarML('Unknown!');
                break;
        }

        $file['storetype']['value'] = $row['xar_store_type'];
        $storeTypeText = 'Database File Entry';

        if (_UPLOADS_STORE_FILESYSTEM & $row['xar_store_type']) {
            if (!empty($storeTypeText)) {
                $storeTypeText .= ' / ';
            }
            $storeTypeText .= 'File System Store';
        } elseif (_UPLOADS_STORE_DB_DATA & $row['xar_store_type']) {
            if (!empty($storeTypeText)) {
                $storeTypeText = ' / ';
            }
            $storeTypeText .= 'Database Store';
        }

        $file['storetype']['text']     = $storeTypeText;
        $file['mimetype']              = xarModAPIFunc('mime', 'user', 'get_rev_mimetype', array('mimeType' => $row['xar_mime_type']));
        $file['mimetype']['text']      = $row['xar_mime_type'];
        $file['mimetype']['imagepath'] = xarModAPIFunc('mime', 'user', 'get_mime_image', array('mimeType' => $row['xar_mime_type']));
        $file['link']['url']           = xarModURL('uploads', 'user', 'download', array('fileId' => $file['id']));
        $file['link']['label']         = xarML('Download file: #(1)', $file['name']);
        $file['link']['link']          = '<a href="'.$file['link']['url'].'" alt="'.$file['link']['label'].'">' . $file['name'] . '</a>';
        $file['location']['uri']       = $row['xar_location'];

        $testLocation = $row['xar_location'];
        
        if (eregi('^conflict:\/\/([0-9]+)\/(xarfs|mount|xardb)(.*)', $testLocation, $matches)) {
            xarDerefData('$matches', $matches);
            $_dirId  = (isset($matches[1]) ? $matches[1] : 'unknown');
            $_schema = (isset($matches[2]) ? $matches[2] : 'unknown');
            $_path   = (isset($matches[3]) ? $matches[3] : '');
            
            if (!empty($_path)) {
                $_path = (($_path{0} == '/') ? substr($_path, 1) : $_path);
            }
            
            $location = "$_schema://$_dirId/$_path";
        } else {
            $location = $testLocation;
        }
        $pathInfo = parse_url($location);

        // Make sure we have a url scheme
        if (!isset($pathInfo['scheme']) || empty($pathInfo['scheme'])) {
            $pathInfo['scheme'] = 'file';
        }

        switch ($pathInfo['scheme']) {
            case 'mount':
                if (in_array($pathInfo['host'], array_keys($mountlist))) {
                    $mountInfo = $mountlist[$pathInfo['host']];
                    $vpath = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $pathInfo['host']));
                    $path = (($pathInfo['path']{0} == '/') ? substr($pathInfo['path'], 1) : $pathInfo['path']);

                    $file['location']['real']    = $mountInfo['path'] . '/' . $path;
                    $file['location']['virtual'] =  $vpath . '/'. $path;
                } else {
                    $file['location']['real']    = 'unknown';
                    $file['location']['virtual'] = '/mount/'.$pathInfo['host'] . '/unknown';
                }
                break;
            case 'xarfs':
                if (!xarModAPIFunc('uploads', 'fs', 'in_trash', array('fileId' => $file['id']))) {
                    $dirId = xarModAPIFunc('uploads', 'vdir', 'get_file_location', 
                        array('fileId' => $file['id'], 'asPath' => FALSE));

                    if ($dirId) {
                        $vpath = xarModAPIFunc('uploads', 'vdir', 'path_encode', array('vdir_id' => $dirId));
                    } else {
                        $vpath = '/' . xarML('corrupted location');
                    }
                } else {
                    $vpath = '/rootfs/' . xarML('Recycle Bin');
                }
                $file['location']['real']      = $untrustDir . $pathInfo['path'];
                $file['location']['virtual']   = $vpath . '/' . $file['name'];
                $file['location']['directory'] = $vpath;

                break;
            case 'file':
                $file['location']['real']    = $pathInfo['path'];
                $file['location']['virtual'] = $pathInfo['path'];
                break;
        }

        $file['time']['modified'] = @filemtime($file['location']['real']);
        $file['time']['created']  = @filectime($file['location']['real']);


        if (empty($file['time']['created'])) {
            $file['time']['created'] = 0;
        }

        if (empty($file['time']['modified'])) {
            $file['time']['modified'] = 0;
        }

        if (!empty($row['xar_extrainfo'])) {
            $file['extrainfo'] = @unserialize($row['xar_extrainfo']);
        }

        $fileList[$file['id']] = $file;
        $result->MoveNext();
    }

    $result->Close();
    xarVarSetCached('uploads', 'file.list', $fileList);

    return $fileList;
}


function uploads_userapi__normalize_filesize( $args )
{

    if (is_array($args)) {
        extract($args);
    } elseif (is_numeric($args)) {
        $fileSize =& $args;
    } else {
        return array('long' => 0, 'short' => 0);
    }

    $size = $fileSize;

    $range = array('', 'KB', 'MB', 'GB');

    for ($i = 0; $size >= 1024 && $i < count($range); $i++) {
        $size /= 1024;
    }

    if (0 == $fileSize) {
        $short = '0.00 KB';
    } else {
        $short = number_format(round($size, 2), 2) . ' ' . $range[$i];
    }

    return array('long' => number_format($fileSize), 'short' => $short);
}
?>
