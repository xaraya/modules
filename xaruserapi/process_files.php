<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */
/**
 *  Processes incoming files (uploades / imports)
 *
 *  @author  Carl P. Corliss (aka Rabbitt)
 *  @access  public
 *  @param   string     importFrom  The complete path to a (local) directory to import files from
 *  @param   array      override    Array containing override values for import/uplaod path/obfuscate
 *  @param   string     override.upload.path        Override the upload path with the specified value
 *  @param   string     override.upload.obfuscate   Override the upload filename obfuscation
 *  @param   integer    action        The action that is happening ;-)
 *  @return array      list of files the files that were requested to be stored. If they had errors,
 *                      they will have 'error' index defined and will -not- have been added. otherwise,
 *                      they will have a fileId associated with them if they were added to the DB
 */

xarModAPILoad('uploads', 'user');

function uploads_userapi_process_files( $args )
{

    extract($args);

    $storeList = array();

    if (!isset($action)) {
        $msg = xarML("Missing parameter [#(1)] to API function [#(2)] in module [#(3)].", 'action', 'process_files', 'uploads');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // If not store type defined, default to DB ENTRY AND FILESYSTEM STORE
    if (!isset($storeType)) {
        // this is the same as _UPLOADS_STORE_DB_ENTRY OR'd with _UPLOADS_STORE_FILESYSTEM
        $storeType = _UPLOADS_STORE_FSDB;
    }

    // If there is an override['upload']['path'], try to use that
    if (!empty($override['upload']['path'])) {
        $upload_directory = $override['upload']['path'];
        if (!file_exists($upload_directory)) {
            // Note: the parent directory must already exist
            $result = @mkdir($upload_directory);
            if ($result) {
                // create dummy index.html in case it's web-accessible
                @touch($upload_directory . '/index.html');
            } else {
            // CHECKME: fall back to common uploads directory, or fail ?
                $upload_directory = xarModGetVar('uploads','path.uploads-directory');
            }
        }
    } else {
        $upload_directory = xarModGetVar('uploads','path.uploads-directory');
    }

    // Check for override of upload obfuscation and set accordingly
    if (isset($override['upload']['obfuscate']) && $override['upload']['obfuscate']) {
        $upload_obfuscate = TRUE;
    } else {
        $upload_obfuscate = FALSE;
    }

    switch ($action) {

        case _UPLOADS_GET_UPLOAD:
            if (!isset($upload) || empty($upload)) {
                $msg = xarML('Missing parameter [#(1)] to API function [#(2)] in module [#(3)].', 'upload', 'process_files', 'uploads');
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
            }

            $allow_duplicate = xarModGetVar('uploads', 'file.allow-duplicate-upload');
            if (empty($allow_duplicate)) {
                $allow_duplicate = 0;
            }

            if (isset($upload['name']) && !empty($upload['name'])) {
                // make sure we look in the right directory :-)
                if ($storeType & _UPLOADS_STORE_FILESYSTEM) {
                    $dirfilter = $upload_directory . '/%';
                } else {
                    $dirfilter = null;
                }
                // Note: we don't check on fileSize here (it wasn't taken into account before)
                $fileTest = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileName' => $upload['name'],
                                                                                  // make sure we look in the right directory :-)
                                                                                  'fileLocation' => $dirfilter));
                if (count($fileTest)) {
                    $file = end($fileTest);
                    // if we don't allow duplicates
                    if (empty($allow_duplicate)) {
                        // specify the error message
                        $file['errors'] = array();
                        $file['errors'][] = array('errorMesg' => xarML('Filename already exists'),
                                                  'errorId'   => _UPLOADS_ERROR_BAD_FORMAT);
                        // set the fileId to null for templates etc.
                        $file['fileId'] = null;
                        // add the existing file to the list and break off
                        $fileList[0] = $file;
                        break;

                    // if we want to replace duplicate files
                    } elseif ($allow_duplicate == 2) {
                        // pass original fileId and fileLocation to $upload,
                        // and do something special in prepare_uploads / file_store ?
                        $upload['fileId'] = $file['fileId'];
                        $upload['fileLocation'] = $file['fileLocation'];
                        $upload['isDuplicate'] = 2;

                    } else {
                        // new version for duplicate files - continue as usual
                        $upload['isDuplicate'] = 1;
                    }
                }
            }

            $fileList = xarModAPIFunc('uploads','user','prepare_uploads',
                                       array('savePath'  => $upload_directory,
                                             'obfuscate' => $upload_obfuscate,
                                             'fileInfo'  => $upload));
            break;
        case _UPLOADS_GET_LOCAL:

            $storeType = _UPLOADS_STORE_DB_ENTRY;

            if (isset($getAll) && !empty($getAll)) {
                // current working directory for the user, set by import_chdir() when using the get_files() GUI
                $cwd = xarModGetUserVar('uploads', 'path.imports-cwd');

                $fileList = xarModAPIFunc('uploads', 'user', 'import_get_filelist', array('fileLocation' => $cwd, 'descend' => TRUE));

            } else {
                $list = array();
                // file list coming from validatevalue() or the get_files() GUI
                foreach ($fileList as $location => $fileInfo) {
                    if ($fileInfo['inodeType'] == _INODE_TYPE_DIRECTORY) {
                        $list += xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                                array('fileLocation' => $location, 'descend' => TRUE));
                        unset($fileList[$location]);
                    }
                }

                $fileList += $list;

                // files in the trusted directory are automatically approved
                foreach ($fileList as $key => $fileInfo) {
                    $fileList[$key]['fileStatus'] = _UPLOADS_STATUS_APPROVED;
                }
                unset($list);
            }
            break;
        case _UPLOADS_GET_EXTERNAL:

            if (!isset($import)) {
                $msg = xarML('Missing parameter [#(1)] to API function [#(2)] in module [#(3)].', 'import', 'process_files', 'uploads');
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
            }

            // Setup the uri structure so we have defaults if parse_url() doesn't create them
            $uri = parse_url($import);

            if (!isset($uri['scheme']) || empty($uri['scheme'])) {
                $uri['scheme'] = xarML('unknown');
            }

            switch ($uri['scheme']) {
                case 'ftp':
                    $fileList = xarModAPIFunc('uploads', 'user', 'import_external_ftp',
                                              array('savePath'  => $upload_directory,
                                                    'obfuscate' => $upload_obfuscate,
                                                    'uri'       => $uri));
                    break;
                case 'https':
                case 'http':
                    $fileList = xarModAPIFunc('uploads', 'user', 'import_external_http',
                                              array('savePath'  => $upload_directory,
                                                    'obfuscate' => $upload_obfuscate,
                                                    'uri'       => $uri));
                    break;
                case 'file':
                    // If we'ere using the file scheme then just store a db entry only
                    // as there is really no sense in moving the file around
                    $storeType = _UPLOADS_STORE_DB_ENTRY;
                    $fileList = xarModAPIFunc('uploads', 'user', 'import_external_file',
                                              array('uri'       => $uri));
                    break;
                case 'gopher':
                case 'wais':
                case 'news':
                case 'nntp':
                case 'prospero':
                default:
                    // ERROR
                    $msg = xarML('Import via scheme \'#(1)\' is not currently supported', $uri['scheme']);
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NOT_SUPPORTED', new SystemException($msg));
                    return;
            }
            break;
        default:
            $msg = xarML("Invalid parameter [#(1)] to API function [#(2)] in module [#(3)].", 'action', 'process_files', 'uploads');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;

    }

    foreach ($fileList as $fileInfo) {

        // If the file has errors, add the file to the storeList (with it's errors intact),
        // and continue to the next file in the list. Note: it's up to the calling function
        // to deal with the error (or not) - however, we won't be adding the file with errors :-)
        if (isset($fileInfo['errors'])) {
            $storeList[] = $fileInfo;
            continue;
        }
        $storeList[] = xarModAPIFunc('uploads', 'user', 'file_store',
                                      array('fileInfo'  => $fileInfo,
                                            'storeType' => $storeType));
    }

    return $storeList;
}

?>
