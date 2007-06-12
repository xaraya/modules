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
 * Retrieve the metadata stored for all files in the database
 *
 * @author Carl P. Corliss
 * @author Micheal Cortez
 * @access public
 *
 * @param  integer  numitems     (Optional) number of files to get
 * @param  integer  startnum     (Optional) starting file number
 * @param  string   sort         (Optional) sort order ('id','name','type','size','user','status','location',...)
 *
 * @return array   All of the metadata stored for all files
 */

function uploads_userapi_db_getall_files( $args )
{
    extract($args);

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
              FROM $fileEntry_table ";

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
            $sql .= ' WHERE ' . $categoriesdef['where'];
        }
    }

// FIXME: we need some indexes on xar_file_entry to make this more efficient
    if (empty($sort)) {
        $sort = '';
    }
    switch ($sort) {
        case 'name':
            $sql .= ' ORDER BY xar_filename';
            break;

        case 'size':
            $sql .= ' ORDER BY xar_filesize';
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

    // remove the '/' at the end of the path
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

    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);

        $fileInfo['fileId']        = $row['xar_fileEntry_id'];
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

    $result->Close();

    return $fileList;
}

?>
