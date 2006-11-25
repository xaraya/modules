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
 *  Retrieve the metadata stored for a particular file based on either
 *  the file id or the file name.
 *
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 * @param  mixed    fileId       (Optional) grab file(s) with the specified file id(s)
 * @param  string   fileName     (Optional) grab file(s) with the specified file name
 * @param  integer  fileType     (Optional) grab files with the specified mime type
 * @param  integer  fileStatus   (Optional) grab files with a specified status  (SUBMITTED, APPROVED, REJECTED)
 * @param  string   fileLocation (Optional) grab file(s) with the specified file location
 * @param  string   fileHash     (Optional) grab file(s) with the specified file hash
 * @param  integer  userId       (Optional) grab files uploaded by a particular user
 * @param  integer  store_type   (Optional) grab files with the specified store type (FILESYSTEM, DATABASE)
 * @param  boolean  inverse      (Optional) inverse the selection
 * @param  integer  numitems     (Optional) number of files to get
 * @param  integer  startnum     (Optional) starting file number
 * @param  string   sort         (Optional) sort order ('id','name','type','size','user','status','location',...)
 * @param  string   catid        (Optional) grab file(s) in the specified categories
 * @param  mixed    getnext      (Optional) grab the next file after this one (file id or file name)
 * @param  mixed    getprev      (Optional) grab the previous file before this one (file id or file name)
 *
 * @return array   All of the metadata stored for the particular file(s)
 * @throws BAD_PARAM
 */

function uploads_userapi_db_get_file( $args )
{
    extract($args);

    if (!isset($fileId) && !isset($fileName) && !isset($fileStatus) && !isset($fileLocation) &&
        !isset($userId)  && !isset($fileType) && !isset($store_type) && !isset($fileHash) &&
        !isset($fileLocationMD5) && empty($getnext) && empty($getprev)) {
        $msg = xarML('Missing parameters for function [#(1)] in module [#(2)]', 'db_get_file', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }

    $where = array();

    if (!isset($inverse)) {
        $inverse = FALSE;
    }

    if (isset($fileId)) {
        if (is_array($fileId)) {
            $where[] = 'xar_fileEntry_id IN (' . implode(',', $fileId) . ')';
        } elseif (!empty($fileId)) {
            $where[] = "xar_fileEntry_id = $fileId";
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
        $where[] = "(xar_filename LIKE '$fileName')";
    }

    if (isset($fileStatus) && !empty($fileStatus)) {
        $where[] = "(xar_status = $fileStatus)";
    }

    if (isset($fileSize) && !empty($fileSize)) {
        $where[] = "(xar_filesize = $fileSize)";
    }

    if (isset($userId) && !empty($userId)) {
        $where[] = "(xar_user_id = $userId)";
    }

    if (isset($store_type) && !empty($store_type)) {
        $where[] = "(xar_store_type = $store_type)";
    }

    if (isset($fileType) && !empty($fileType)) {
        $where[] = "(xar_mime_type LIKE '$fileType')";
    }

    if (isset($fileLocation) && !empty($fileLocation)) {
        if (strpos($fileLocation,'%') === FALSE) {
            $where[] = '(xar_location = ' . $dbconn->qstr($fileLocation) . ')';
        } else {
            $where[] = '(xar_location LIKE ' . $dbconn->qstr($fileLocation) . ')';
        }
    }

    // Note: the fileHash is the last part of the location
    if (isset($fileHash) && !empty($fileHash)) {
        $where[] = '(xar_location LIKE ' . $dbconn->qstr("%/$fileHash") . ')';
    }

    // Note: the MD5 hash of the file location is used by derivatives in the images module
    if (isset($fileLocationMD5) && !empty($fileLocationMD5)) {
        if ($dbconn->databaseType == 'sqlite') {
        // CHECKME: verify this syntax for SQLite !
            $where[] = "(php('md5',xar_location) = " . $dbconn->qstr($fileLocationMD5) . ')';
        } else {
            $where[] = '(md5(xar_location) = ' . $dbconn->qstr($fileLocationMD5) . ')';
        }
    }

    if (!empty($getnext)) {
        $startnum = 1;
        $numitems = 1;
        if (is_numeric($getnext)) {
            // sort by file id
            $where[] = '(xar_fileEntry_id > ' . $dbconn->qstr($getnext) . ')';
            $sort = 'id_asc';
        } else {
            // sort by file name
            $where[] = '(xar_filename > ' . $dbconn->qstr($getnext) . ')';
            $sort = 'name_asc';
        }
    }

    if (!empty($getprev)) {
        $startnum = 1;
        $numitems = 1;
        if (is_numeric($getprev)) {
            // sort by file id
            $where[] = '(xar_fileEntry_id < ' . $dbconn->qstr($getprev) . ')';
            $sort = 'id_desc';
        } else {
            // sort by file name
            $where[] = '(xar_filename < ' . $dbconn->qstr($getprev) . ')';
            $sort = 'name_desc';
        }
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
              FROM $fileEntry_table ";
    // Put the category id to work
    if (!empty($catid) && xarModIsAvailable('categories') && xarModIsHooked('categories','uploads',1)) {
        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                      array('modid' => xarModGetIDFromName('uploads'),
                                            'itemtype' => 1,
                                            'catid' => $catid));
        if (empty($categoriesdef)) return;

        // Add LEFT JOIN ... ON ... from categories_linkage
        $sql .= ' LEFT JOIN ' . $categoriesdef['table'];
        $sql .= ' ON ' . $categoriesdef['field'] . ' = ' . 'xar_fileEntry_id';
        if (!empty($categoriesdef['more'])) {
            // More LEFT JOIN ... ON ... from categories (when selecting by category)
            $sql .= $categoriesdef['more'];
        }
        if (!empty($categoriesdef['where'])) {
            $where .= ' AND ' . $categoriesdef['where'];
        }
    }

    $sql .= " WHERE $where";

// FIXME: we need some indexes on xar_file_entry to make this more efficient
    if (empty($sort)) {
        $sort = '';
    }
    switch ($sort) {
        case 'name':
        case 'name_asc':
            $sql .= ' ORDER BY xar_filename';
            break;

        case 'name_desc':
            $sql .= ' ORDER BY xar_filename DESC';
            break;

        case 'size':
            $sql .= ' ORDER BY xar_filesize DESC';
            break;

        case 'type':
            $sql .= ' ORDER BY xar_mime_type';
            break;

        case 'status':
            $sql .= ' ORDER BY xar_status';
            break;

        case 'location':
            $sql .= ' ORDER BY xar_location';
            break;

        case 'user':
            $sql .= ' ORDER BY xar_user_id';
            break;

        case 'store':
            $sql .= ' ORDER BY xar_store_type';
            break;

        case 'id':
        case 'id_desc':
            $sql .= ' ORDER BY xar_fileEntry_id DESC';
            break;

        case 'id_asc':
        default:
            $sql .= ' ORDER BY xar_fileEntry_id';
            break;
    }

    if (!empty($numitems) && is_numeric($numitems)) {
        if (empty($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($sql);
    }

    if (!$result)  {
        return;
    }

    // if no record found, return an empty array
    if ($result->EOF) {
        return array();
    }

    $importDir = xarModGetVar('uploads','path.imports-directory');
    $uploadDir = xarModGetVar('uploads','path.uploads-directory');

    // remove the '/' from the path
    $importDir = eregi_replace('/$', '', $importDir);
    $uploadDir = eregi_replace('/$', '', $uploadDir);

    if(xarServerGetVar('PATH_TRANSLATED')) {
        $base_directory = dirname(realpath(xarServerGetVar('PATH_TRANSLATED')));
    } elseif(xarServerGetVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }

    $revcache = array();
    $imgcache = array();
    $usercache = array();

    $fileList = array();
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $fileInfo['fileId']        = $row['xar_fileentry_id'];
        $fileInfo['userId']        = $row['xar_user_id'];
        if (!isset($usercache[$fileInfo['userId']])) {
            $usercache[$fileInfo['userId']] = xarUserGetVar('name',$fileInfo['userId']);
        }
        $fileInfo['userName']      = $usercache[$fileInfo['userId']];
        $fileInfo['fileName']      = $row['xar_filename'];
        $fileInfo['fileLocation']  = $row['xar_location'];
        $fileInfo['fileSize']      = $row['xar_filesize'];
        $fileInfo['fileStatus']    = $row['xar_status'];
        $fileInfo['fileType']      = $row['xar_mime_type'];
        if (!isset($revcache[$fileInfo['fileType']])) {
            $revcache[$fileInfo['fileType']] = xarModAPIFunc('mime', 'user', 'get_rev_mimetype', array('mimeType' => $fileInfo['fileType']));
        }
        $fileInfo['fileTypeInfo']  = $revcache[$fileInfo['fileType']];
        $fileInfo['storeType']     = $row['xar_store_type'];
        if (!isset($imgcache[$fileInfo['fileType']])) {
            $imgcache[$fileInfo['fileType']] = xarModAPIFunc('mime', 'user', 'get_mime_image', array('mimeType' => $fileInfo['fileType']));
        }
        $fileInfo['mimeImage']     = $imgcache[$fileInfo['fileType']];
        $fileInfo['fileDownload']  = xarModURL('uploads', 'user', 'download', array('fileId' => $fileInfo['fileId']));
        $fileInfo['fileURL']       = $fileInfo['fileDownload'];
        $fileInfo['DownloadLabel'] = xarML('Download file: #(1)', $fileInfo['fileName']);
        if (!empty($fileInfo['fileLocation']) && file_exists($fileInfo['fileLocation'])) {
            $fileInfo['fileModified'] = @filemtime($fileInfo['fileLocation']);
        }

        if (stristr($fileInfo['fileLocation'], $importDir)) {
            $fileInfo['fileDirectory'] = dirname(str_replace($importDir, 'imports', $fileInfo['fileLocation']));
            $fileInfo['fileHash']  = basename($fileInfo['fileLocation']);
        } elseif (stristr($fileInfo['fileLocation'], $uploadDir)) {
            $fileInfo['fileDirectory'] = dirname(str_replace($uploadDir, 'uploads', $fileInfo['fileLocation']));
            $fileInfo['fileHash']  = basename($fileInfo['fileLocation']);
        } else {
            $fileInfo['fileDirectory'] = dirname($fileInfo['fileLocation']);
            $fileInfo['fileHash']  = basename($fileInfo['fileLocation']);
        }

        $fileInfo['fileHashName']     = $fileInfo['fileDirectory'] . '/' . $fileInfo['fileHash'];
        $fileInfo['fileHashRealName'] = $fileInfo['fileDirectory'] . '/' . $fileInfo['fileName'];

        switch($fileInfo['fileStatus']) {
            case _UPLOADS_STATUS_REJECTED:
                $fileInfo['fileStatusName'] = xarML('Rejected');
                break;
            case _UPLOADS_STATUS_APPROVED:
                $fileInfo['fileStatusName'] = xarML('Approved');
                break;
            case _UPLOADS_STATUS_SUBMITTED:
                $fileInfo['fileStatusName'] = xarML('Submitted');
                break;
            default:
                $fileInfo['fileStatusName'] = xarML('Unknown!');
                break;
        }

        if (!empty($row['xar_extrainfo'])) {
            $fileInfo['extrainfo'] = @unserialize($row['xar_extrainfo']);
        }

        $instance = array();
        $instance[0] = $fileInfo['fileTypeInfo']['typeId'];
        $instance[1] = $fileInfo['fileTypeInfo']['subtypeId'];
        $instance[2] = xarSessionGetVar('uid');
        $instance[3] = $fileInfo['fileId'];

        $instance = implode(':', $instance);

        if ($fileInfo['fileStatus'] == _UPLOADS_STATUS_APPROVED ||
            xarSecurityCheck('EditUploads', 0, 'File', $instance)) {
            $fileList[$fileInfo['fileId']] = $fileInfo;
        }
        $result->MoveNext();
    }

    return $fileList;
}

?>
